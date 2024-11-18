<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\Auth\Process;

use SAML2\Constants;
use SimpleSAML\Error;
use SimpleSAML\Logger;
use SimpleSAML\Utils;

/**
 * Authentication processing filter to generate a persistent NameID.
 *
 * @package SimpleSAMLphp
 */

class PersistentNameID extends \SimpleSAML\Module\saml\BaseNameIDGenerator
{
    /**
     * Which attribute contains the unique identifier of the user.
     *
     * @var string
     */
    private $attribute;


    /**
     * Initialize this filter, parse configuration.
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception If the required option 'attribute' is missing.
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
    }


    /**
     * Get the NameID value.
     *
     * @param array $state The state array.
     * @return string|null The NameID value.
     */
    protected function getValue(array &$state)
    {
        if (!isset($state['Destination']['entityid'])) {
            Logger::warning('No SP entity ID - not generating persistent NameID.');
            return null;
        }
        $spEntityId = $state['Destination']['entityid'];

        if (!isset($state['Source']['entityid'])) {
            Logger::warning('No IdP entity ID - not generating persistent NameID.');
            return null;
        }
        $idpEntityId = $state['Source']['entityid'];

        if (!isset($state['Attributes'][$this->attribute]) || count($state['Attributes'][$this->attribute]) === 0) {
            Logger::warning(
                'Missing attribute ' . var_export($this->attribute, true) .
                ' on user - not generating persistent NameID.'
            );
            return null;
        }
        if (count($state['Attributes'][$this->attribute]) > 1) {
            Logger::warning(
                'More than one value in attribute ' . var_export($this->attribute, true) .
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

        $secretSalt = Utils\Config::getSecretSalt();

        $uidData = 'uidhashbase' . $secretSalt;
        $uidData .= strlen($idpEntityId) . ':' . $idpEntityId;
        $uidData .= strlen($spEntityId) . ':' . $spEntityId;
        $uidData .= strlen($uid) . ':' . $uid;
        $uidData .= $secretSalt;

        return sha1($uidData);
    }
}
