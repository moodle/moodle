<?php

declare(strict_types=1);

namespace SimpleSAML\Metadata;

use SimpleSAML\Error;
use SimpleSAML\Module;
use SimpleSAML\Utils;

/**
 * This abstract class defines an interface for metadata storage sources.
 *
 * It also contains the overview of the different metadata storage sources.
 * A metadata storage source can be loaded by passing the configuration of it
 * to the getSource static function.
 *
 * @author Olav Morken, UNINETT AS.
 * @author Andreas Aakre Solberg, UNINETT AS.
 * @package SimpleSAMLphp
 */

abstract class MetaDataStorageSource
{
    /**
     * Parse array with metadata sources.
     *
     * This function accepts an array with metadata sources, and returns an array with
     * each metadata source as an object.
     *
     * @param array $sourcesConfig Array with metadata source configuration.
     *
     * @return array  Parsed metadata configuration.
     *
     * @throws \Exception If something is wrong in the configuration.
     */
    public static function parseSources($sourcesConfig)
    {
        assert(is_array($sourcesConfig));

        $sources = [];

        foreach ($sourcesConfig as $sourceConfig) {
            if (!is_array($sourceConfig)) {
                throw new \Exception("Found an element in metadata source configuration which wasn't an array.");
            }

            $sources[] = self::getSource($sourceConfig);
        }

        return $sources;
    }


    /**
     * This function creates a metadata source based on the given configuration.
     * The type of source is based on the 'type' parameter in the configuration.
     * The default type is 'flatfile'.
     *
     * @param array $sourceConfig Associative array with the configuration for this metadata source.
     *
     * @return \SimpleSAML\Metadata\MetaDataStorageSource An instance of a metadata source with the given configuration.
     *
     * @throws \Exception If the metadata source type is invalid.
     */
    public static function getSource($sourceConfig)
    {
        assert(is_array($sourceConfig));

        if (array_key_exists('type', $sourceConfig)) {
            $type = $sourceConfig['type'];
        } else {
            $type = 'flatfile';
        }

        switch ($type) {
            case 'flatfile':
                return new MetaDataStorageHandlerFlatFile($sourceConfig);
            case 'xml':
                return new MetaDataStorageHandlerXML($sourceConfig);
            case 'serialize':
                return new MetaDataStorageHandlerSerialize($sourceConfig);
            case 'mdx':
            case 'mdq':
                return new Sources\MDQ($sourceConfig);
            case 'pdo':
                return new MetaDataStorageHandlerPdo($sourceConfig);
            default:
                // metadata store from module
                try {
                    $className = Module::resolveClass(
                        $type,
                        'MetadataStore',
                        '\SimpleSAML\Metadata\MetaDataStorageSource'
                    );
                } catch (\Exception $e) {
                    throw new Error\CriticalConfigurationError(
                        "Invalid 'type' for metadata source. Cannot find store '$type'.",
                        null
                    );
                }

                /** @var \SimpleSAML\Metadata\MetaDataStorageSource */
                return new $className($sourceConfig);
        }
    }


    /**
     * This function attempts to generate an associative array with metadata for all entities in the
     * given set. The key of the array is the entity id.
     *
     * A subclass should override this function if it is able to easily generate this list.
     *
     * @param string $set The set we want to list metadata for.
     *
     * @return array An associative array with all entities in the given set, or an empty array if we are
     *         unable to generate this list.
     */
    public function getMetadataSet($set)
    {
        return [];
    }


