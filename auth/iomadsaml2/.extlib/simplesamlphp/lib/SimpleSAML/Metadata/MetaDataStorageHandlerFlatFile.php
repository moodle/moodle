<?php

declare(strict_types=1);

namespace SimpleSAML\Metadata;

use SimpleSAML\Configuration;

/**
 * This file defines a flat file metadata source.
 * Instantiation of session handler objects should be done through
 * the class method getMetadataHandler().
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */

class MetaDataStorageHandlerFlatFile extends MetaDataStorageSource
{
    /**
     * This is the directory we will load metadata files from. The path will always end
     * with a '/'.
     *
     * @var string
     */
    private $directory = '/';


    /**
     * This is an associative array which stores the different metadata sets we have loaded.
     *
     * @var array
     */
    private $cachedMetadata = [];


    /**
     * This constructor initializes the flatfile metadata storage handler with the
     * specified configuration. The configuration is an associative array with the following
     * possible elements:
     * - 'directory': The directory we should load metadata from. The default directory is
     *                set in the 'metadatadir' configuration option in 'config.php'.
     *
     * @param array $config An associative array with the configuration for this handler.
     */
    protected function __construct($config)
    {
        assert(is_array($config));

        // get the configuration
        $globalConfig = Configuration::getInstance();

        // find the path to the directory we should search for metadata in
        if (array_key_exists('directory', $config)) {
            $this->directory = $config['directory'] ?: 'metadata/';
        } else {
            $this->directory = $globalConfig->getString('metadatadir', 'metadata/');
        }

        /* Resolve this directory relative to the SimpleSAMLphp directory (unless it is
         * an absolute path).
         */

        /** @var string $base */
        $base = $globalConfig->resolvePath($this->directory);
        $this->directory = $base . '/';
    }


    /**
     * This function loads the given set of metadata from a file our metadata directory.
     * This function returns null if it is unable to locate the given set in the metadata directory.
     *
     * @param string $set The set of metadata we are loading.
     *
     * @return array|null An associative array with the metadata,
     *     or null if we are unable to load metadata from the given file.
     * @throws \Exception If the metadata set cannot be loaded.
     */
    private function load(string $set): ?array
    {
        $metadatasetfile = $this->directory . $set . '.php';

        if (!file_exists($metadatasetfile)) {
            return null;
        }

        /** @psalm-var mixed $metadata   We cannot be sure what the include below will do with this var */
        $metadata = [];

        include($metadatasetfile);

        if (!is_array($metadata)) {
            throw new \Exception('Could not load metadata set [' . $set . '] from file: ' . $metadatasetfile);
        }

        return $metadata;
    }


    /**
     * This function retrieves the given set of metadata. It will return an empty array if it is
     * unable to locate it.
     *
     * @param string $set The set of metadata we are retrieving.
     *
     * @return array An associative array with the metadata. Each element in the array is an entity, and the
     *         key is the entity id.
     */
    public function getMetadataSet($set)
    {
        if (array_key_exists($set, $this->cachedMetadata)) {
            return $this->cachedMetadata[$set];
        }

        /** @var array|null $metadataSet */
        $metadataSet = $this->load($set);
        if ($metadataSet === null) {
            $metadataSet = [];
        }

        // add the entity id of an entry to each entry in the metadata
        foreach ($metadataSet as $entityId => &$entry) {
            $entry = $this->updateEntityID($set, $entityId, $entry);
        }

        $this->cachedMetadata[$set] = $metadataSet;
        return $metadataSet;
    }
}
