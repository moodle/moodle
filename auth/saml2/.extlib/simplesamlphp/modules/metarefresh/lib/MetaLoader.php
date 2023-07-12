<?php

namespace SimpleSAML\Module\metarefresh;

use Exception;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use SAML2\DOMDocumentFactory;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Metadata;
use SimpleSAML\Utils;

/**
 * @package SimpleSAMLphp
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 */
class MetaLoader
{
    /** @var int|null */
    private $expire;

    /** @var array */
    private $metadata = [];

    /** @var object|null */
    private $oldMetadataSrc;

    /** @var string|null */
    private $stateFile;

    /** @var bool*/
    private $changed = false;

    /** @var array */
    private $state = [];

    /** @var array */
    private $types = [
        'saml20-idp-remote',
        'saml20-sp-remote',
        'shib13-idp-remote',
        'shib13-sp-remote',
        'attributeauthority-remote'
    ];


    /**
     * Constructor
     *
     * @param int|null $expire
     * @param string|null  $stateFile
     * @param object|null  $oldMetadataSrc
     */
    public function __construct($expire = null, $stateFile = null, $oldMetadataSrc = null)
    {
        $this->expire = $expire;
        $this->oldMetadataSrc = $oldMetadataSrc;
        $this->stateFile = $stateFile;

        // Read file containing $state from disk
        if (!is_null($stateFile) && is_readable($stateFile)) {
            include($stateFile);
        }

        if (isset($state)) {
            $this->state = $state;
        }
    }


    /**
     * Get the types of entities that will be loaded.
     *
     * @return array The entity types allowed.
     */
    public function getTypes()
    {
        return $this->types;
    }


    /**
     * Set the types of entities that will be loaded.
     *
     * @param string|array $types Either a string with the name of one single type allowed, or an array with a list of
     * types. Pass an empty array to reset to all types of entities.
     * @return void
     */
    public function setTypes($types)
    {
        if (!is_array($types)) {
            $types = [$types];
        }
        $this->types = $types;
    }


