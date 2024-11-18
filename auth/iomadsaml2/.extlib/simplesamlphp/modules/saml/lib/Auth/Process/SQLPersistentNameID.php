<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\Auth\Process;

use SAML2\Constants;
use SimpleSAML\Error;
use SimpleSAML\Logger;

/**
 * Authentication processing filter to generate a persistent NameID.
 *
 * @package SimpleSAMLphp
 */

class SQLPersistentNameID extends \SimpleSAML\Module\saml\BaseNameIDGenerator
{
    /**
     * Which attribute contains the unique identifier of the user.
     *
     * @var string
     */
    private $attribute;

    /**
     * Whether we should create a persistent NameID if not explicitly requested (as saml:PersistentNameID does).
     *
     * @var boolean
     */
    private $allowUnspecified = false;

    /**
     * Whether we should create a persistent NameID if a different format is requested.
     *
     * @var boolean
     */
    private $allowDifferent = false;

    /**
     * Whether we should ignore allowCreate in the NameID policy
     *
     * @var boolean
     */
    private $alwaysCreate = false;

    /**
     * Database store configuration.
     *
     * @var array
     */
    private $storeConfig = [];


    /**
     * Initialize this filter, parse configuration.
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception If the 'attribute' option is not specified.
     */
    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));

        $this->format = Constants::NAMEID_PERSISTENT;

        if (!isset($config['attribute'])) {
            throw new Error\Exception("PersistentNameID: Missing required option 'attribute'.");
        }
        $this->attribute = $config['attribute'];

        if (isset($config['allowUnspecified'])) {
            $this->allowUnspecified = (bool) $config['allowUnspecified'];
        }

        if (isset($config['allowDifferent'])) {
            $this->allowDifferent = (bool) $config['allowDifferent'];
        }

        if (isset($config['alwaysCreate'])) {
            $this->alwaysCreate = (bool) $config['alwaysCreate'];
        }

        if (isset($config['store'])) {
            $this->storeConfig = (array) $config['store'];
        }
    }


    /**
     * Get the NameID value.
     *
     * @param array $state The state array.
     * @return string|null The NameID value.
     *
     * @throws \SimpleSAML\Module\saml\Error if the NameID creation policy is invalid.
     */
    protected function getValue(array &$state)
    {
        if (!isset($state['saml:NameIDFormat']) && !$this->allowUnspecified) {
            Logger::debug(
                'SQLPersistentNameID: Request did not specify persistent NameID format, ' .
                'not generating persistent NameID.'
            );
            return null;
        }

        $validNameIdFormats = @array_filter([
            $state['saml:NameIDFormat'],
            $state['SPMetadata']['NameIDFormat']
        ]);
        if (
            count($validNameIdFormats)
            && !in_array($this->format, $validNameIdFormats, true)
            && !$this->allowDifferent
        ) {
            Logger::debug(
                'SQLPersistentNameID: SP expects different NameID format (' .
                implode(', ', $validNameIdFormats) . '),  not generating persistent NameID.'
            );
            return null;
        }

        if (!isset($state['Destination']['entityid'])) {
            Logger::warning('SQLPersistentNameID: No SP entity ID - not generating persistent NameID.');
            return null;
        }
        $spEntityId = $state['Destination']['entityid'];

        if (!isset($state['Source']['entityid'])) {
            Logger::warning('SQLPersistentNameID: No IdP entity ID - not generating persistent NameID.');
            return null;
        }
        $idpEntityId = $state['Source']['entityid'];

        if (!isset($state['Attributes'][$this->attribute]) || count($state['Attributes'][$this->attribute]) === 0) {
            Logger::warning(
                'SQLPersistentNameID: Missing attribute ' . var_export($this->attribute, true) .
                ' on user - not generating persistent NameID.'
            );
            return null;
        }
        if (count($state['Attributes'][$this->attribute]) > 1) {
            Logger::warning(
                'SQLPersistentNameID: More than one value in attribute ' . var_export($this->attribute, true) .
                ' on user - not generating persistent NameID.'
            );
            return null;
        }
        $uid = array_values($state['Attributes'][$this->attribute]); // just in case the first index is no longer 0
        $uid = $uid[0];

        if (empty($uid)) {
            Logger::warning(
                'Empty value in attribute ' . var_export($this->attribute, true) .
                ' on user - not generating persistent NameID.'
            );
            return null;
        }

        $value = \SimpleSAML\Module\saml\IdP\SQLNameID::get($idpEntityId, $spEntityId, $uid, $this->storeConfig);
        if ($value !== null) {
            Logger::debug(
                'SQLPersistentNameID: Found persistent NameID ' . var_export($value, true) . ' for user ' .
                var_export($uid, true) . '.'
            );
            return $value;
        }

        if ((!isset($state['saml:AllowCreate']) || !$state['saml:AllowCreate']) && !$this->alwaysCreate) {
            Logger::warning(
                'SQLPersistentNameID: Did not find persistent NameID for user, and not allowed to create new NameID.'
            );
            throw new \SimpleSAML\Module\saml\Error(
                Constants::STATUS_RESPONDER,
                Constants::STATUS_INVALID_NAMEID_POLICY
            );
        }

        $value = bin2hex(openssl_random_pseudo_bytes(20));
        Logger::debug(
            'SQLPersistentNameID: Created persistent NameID ' . var_export($value, true) . ' for user ' .
            var_export($uid, true) . '.'
        );
        \SimpleSAML\Module\saml\IdP\SQLNameID::add($idpEntityId, $spEntityId, $uid, $value, $this->storeConfig);

        return $value;
    }
}
