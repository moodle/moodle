<?php

namespace SimpleSAML\Module\consent;

/**
 * Base class for consent storage handlers.
 *
 * @package SimpleSAMLphp
 * @author Olav Morken <olav.morken@uninett.no>
 * @author JAcob Christiansen <jach@wayf.dk>
 */

abstract class Store
{
    /**
     * Constructor for the base class.
     *
     * This constructor should always be called first in any class which implements this class.
     *
     * @param array &$config The configuration for this storage handler.
     */
    protected function __construct(&$config)
    {
        assert(is_array($config));
    }


    /**
     * Check for consent.
     *
     * This function checks whether a given user has authorized the release of the attributes identified by
     * $attributeSet from $source to $destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifyes the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if the user has given consent earlier, false if not
     *              (or on error).
     */
    abstract public function hasConsent($userId, $destinationId, $attributeSet);


    /**
     * Save consent.
     *
     * Called when the user asks for the consent to be saved. If consent information for the given user and destination
     * already exists, it should be overwritten.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifyes the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if consent is succesfully saved otherwise false.
     */
    abstract public function saveConsent($userId, $destinationId, $attributeSet);


    /**
     * Delete consent.
     *
     * Called when a user revokes consent for a given destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifyes the destination.
     *
     * @return mixed Should be the number of consent deleted.
     */
    abstract public function deleteConsent($userId, $destinationId);


    /**
     * Delete all consents.
     *
     * Called when a user revokes all consents
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return mixed Should be the number of consent removed
     *
     * @throws \Exception
     */
    public function deleteAllConsents($userId)
    {
        throw new \Exception('Not implemented: deleteAllConsents()');
    }


    /**
     * Get statistics for all consent given in the consent store
     *
     * @return mixed Statistics from the consent store
     *
     * @throws \Exception
     */
    public function getStatistics()
    {
        throw new \Exception('Not implemented: getStatistics()');
    }


    /**
     * Retrieve consents.
     *
     * This function should return a list of consents the user has saved.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return array Array of all destination ids the user has given consent for.
     */
    abstract public function getConsents($userId);


    /**
     * Parse consent storage configuration.
     *
     * This function parses the configuration for a consent storage method. An exception will be thrown if
     * configuration parsing fails.
     *
     * @param mixed $config The configuration.
     *
     * @return \SimpleSAML\Module\consent\Store An object which implements the \SimpleSAML\Module\consent\Store class.
     *
     * @throws \Exception if the configuration is invalid.
     */
    public static function parseStoreConfig($config)
    {
        if (is_string($config)) {
            $config = [$config];
        }

        if (!is_array($config)) {
            throw new \Exception('Invalid configuration for consent store option: '.var_export($config, true));
        }

        if (!array_key_exists(0, $config)) {
            throw new \Exception('Consent store without name given.');
        }

        $className = \SimpleSAML\Module::resolveClass(
            $config[0],
            'Consent\Store',
            '\SimpleSAML\Module\consent\Store'
        );

        unset($config[0]);
        /**
         * @psalm-suppress InvalidStringClass
         * @var \SimpleSAML\Module\consent\Store $retval
         */
        $retval = new $className($config);
        return $retval;
    }
}