    /**
     * This function processes a SAML metadata file.
     *
     * @param $source array
     * @return void
     */
    public function loadSource(array $source)
    {
        if (preg_match('@^https?://@i', $source['src'])) {
            // Build new HTTP context
            $context = $this->createContext($source);

            // GET!
            try {
                /** @var array $response  We know this because we set the third parameter to `true` */
                $response = Utils\HTTP::fetch($source['src'], $context, true);
                list($data, $responseHeaders) = $response;
            } catch (Exception $e) {
                Logger::warning('metarefresh: ' . $e->getMessage());
            }

            // We have response headers, so the request succeeded
            if (!isset($responseHeaders)) {
                // No response headers, this means the request failed in some way, so re-use old data
                Logger::debug('No response from '.$source['src'].' - attempting to re-use cached metadata');
                $this->addCachedMetadata($source);
                return;
            } elseif (preg_match('@^HTTP/1\.[01]\s304\s@', $responseHeaders[0])) {
                // 304 response
                Logger::debug('Received HTTP 304 (Not Modified) - attempting to re-use cached metadata');
                $this->addCachedMetadata($source);
                return;
            } elseif (!preg_match('@^HTTP/1\.[01]\s200\s@', $responseHeaders[0])) {
                // Other error
                Logger::debug('Error from '.$source['src'].' - attempting to re-use cached metadata');
                $this->addCachedMetadata($source);
                return;
            }
        } else {
            // Local file.
            $data = file_get_contents($source['src']);
            $responseHeaders = null;
        }

        // Everything OK. Proceed.
        if (isset($source['conditionalGET']) && $source['conditionalGET']) {
            // Stale or no metadata, so a fresh copy
            Logger::debug('Downloaded fresh copy');
        }

        try {
            $entities = $this->loadXML($data, $source);
        } catch (Exception $e) {
            Logger::debug('XML parser error when parsing ' . $source['src'] . ' - attempting to re-use cached metadata');
            Logger::debug('XML parser returned: ' . $e->getMessage());
            $this->addCachedMetadata($source);
            return;
        }

        foreach ($entities as $entity) {
            if (isset($source['blacklist'])) {
                if (!empty($source['blacklist']) && in_array($entity->getEntityId(), $source['blacklist'], true)) {
                    Logger::info('Skipping "'.$entity->getEntityId().'" - blacklisted.'."\n");
                    continue;
                }
            }

            if (isset($source['whitelist'])) {
                if (!empty($source['whitelist']) && !in_array($entity->getEntityId(), $source['whitelist'], true)) {
                    Logger::info('Skipping "'.$entity->getEntityId().'" - not in the whitelist.'."\n");
                    continue;
                }
            }

            /* Do we have an attribute whitelist? */
            if (isset($source['attributewhitelist']) && !empty($source['attributewhitelist'])) {
                $idpMetadata = $entity->getMetadata20IdP();
                if (!isset($idpMetadata)) {
                    /* Skip non-IdPs */
                    continue;
                }

                /* Do a recursive comparison for each whitelist of the attributewhitelist with the idpMetadata for this
                 * IdP. At least one of these whitelists should match */
                $match = false;
                foreach ($source['attributewhitelist'] as $whitelist) {
                    if ($this->containsArray($whitelist, $idpMetadata)) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    /* No match found -> next IdP */
                    continue;
                }
                Logger::debug('Whitelisted entityID: ' . $entity->getEntityID());
            }

            if (array_key_exists('certificates', $source) && ($source['certificates'] !== null)) {
                if (!$entity->validateSignature($source['certificates'])) {
                    Logger::info(
                        'Skipping "'.$entity->getEntityId().'" - could not verify signature using certificate.'."\n"
                    );
                    continue;
                }
            }

            if (array_key_exists('validateFingerprint', $source) && $source['validateFingerprint'] !== null) {
                if (!array_key_exists('certificates', $source) || $source['certificates'] == null) {
                    $algo = isset($source['validateFingerprintAlgorithm'])
                        ? $source['validateFingerprintAlgorithm']
                        : XMLSecurityDSig::SHA1;
                    if (!$entity->validateFingerprint($source['validateFingerprint'], $algo)) {
                        Logger::info(
                            'Skipping "'.$entity->getEntityId().'" - could not verify signature using fingerprint.'."\n"
                        );
                        continue;
                    }
                } else {
                    Logger::info('Skipping validation with fingerprint since option certificate is set.'."\n");
                }
            }

            $template = null;
            if (array_key_exists('template', $source)) {
                $template = $source['template'];
            }

            if (array_key_exists('regex-template', $source)) {
                foreach ($source['regex-template'] as $e => $t) {
                    if (preg_match($e, $entity->getEntityID())) {
                        if (is_array($template)) {
                            $template = array_merge($template, $t);
                        } else {
                            $template = $t;
                        }
                    }
                }
            }

            if (in_array('shib13-sp-remote', $this->types, true)) {
                $this->addMetadata($source['src'], $entity->getMetadata1xSP(), 'shib13-sp-remote', $template);
            }
            if (in_array('shib13-idp-remote', $this->types, true)) {
                $this->addMetadata($source['src'], $entity->getMetadata1xIdP(), 'shib13-idp-remote', $template);
            }
            if (in_array('saml20-sp-remote', $this->types, true)) {
                $this->addMetadata($source['src'], $entity->getMetadata20SP(), 'saml20-sp-remote', $template);
            }
            if (in_array('saml20-idp-remote', $this->types, true)) {
                $this->addMetadata($source['src'], $entity->getMetadata20IdP(), 'saml20-idp-remote', $template);
            }
            if (in_array('attributeauthority-remote', $this->types, true)) {
                $attributeAuthorities = $entity->getAttributeAuthorities();
                if (!empty($attributeAuthorities)) {
                    $this->addMetadata(
                        $source['src'],
                        $attributeAuthorities[0],
                        'attributeauthority-remote',
                        $template
                    );
                }
            }
        }

        $this->saveState($source, $responseHeaders);
    }


