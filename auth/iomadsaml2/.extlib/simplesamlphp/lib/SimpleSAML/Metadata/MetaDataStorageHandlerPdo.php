<?php

declare(strict_types=1);

namespace SimpleSAML\Metadata;

use SimpleSAML\Database;
use SimpleSAML\Error;

/**
 * Class for handling metadata files stored in a database.
 *
 * This class has been based off a previous version written by
 * mooknarf@gmail.com and patched to work with the latest version
 * of SimpleSAMLphp
 *
 * @package SimpleSAMLphp
 */

class MetaDataStorageHandlerPdo extends MetaDataStorageSource
{
    /**
     * The PDO object
     */
    private $db;

    /**
     * Prefix to apply to the metadata table
     */
    private $tablePrefix;

    /**
     * This is an associative array which stores the different metadata sets we have loaded.
     */
    private $cachedMetadata = [];

    /**
     * All the metadata sets supported by this MetaDataStorageHandler
     */
    public $supportedSets = [
        'adfs-idp-hosted',
        'adfs-sp-remote',
        'saml20-idp-hosted',
        'saml20-idp-remote',
        'saml20-sp-remote',
        'shib13-idp-hosted',
        'shib13-idp-remote',
        'shib13-sp-hosted',
        'shib13-sp-remote'
    ];


    /**
     * This constructor initializes the PDO metadata storage handler with the specified
     * configuration. The configuration is an associative array with the following
     * possible elements (set in config.php):
     * - 'usePersistentConnection': TRUE/FALSE if database connection should be persistent.
     * - 'dsn':                     The database connection string.
     * - 'username':                Database user name
     * - 'password':                Password for the database user.
     *
     * @param array $config An associative array with the configuration for this handler.
     */
    public function __construct($config)
    {
        assert(is_array($config));

        $this->db = Database::getInstance();
    }


    /**
     * This function loads the given set of metadata from a file to a configured database.
     * This function returns NULL if it is unable to locate the given set in the metadata directory.
     *
     * @param string $set The set of metadata we are loading.
     *
     * @return array|null $metadata Associative array with the metadata, or NULL if we are unable to load
     *     metadata from the given file.
     *
     * @throws \Exception If a database error occurs.
     * @throws \SimpleSAML\Error\Exception If the metadata can be retrieved from the database, but cannot be decoded.
     */
    private function load(string $set): ?array
    {
        $tableName = $this->getTableName($set);

        if (!in_array($set, $this->supportedSets, true)) {
            return null;
        }

        $stmt = $this->db->read("SELECT entity_id, entity_data FROM $tableName");
        if ($stmt->execute()) {
            $metadata = [];

            while ($d = $stmt->fetch()) {
                $data = json_decode($d['entity_data'], true);
                if ($data === null) {
                    throw new Error\Exception("Cannot decode metadata for entity '${d['entity_id']}'");
                }
                if (!array_key_exists('entityid', $data)) {
                    $data['entityid'] = $d['entity_id'];
                }
                $metadata[$d['entity_id']] = $data;
            }

            return $metadata;
        } else {
            throw new \Exception(
                'PDO metadata handler: Database error: ' . var_export($this->db->getLastError(), true)
            );
        }
    }


    /**
     * Retrieve a list of all available metadata for a given set.
     *
     * @param string $set The set we are looking for metadata in.
     *
     * @return array $metadata An associative array with all the metadata for the given set.
     */
    public function getMetadataSet($set)
    {
        assert(is_string($set));

        if (array_key_exists($set, $this->cachedMetadata)) {
            return $this->cachedMetadata[$set];
        }

        $metadataSet = $this->load($set);
        if ($metadataSet === null) {
            $metadataSet = [];
        }

        /** @var array $metadataSet */
        foreach ($metadataSet as $entityId => &$entry) {
            $entry = $this->updateEntityID($set, $entityId, $entry);
        }

        $this->cachedMetadata[$set] = $metadataSet;
        return $metadataSet;
    }

