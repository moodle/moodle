<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SAML2\Constants;
use SAML2\XML\saml\NameID;
use SimpleSAML\Utils;

/**
 * Filter to generate the eduPersonTargetedID attribute.
 *
 * By default, this filter will generate the ID based on the UserID of the current user.
 * This is by default generated from the attribute configured in 'userid.attribute' in the
 * metadata. If this attribute isn't present, the userid will be generated from the
 * eduPersonPrincipalName attribute, if it is present.
 *
 * It is possible to generate this attribute from another attribute by specifying this attribute
 * in this configuration.
 *
 * Example - generate from user ID:
 * <code>
 * 'authproc' => array(
 *   50 => 'core:TargetedID',
 * )
 * </code>
 *
 * Example - generate from mail-attribute:
 * <code>
 * 'authproc' => array(
 *   50 => array('class' => 'core:TargetedID' , 'attributename' => 'mail'),
 * ),
 * </code>
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */
class TargetedID extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The attribute we should generate the targeted id from, or NULL if we should use the
     * UserID.
     *
     * @var string|null
     */
    private $attribute = null;

    /**
     * Whether the attribute should be generated as a NameID value, or as a simple string.
     *
     * @var boolean
     */
    private $generateNameId = false;


    /**
     * Initialize this filter.
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        if (array_key_exists('identifyingAttribute', $config)) {
            $this->attribute = $config['identifyingAttribute'];
            if (!is_string($this->attribute)) {
                throw new \Exception('Invalid `identifyingAttribute` name given to core:TargetedID filter.');
            }
        } elseif (array_key_exists('attributename', $config)) {
            $this->attribute = $config['attributename'];
            if (!is_string($this->attribute)) {
                throw new \Exception('Invalid `attributename` given to core:TargetedID filter.');
            }
        }

        if (array_key_exists('nameId', $config)) {
            $this->generateNameId = $config['nameId'];
            if (!is_bool($this->generateNameId)) {
                throw new \Exception('Invalid value of \'nameId\'-option to core:TargetedID filter.');
            }
        }
    }


    /**
     * Apply filter to add the targeted ID.
     *
     * @param array &$state  The current state.
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('Attributes', $state));

        if ($this->attribute === null) {
            if (!array_key_exists('UserID', $state)) {
                throw new \Exception('core:TargetedID: Missing UserID for this user. Please' .
                    ' check the \'userid.attribute\' option in the metadata against the' .
                    ' attributes provided by the authentication source.');
            }

            $userID = $state['UserID'];
        } else {
            if (!array_key_exists($this->attribute, $state['Attributes'])) {
                throw new \Exception('core:TargetedID: Missing attribute \'' . $this->attribute .
                    '\', which is needed to generate the targeted ID.');
            }

            $userID = $state['Attributes'][$this->attribute][0];
        }


        $secretSalt = Utils\Config::getSecretSalt();

        if (array_key_exists('Source', $state)) {
            $srcID = self::getEntityId($state['Source']);
        } else {
            $srcID = '';
        }

        if (array_key_exists('Destination', $state)) {
            $dstID = self::getEntityId($state['Destination']);
        } else {
            $dstID = '';
        }

        $uidData = 'uidhashbase' . $secretSalt;
        $uidData .= strlen($srcID) . ':' . $srcID;
        $uidData .= strlen($dstID) . ':' . $dstID;
        $uidData .= strlen($userID) . ':' . $userID;
        $uidData .= $secretSalt;

        $uid = hash('sha1', $uidData);

        if ($this->generateNameId) {
            // Convert the targeted ID to a SAML 2.0 name identifier element
            $nameId = new NameID();
            $nameId->setValue($uid);
            $nameId->setFormat(Constants::NAMEID_PERSISTENT);

            if (isset($state['Source']['entityid'])) {
                $nameId->setNameQualifier($state['Source']['entityid']);
            }
            if (isset($state['Destination']['entityid'])) {
                $nameId->setSPNameQualifier($state['Destination']['entityid']);
            }
        } else {
            $nameId = $uid;
        }

        $state['Attributes']['eduPersonTargetedID'] = [$nameId];
    }


    /**
     * Generate ID from entity metadata.
     *
     * This function takes in the metadata of an entity, and attempts to generate
     * an unique identifier based on that.
     *
     * @param array $metadata  The metadata of the entity.
     * @return string  The unique identifier for the entity.
     */
    private static function getEntityId(array $metadata): string
    {
        $id = '';

        if (array_key_exists('metadata-set', $metadata)) {
            $set = $metadata['metadata-set'];
            $id .= 'set' . strlen($set) . ':' . $set;
        }

        if (array_key_exists('entityid', $metadata)) {
            $entityid = $metadata['entityid'];
            $id .= 'set' . strlen($entityid) . ':' . $entityid;
        }

        return $id;
    }
}