    /*
     * Recursively checks whether array $dst contains array $src. If $src
     * is not an array, a literal comparison is being performed.
     */
    private function containsArray($src, $dst)
    {
        if (is_array($src)) {
            if (!is_array($dst)) {
                return false;
            }
            $dstKeys = array_keys($dst);

            /* Loop over all src keys */
            foreach ($src as $srcKey => $srcval) {
                if (is_int($srcKey)) {
                    /* key is number, check that the key appears as one
                     * of the destination keys: if not, then src has
                     * more keys than dst */
                    if (!array_key_exists($srcKey, $dst)) {
                        return false;
                    }

                    /* loop over dest keys, to find value: we don't know
                     * whether they are in the same order */
                    $submatch = false;
                    foreach ($dstKeys as $dstKey) {
                        if ($this->containsArray($srcval, $dst[$dstKey])) {
                            $submatch = true;
                            break;
                        }
                    }
                    if (!$submatch) {
                        return false;
                    }
                } else {
                    /* key is regexp: find matching keys */
                    /** @var array|false $matchingDstKeys */
                    $matchingDstKeys = preg_grep($srcKey, $dstKeys);
                    if (!is_array($matchingDstKeys)) {
                        return false;
                    }

                    $match = false;
                    foreach ($matchingDstKeys as $dstKey) {
                        if ($this->containsArray($srcval, $dst[$dstKey])) {
                            /* Found a match */
                            $match = true;
                            break;
                        }
                    }
                    if (!$match) {
                        /* none of the keys has a matching value */
                        return false;
                    }
                }
            }
            /* each src key/value matches */
            return true;
        } else {
            /* src is not an array, do a regexp match against dst */
            return (preg_match($src, $dst) === 1);
        }
    }

    /**
     * Create HTTP context, with any available caches taken into account
     *
     * @param array $source
     * @return array
     */
    private function createContext(array $source)
    {
        $config = Configuration::getInstance();
        $name = $config->getString('technicalcontact_name', null);
        $mail = $config->getString('technicalcontact_email', null);

        $rawheader = "User-Agent: SimpleSAMLphp metarefresh, run by $name <$mail>\r\n";

        if (isset($source['conditionalGET']) && $source['conditionalGET']) {
            if (array_key_exists($source['src'], $this->state)) {
                $sourceState = $this->state[$source['src']];

                if (isset($sourceState['last-modified'])) {
                    $rawheader .= 'If-Modified-Since: '.$sourceState['last-modified']."\r\n";
                }

                if (isset($sourceState['etag'])) {
                    $rawheader .= 'If-None-Match: '.$sourceState['etag']."\r\n";
                }
            }
        }

        return ['http' => ['header' => $rawheader]];
    }


    /**
     * @param array $source
     * @return void
     */
    private function addCachedMetadata(array $source)
    {
        if (isset($this->oldMetadataSrc)) {
            foreach ($this->types as $type) {
                foreach ($this->oldMetadataSrc->getMetadataSet($type) as $entity) {
                    if (array_key_exists('metarefresh:src', $entity)) {
                        if ($entity['metarefresh:src'] == $source['src']) {
                            $this->addMetadata($source['src'], $entity, $type);
                        }
                    }
                }
            }
        }
    }


    /**
     * Store caching state data for a source
     *
     * @param array $source
     * @param array|null $responseHeaders
     * @return void
     */
    private function saveState(array $source, $responseHeaders)
    {
        if (isset($source['conditionalGET']) && $source['conditionalGET']) {
            // Headers section
            if ($responseHeaders !== null) {
                $candidates = ['last-modified', 'etag'];

                foreach ($candidates as $candidate) {
                    if (array_key_exists($candidate, $responseHeaders)) {
                        $this->state[$source['src']][$candidate] = $responseHeaders[$candidate];
                    }
                }
            }

            if (!empty($this->state[$source['src']])) {
                // Timestamp when this src was requested.
                $this->state[$source['src']]['requested_at'] = $this->getTime();
                $this->changed = true;
            }
        }
    }