    /**
     * Retrieve a metadata entry.
     *
     * @param string $index The entityId we are looking up.
     * @param string $set The set we are looking for metadata in.
     *
     * @return array|null An associative array with metadata for the given entity, or NULL if we are unable to
     *         locate the entity.
     */
    public function getMetaData($index, $set)
    {
        assert(is_string($index));
        assert(is_string($set));

        // validate the metadata set is valid
        if (!in_array($set, $this->supportedSets, true)) {
            return null;
        }

        // support caching
        if (isset($this->cachedMetadata[$index][$set])) {
            return $this->cachedMetadata[$index][$set];
        }

        $tableName = $this->getTableName($set);

        // according to the docs, it looks like *-idp-hosted metadata are the types
        // that allow the __DYNAMIC:*__ entity id.  with the current table design
        // we need to lookup the specific metadata entry but also we need to lookup
        // any dynamic entries to see if the dynamic hosted entity id matches
        if (substr($set, -10) == 'idp-hosted') {
            $stmt = $this->db->read(
                "SELECT entity_id, entity_data FROM {$tableName} "
                . "WHERE (entity_id LIKE :dynamicId OR entity_id = :entityId)",
                ['dynamicId' => '__DYNAMIC%', 'entityId' => $index]
            );
        } else {
            // other metadata types should be able to match on entity id
            $stmt = $this->db->read(
                "SELECT entity_id, entity_data FROM {$tableName} WHERE entity_id = :entityId",
                ['entityId' => $index]
            );
        }

        // throw pdo exception upon execution failure
        if (!$stmt->execute()) {
            throw new \Exception(
                'PDO metadata handler: Database error: ' . var_export($this->db->getLastError(), true)
            );
        }

        // load the metadata into an array
        $metadataSet = [];
        while ($d = $stmt->fetch()) {
            $data = json_decode($d['entity_data'], true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new \SimpleSAML\Error\Exception(
                    "Cannot decode metadata for entity '${d['entity_id']}'"
                );
            }

            // update the entity id to either the key (if not dynamic or generate the dynamic hosted url)
            $metadataSet[$d['entity_id']] = $this->updateEntityID($set, $index, $data);
        }

        $indexLookup = $this->lookupIndexFromEntityId($index, $metadataSet);
        if (isset($indexLookup) && array_key_exists($indexLookup, $metadataSet)) {
            $this->cachedMetadata[$indexLookup][$set] = $metadataSet[$indexLookup];
            return $this->cachedMetadata[$indexLookup][$set];
        }

        return null;
    }

    /**
     * Add metadata to the configured database
     *
     * @param string $index Entity ID
     * @param string $set The set to add the metadata to
     * @param array  $entityData Metadata
     *
     * @return bool True/False if entry was successfully added
     */
    public function addEntry($index, $set, $entityData)
    {
        assert(is_string($index));
        assert(is_string($set));
        assert(is_array($entityData));

        if (!in_array($set, $this->supportedSets, true)) {
            return false;
        }

        $tableName = $this->getTableName($set);

        $metadata = $this->db->read(
            "SELECT entity_id, entity_data FROM $tableName WHERE entity_id = :entity_id",
            [
                'entity_id' => $index,
            ]
        );

        $retrivedEntityIDs = $metadata->fetch();

        $params = [
            'entity_id'   => $index,
            'entity_data' => json_encode($entityData),
        ];

        if ($retrivedEntityIDs !== false && count($retrivedEntityIDs) > 0) {
            $rows = $this->db->write(
                "UPDATE $tableName SET entity_data = :entity_data WHERE entity_id = :entity_id",
                $params
            );
        } else {
            $rows = $this->db->write(
                "INSERT INTO $tableName (entity_id, entity_data) VALUES (:entity_id, :entity_data)",
                $params
            );
        }

        return $rows === 1;
    }


    /**
     * Replace the -'s to an _ in table names for Metadata sets
     * since SQL does not allow a - in a table name.
     *
     * @param string $table Table
     *
     * @return string Replaced table name
     */
    private function getTableName(string $table): string
    {
        return $this->db->applyPrefix(str_replace("-", "_", $this->tablePrefix . $table));
    }


    /**
     * Initialize the configured database
     *
     * @return int|false The number of SQL statements successfully executed, false if some error occurred.
     */
    public function initDatabase()
    {
        $stmt = 0;
        $fine = true;
        $driver = $this->db->getDriver();

        $text = 'TEXT';
        if ($driver === 'mysql') {
            $text = 'MEDIUMTEXT';
        }

        foreach ($this->supportedSets as $set) {
            $tableName = $this->getTableName($set);
            $rows = $this->db->write(sprintf(
                "CREATE TABLE IF NOT EXISTS $tableName (entity_id VARCHAR(255) PRIMARY KEY NOT NULL, "
                    . "entity_data %s NOT NULL)",
                $text
            ));

            if ($rows === false) {
                $fine = false;
            } else {
                $stmt += $rows;
            }
        }

        if (!$fine) {
            return false;
        }
        return $stmt;
    }
}