    /**
     * This function resolves an host/path combination to an entity id.
     *
     * This class implements this function using the getMetadataSet-function. A subclass should
     * override this function if it doesn't implement the getMetadataSet function, or if the
     * implementation of getMetadataSet is slow.
     *
     * @param string $hostPath The host/path combination we are looking up.
     * @param string $set Which set of metadata we are looking it up in.
     * @param string $type Do you want to return the metaindex or the entityID. [entityid|metaindex]
     *
     * @return string|null An entity id which matches the given host/path combination, or NULL if
     *         we are unable to locate one which matches.
     */
    public function getEntityIdFromHostPath($hostPath, $set, $type = 'entityid')
    {

        $metadataSet = $this->getMetadataSet($set);
        /** @psalm-suppress DocblockTypeContradiction */
        if ($metadataSet === null) {
            // this metadata source does not have this metadata set
            return null;
        }

        foreach ($metadataSet as $index => $entry) {
            if (!array_key_exists('host', $entry)) {
                continue;
            }

            if ($hostPath === $entry['host']) {
                if ($type === 'entityid') {
                    return $entry['entityid'];
                } else {
                    return $index;
                }
            }
        }

        // no entries matched, we should return null
        return null;
    }


    /**
     * This function will go through all the metadata, and check the DiscoHints->IPHint
     * parameter, which defines a network space (ip range) for each remote entry.
     * This function returns the entityID for any of the entities that have an
     * IP range which the IP falls within.
     *
     * @param string $set Which set of metadata we are looking it up in.
     * @param string $ip IP address
     * @param string $type Do you want to return the metaindex or the entityID. [entityid|metaindex]
     *
     * @return string|null The entity id of a entity which have a CIDR hint where the provided
     *        IP address match.
     */
    public function getPreferredEntityIdFromCIDRhint($set, $ip, $type = 'entityid')
    {
        $metadataSet = $this->getMetadataSet($set);

        foreach ($metadataSet as $index => $entry) {
            $cidrHints = [];

            // support hint.cidr for idp discovery
            if (array_key_exists('hint.cidr', $entry) && is_array($entry['hint.cidr'])) {
                $cidrHints = $entry['hint.cidr'];
            }

            // support discohints in idp metadata for idp discovery
            if (
                array_key_exists('DiscoHints', $entry)
                && array_key_exists('IPHint', $entry['DiscoHints'])
                && is_array($entry['DiscoHints']['IPHint'])
            ) {
                // merge with hints derived from discohints, but prioritize hint.cidr in case it is used
                $cidrHints = array_merge($entry['DiscoHints']['IPHint'], $cidrHints);
            }

            if (empty($cidrHints)) {
                continue;
            }

            foreach ($cidrHints as $hint_entry) {
                if (Utils\Net::ipCIDRcheck($hint_entry, $ip)) {
                    if ($type === 'entityid') {
                        return $entry['entityid'];
                    } else {
                        return $index;
                    }
                }
            }
        }

        // no entries matched, we should return null
        return null;
    }


    /**
     * This function retrieves metadata for the given entity id in the given set of metadata.
     * It will return NULL if it is unable to locate the metadata.
     *
     * This class implements this function using the getMetadataSet-function. A subclass should
     * override this function if it doesn't implement the getMetadataSet function, or if the
     * implementation of getMetadataSet is slow.
     *
     * @param string $index The entityId or metaindex we are looking up.
     * @param string $set The set we are looking for metadata in.
     *
     * @return array|null An associative array with metadata for the given entity, or NULL if we are unable to
     *         locate the entity.
     */
    public function getMetaData($index, $set)
    {

        assert(is_string($index));
        assert(isset($set));

        $metadataSet = $this->getMetadataSet($set);

        $indexLookup = $this->lookupIndexFromEntityId($index, $metadataSet);
        if (isset($indexLookup) && array_key_exists($indexLookup, $metadataSet)) {
            return $metadataSet[$indexLookup];
        }

        return null;
    }

    /**
     * This function loads the metadata for entity IDs in $entityIds. It is returned as an associative array
     * where the key is the entity id. An empty array may be returned if no matching entities were found.
     * Subclasses should override if their getMetadataSet returns nothing or is slow. Subclasses may want to
     * delegate to getMetaDataForEntitiesIndividually if loading entities one at a time is faster.
     * @param array $entityIds The entity ids to load
     * @param string $set The set we want to get metadata from.
     * @return array An associative array with the metadata for the requested entities, if found.
     */
    public function getMetaDataForEntities(array $entityIds, $set)
    {
        if (count($entityIds) === 1) {
            return $this->getMetaDataForEntitiesIndividually($entityIds, $set);
        }
        $entities = $this->getMetadataSet($set);
        return array_intersect_key($entities, array_flip($entityIds));
    }