    /**
     * Parse XML metadata and return entities
     *
     * @param string $data
     * @param array $source
     * @return \SimpleSAML\Metadata\SAMLParser[]
     * @throws \Exception
     */
    private function loadXML($data, array $source)
    {
        try {
            $doc = DOMDocumentFactory::fromString($data);
        } catch (Exception $e) {
            throw new Exception('Failed to read XML from ' . $source['src']);
        }
        return Metadata\SAMLParser::parseDescriptorsElement($doc->documentElement);
    }


    /**
     * This function writes the state array back to disk
     *
     * @return void
     */
    public function writeState()
    {
        if ($this->changed && !is_null($this->stateFile)) {
            Logger::debug('Writing: ' . $this->stateFile);
            Utils\System::writeFile(
                $this->stateFile,
                "<?php\n/* This file was generated by the metarefresh module at ".$this->getTime().".\n".
                " Do not update it manually as it will get overwritten. */\n".
                '$state = '.var_export($this->state, true).";\n\n",
                0644
            );
        }
    }


    /**
     * This function writes the metadata to stdout.
     *
     * @return void
     */
    public function dumpMetadataStdOut()
    {
        foreach ($this->metadata as $category => $elements) {
            echo '/* The following data should be added to metadata/'.$category.'.php. */'."\n";

            foreach ($elements as $m) {
                $filename = $m['filename'];
                $entityID = $m['metadata']['entityid'];

                echo "\n";
                echo '/* The following metadata was generated from '.$filename.' on '.$this->getTime().'. */'."\n";
                echo '$metadata[\''.addslashes($entityID).'\'] = '.var_export($m['metadata'], true).';'."\n";
            }

            echo "\n";
            echo '/* End of data which should be added to metadata/'.$category.'.php. */'."\n";
            echo "\n";
        }
    }


    /**
     * This function adds metadata from the specified file to the list of metadata.
     * This function will return without making any changes if $metadata is NULL.
     *
     * @param string $filename The filename the metadata comes from.
     * @param array|null $metadata The metadata.
     * @param string $type The metadata type.
     * @param array|null $template The template.
     * @return void
     */
    private function addMetadata($filename, $metadata, $type, array $template = null)
    {
        if ($metadata === null) {
            return;
        }

        if (isset($template)) {
            $metadata = array_merge($metadata, $template);
        }

        $metadata['metarefresh:src'] = $filename;
        if (!array_key_exists($type, $this->metadata)) {
            $this->metadata[$type] = [];
        }

        // If expire is defined in constructor...
        if (!empty($this->expire)) {
            // If expire is already in metadata
            if (array_key_exists('expire', $metadata)) {
                // Override metadata expire with more restrictive global config
                if ($this->expire < $metadata['expire']) {
                    $metadata['expire'] = $this->expire;
                }

                // If expire is not already in metadata use global config
            } else {
                $metadata['expire'] = $this->expire;
            }
        }
        $this->metadata[$type][] = ['filename' => $filename, 'metadata' => $metadata];
    }


    /**
     * This function writes the metadata to an ARP file
     *
     * @param \SimpleSAML\Configuration $config
     * @return void
     */
    public function writeARPfile(Configuration $config)
    {
        assert($config instanceOf \SimpleSAML\Configuration);

        $arpfile = $config->getValue('arpfile');
        $types = ['saml20-sp-remote'];

        $md = [];
        foreach ($this->metadata as $category => $elements) {
            if (!in_array($category, $types, true)) {
                continue;
            }
            $md = array_merge($md, $elements);
        }

        // $metadata, $attributemap, $prefix, $suffix
        $arp = new ARP(
            $md,
            $config->getValue('attributemap', ''),
            $config->getValue('prefix', ''),
            $config->getValue('suffix', '')
        );


        $arpxml = $arp->getXML();

        Logger::info('Writing ARP file: '.$arpfile."\n");
        file_put_contents($arpfile, $arpxml);
    }


    /**
     * This function writes the metadata to to separate files in the output directory.
     *
     * @param string $outputDir
     * @return void
     */
    public function writeMetadataFiles($outputDir)
    {
        while (strlen($outputDir) > 0 && $outputDir[strlen($outputDir) - 1] === '/') {
            $outputDir = substr($outputDir, 0, strlen($outputDir) - 1);
        }

        if (!file_exists($outputDir)) {
            Logger::info('Creating directory: '.$outputDir."\n");
            $res = @mkdir($outputDir, 0777, true);
            if ($res === false) {
                throw new Exception('Error creating directory: ' . $outputDir);
            }
        }

        foreach ($this->types as $type) {
            $filename = $outputDir.'/'.$type.'.php';

            if (array_key_exists($type, $this->metadata)) {
                $elements = $this->metadata[$type];
                Logger::debug('Writing: '.$filename);

                $content  = '<?php'."\n".'/* This file was generated by the metarefresh module at ';
                $content .= $this->getTime()."\nDo not update it manually as it will get overwritten\n".'*/'."\n";

                foreach ($elements as $m) {
                    $entityID = $m['metadata']['entityid'];
                    $content .= "\n".'$metadata[\'';
                    $content .= addslashes($entityID).'\'] = '.var_export($m['metadata'], true).';'."\n";
                }

                Utils\System::writeFile($filename, $content, 0644);
            } elseif (is_file($filename)) {
                if (unlink($filename)) {
                    Logger::debug('Deleting stale metadata file: '.$filename);
                } else {
                    Logger::warning('Could not delete stale metadata file: '.$filename);
                }
            }
        }
    }


    /**
     * Save metadata for loading with the 'serialize' metadata loader.
     *
     * @param string $outputDir  The directory we should save the metadata to.
     * @return void
     */
    public function writeMetadataSerialize($outputDir)
    {
        assert(is_string($outputDir));

        $metaHandler = new Metadata\MetaDataStorageHandlerSerialize(['directory' => $outputDir]);

        // First we add all the metadata entries to the metadata handler
        foreach ($this->metadata as $set => $elements) {
            foreach ($elements as $m) {
                $entityId = $m['metadata']['entityid'];

                Logger::debug(
                    'metarefresh: Add metadata entry '.
                    var_export($entityId, true).' in set '.var_export($set, true).'.'
                );
                $metaHandler->saveMetadata($entityId, $set, $m['metadata']);
            }
        }

        // Then we delete old entries which should no longer exist
        $ct = time();
        foreach ($metaHandler->getMetadataSets() as $set) {
            foreach ($metaHandler->getMetadataSet($set) as $entityId => $metadata) {
                if (!array_key_exists('expire', $metadata) || !is_int($metadata['expire'])) {
                    Logger::warning(
                        'metarefresh: Metadata entry without valid expire timestamp: ' . var_export($entityId, true) .
                        ' in set ' . var_export($set, true) . '.'
                    );
                    continue;
                }

                $expire = $metadata['expire'];
                if ($expire > $ct) {
                    continue;
                }

                /** @var int $stamp */
                $stamp = date('l jS \of F Y h:i:s A', $expire);
                Logger::debug('metarefresh: ' . $entityId . ' expired ' . $stamp);
                Logger::debug(
                    'metarefresh: Delete expired metadata entry ' .
                    var_export($entityId, true) . ' in set ' . var_export($set, true) .
                    '. (' . ($ct - $expire) . ' sec)'
                );
                $metaHandler->deleteMetadata($entityId, $set);
            }
        }
    }


    /**
     * @return string
     */
    private function getTime()
    {
        // The current date, as a string
        return gmdate('Y-m-d\\TH:i:s\\Z');
    }
}