    /**
     * Loads metadata entities one at a time, rather than the default implementation of loading all entities
     * and filtering.
     * @see MetaDataStorageSource::getMetaDataForEntities()
     * @param array $entityIds The entity ids to load
     * @param string $set The set we want to get metadata from.
     * @return array An associative array with the metadata for the requested entities, if found.
     */
    protected function getMetaDataForEntitiesIndividually(array $entityIds, $set)
    {
        $entities = [];
        foreach ($entityIds as $entityId) {
            $metadata = $this->getMetaData($entityId, $set);
            if ($metadata !== null) {
                $entities[$entityId] = $metadata;
            }
        }
        return $entities;
    }

    /**
     * This method returns the full metadata set for a given entity id or null if the entity id cannot be found
     * in the given metadata set.
     *
     * @param string $entityId
     * @param array $metadataSet the already loaded metadata set
     * @return mixed|null
     */
    protected function lookupIndexFromEntityId($entityId, array $metadataSet)
    {
        assert(is_string($entityId));

        // check for hostname
        $currentHost = Utils\HTTP::getSelfHost(); // sp.example.org

        foreach ($metadataSet as $index => $entry) {
            // explicit index match
            if ($index === $entityId) {
                return $index;
            }

            if ($entry['entityid'] === $entityId) {
                if ($entry['host'] === '__DEFAULT__' || $entry['host'] === $currentHost) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * @param string $set
     * @throws \Exception
     * @return string
     */
    private function getDynamicHostedUrl(string $set): string
    {
        // get the configuration
        $baseUrl = Utils\HTTP::getBaseURL();

        if ($set === 'saml20-idp-hosted') {
            return $baseUrl . 'saml2/idp/metadata.php';
        } elseif ($set === 'saml20-sp-hosted') {
            return $baseUrl . 'saml2/sp/metadata.php';
        } elseif ($set === 'shib13-idp-hosted') {
            return $baseUrl . 'shib13/idp/metadata.php';
        } elseif ($set === 'shib13-sp-hosted') {
            return $baseUrl . 'shib13/sp/metadata.php';
        } elseif ($set === 'adfs-idp-hosted') {
            return 'urn:federation:' . Utils\HTTP::getSelfHost() . ':idp';
        } else {
            throw new \Exception('Can not generate dynamic EntityID for metadata of this type: [' . $set . ']');
        }
    }

    /**
     * Updates the metadata entry's entity id and returns the modified array.  If the entity id is __DYNAMIC:*__ a
     * the current url is assigned.  If it is explicit the entityid array key is updated to the entityId that was
     * provided.
     *
     * @param string $metadataSet a metadata set (saml20-idp-hosted, saml20-sp-remote, etc)
     * @param string $entityId the entity id we are modifying
     * @param array $metadataEntry the fully populated metadata entry
     * @return array modified metadata to include the valid entityid
     *
     * @throws \Exception
     */
    protected function updateEntityID($metadataSet, $entityId, array $metadataEntry)
    {
        assert(is_string($metadataSet));
        assert(is_string($entityId));

        $modifiedMetadataEntry = $metadataEntry;

        // generate a dynamic hosted url
        if (preg_match('/__DYNAMIC(:[0-9]+)?__/', $entityId)) {
            $modifiedMetadataEntry['entityid'] = $this->getDynamicHostedUrl($metadataSet);
        } else {
            // set the entityid metadata array key to the provided entity id
            $modifiedMetadataEntry['entityid'] = $entityId;
        }

        return $modifiedMetadataEntry;
    }
}
