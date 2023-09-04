<?php

declare(strict_types=1);

namespace SimpleSAML\XHTML;

use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Session;
use SimpleSAML\Utils;

/**
 * This class implements a generic IdP discovery service, for use in various IdP
 * discovery service pages. This should reduce code duplication.
 *
 * Experimental support added for Extended IdP Metadata Discovery Protocol by Andreas 2008-08-28
 * More information: https://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-idp-discovery.pdf
 *
 * @author Jaime Pérez <jaime.perez@uninett.no>, UNINETT AS.
 * @author Olav Morken, UNINETT AS.
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */

class IdPDisco
{
    /**
     * An instance of the configuration class.
     *
     * @var \SimpleSAML\Configuration
     */
    protected $config;

    /**
     * The identifier of this discovery service.
     *
     * @var string
     */
    protected $instance;

    /**
     * An instance of the metadata handler, which will allow us to fetch metadata about IdPs.
     *
     * @var \SimpleSAML\Metadata\MetaDataStorageHandler
     */
    protected $metadata;

    /**
     * The users session.
     *
     * @var \SimpleSAML\Session
     */
    protected $session;

    /**
     * The metadata sets we find allowed entities in, in prioritized order.
     *
     * @var array
     */
    protected $metadataSets;

    /**
     * The entity id of the SP which accesses this IdP discovery service.
     *
     * @var string
     */
    protected $spEntityId;

    /**
     * HTTP parameter from the request, indicating whether the discovery service
     * can interact with the user or not.
     *
     * @var boolean
     */
    protected $isPassive;

    /**
     * The SP request to set the IdPentityID...
     *
     * @var string|null
     */
    protected $setIdPentityID = null;

    /**
     * The name of the query parameter which should contain the users choice of IdP.
     * This option default to 'entityID' for Shibboleth compatibility.
     *
     * @var string
     */
    protected $returnIdParam;

    /**
     * The list of scoped idp's. The intersection between the metadata idpList
     * and scopedIDPList (given as a $_GET IDPList[] parameter) is presented to
     * the user. If the intersection is empty the metadata idpList is used.
     *
     * @var array
     */
    protected $scopedIDPList = [];

    /**
     * The URL the user should be redirected to after choosing an IdP.
     *
     * @var string
     */
    protected $returnURL;


    /**
     * Initializes this discovery service.
     *
     * The constructor does the parsing of the request. If this is an invalid request, it will throw an exception.
     *
     * @param array  $metadataSets Array with metadata sets we find remote entities in.
     * @param string $instance The name of this instance of the discovery service.
     *
     * @throws \Exception If the request is invalid.
     */
    public function __construct(array $metadataSets, $instance)
    {
        assert(is_string($instance));

        // initialize standard classes
        $this->config = Configuration::getInstance();
        $this->metadata = MetaDataStorageHandler::getMetadataHandler();
        $this->session = Session::getSessionFromRequest();
        $this->instance = $instance;
        $this->metadataSets = $metadataSets;

        $this->log('Accessing discovery service.');

        // standard discovery service parameters
        if (!array_key_exists('entityID', $_GET)) {
            throw new \Exception('Missing parameter: entityID');
        } else {
            $this->spEntityId = $_GET['entityID'];
        }

        if (!array_key_exists('returnIDParam', $_GET)) {
            $this->returnIdParam = 'entityID';
        } else {
            $this->returnIdParam = $_GET['returnIDParam'];
        }

        $this->log('returnIdParam initially set to [' . $this->returnIdParam . ']');

        if (!array_key_exists('return', $_GET)) {
            throw new \Exception('Missing parameter: return');
        } else {
            $this->returnURL = Utils\HTTP::checkURLAllowed($_GET['return']);
        }

        $this->isPassive = false;
        if (array_key_exists('isPassive', $_GET)) {
            if ($_GET['isPassive'] === 'true') {
                $this->isPassive = true;
            }
        }
        $this->log('isPassive initially set to [' . ($this->isPassive ? 'TRUE' : 'FALSE') . ']');

        if (array_key_exists('IdPentityID', $_GET)) {
            $this->setIdPentityID = $_GET['IdPentityID'];
        }

        if (array_key_exists('IDPList', $_REQUEST)) {
            $this->scopedIDPList = $_REQUEST['IDPList'];
        }
    }


    /**
     * Log a message.
     *
     * This is an helper function for logging messages. It will prefix the messages with our
     * discovery service type.
     *
     * @param string $message The message which should be logged.
     * @return void
     */
    protected function log($message)
    {
        Logger::info('idpDisco.' . $this->instance . ': ' . $message);
    }


    /**
     * Retrieve cookie with the given name.
     *
     * This function will retrieve a cookie with the given name for the current discovery
     * service type.
     *
     * @param string $name The name of the cookie.
     *
     * @return string|null The value of the cookie with the given name, or null if no cookie with that name exists.
     */
    protected function getCookie($name)
    {
        $prefixedName = 'idpdisco_' . $this->instance . '_' . $name;
        if (array_key_exists($prefixedName, $_COOKIE)) {
            return $_COOKIE[$prefixedName];
        } else {
            return null;
        }
    }


    /**
     * Save cookie with the given name and value.
     *
     * This function will save a cookie with the given name and value for the current discovery
     * service type.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @return void
     */
    protected function setCookie($name, $value)
    {
        $prefixedName = 'idpdisco_' . $this->instance . '_' . $name;

        $params = [
            // we save the cookies for 90 days
            'lifetime' => (60 * 60 * 24 * 90),
            // the base path for cookies. This should be the installation directory for SimpleSAMLphp
            'path'     => $this->config->getBasePath(),
            'httponly' => false,
        ];

        Utils\HTTP::setCookie($prefixedName, $value, $params, false);
    }


    /**
     * Validates the given IdP entity id.
     *
     * Takes a string with the IdP entity id, and returns the entity id if it is valid, or
     * null if not.
     *
     * @param string|null $idp The entity id we want to validate. This can be null, in which case we will return null.
     *
     * @return string|null The entity id if it is valid, null if not.
     */
    protected function validateIdP($idp)
    {
        if ($idp === null) {
            return null;
        }

        if (!$this->config->getBoolean('idpdisco.validate', true)) {
            return $idp;
        }

        foreach ($this->metadataSets as $metadataSet) {
            try {
                $this->metadata->getMetaData($idp, $metadataSet);
                return $idp;
            } catch (\Exception $e) {
                // continue
            }
        }

        $this->log('Unable to validate IdP entity id [' . $idp . '].');

        // the entity id wasn't valid
        return null;
    }


    /**
     * Retrieve the users choice of IdP.
     *
     * This function finds out which IdP the user has manually chosen, if any.
     *
     * @return string|null The entity id of the IdP the user has chosen, or null if the user has made no choice.
     */
    protected function getSelectedIdP()
    {
        /* Parameter set from the Extended IdP Metadata Discovery Service Protocol, indicating that the user prefers
         * this IdP.
         */
        if (!empty($this->setIdPentityID)) {
            return $this->validateIdP($this->setIdPentityID);
        }

        // user has clicked on a link, or selected the IdP from a drop-down list
        if (array_key_exists('idpentityid', $_GET)) {
            return $this->validateIdP($_GET['idpentityid']);
        }

        /* Search for the IdP selection from the form used by the links view. This form uses a name which equals
         * idp_<entityid>, so we search for that.
         *
         * Unfortunately, php replaces periods in the name with underscores, and there is no reliable way to get them
         * back. Therefore we do some quick and dirty parsing of the query string.
         */
        $qstr = $_SERVER['QUERY_STRING'];
        $matches = [];
        if (preg_match('/(?:^|&)idp_([^=]+)=/', $qstr, $matches)) {
            return $this->validateIdP(urldecode($matches[1]));
        }

        // no IdP chosen
        return null;
    }


    /**
     * Retrieve the users saved choice of IdP.
     *
     * @return string|null The entity id of the IdP the user has saved, or null if the user hasn't saved any choice.
     */
    protected function getSavedIdP()
    {
        if (!$this->config->getBoolean('idpdisco.enableremember', false)) {
            // saving of IdP choices is disabled
            return null;
        }

        if ($this->getCookie('remember') === '1') {
            $this->log('Return previously saved IdP because of remember cookie set to 1');
            return $this->getPreviousIdP();
        }

        if ($this->isPassive) {
            $this->log('Return previously saved IdP because of isPassive');
            return $this->getPreviousIdP();
        }

        return null;
    }


    /**
     * Retrieve the previous IdP the user used.
     *
     * @return string|null The entity id of the previous IdP the user used, or null if this is the first time.
     */
    protected function getPreviousIdP()
    {
        return $this->validateIdP($this->getCookie('lastidp'));
    }


    /**
     * Retrieve a recommended IdP based on the IP address of the client.
     *
     * @return string|null  The entity ID of the IdP if one is found, or null if not.
     */
    protected function getFromCIDRhint()
    {
        foreach ($this->metadataSets as $metadataSet) {
            $idp = $this->metadata->getPreferredEntityIdFromCIDRhint($metadataSet, $_SERVER['REMOTE_ADDR']);
            if (!empty($idp)) {
                return $idp;
            }
        }

        return null;
    }


    /**
     * Try to determine which IdP the user should most likely use.
     *
     * This function will first look at the previous IdP the user has chosen. If the user
     * hasn't chosen an IdP before, it will look at the IP address.
     *
     * @return string|null The entity id of the IdP the user should most likely use.
     */
    protected function getRecommendedIdP()
    {
        $idp = $this->getPreviousIdP();
        if ($idp !== null) {
            $this->log('Preferred IdP from previous use [' . $idp . '].');
            return $idp;
        }

        $idp = $this->getFromCIDRhint();

        if (!empty($idp)) {
            $this->log('Preferred IdP from CIDR hint [' . $idp . '].');
            return $idp;
        }

        return null;
    }


    /**
     * Save the current IdP choice to a cookie.
     *
     * @param string $idp The entityID of the IdP.
     * @return void
     */
    protected function setPreviousIdP($idp)
    {
        assert(is_string($idp));

        $this->log('Choice made [' . $idp . '] Setting cookie.');
        $this->setCookie('lastidp', $idp);
    }


    /**
     * Determine whether the choice of IdP should be saved.
     *
     * @return boolean True if the choice should be saved, false otherwise.
     */
    protected function saveIdP()
    {
        if (!$this->config->getBoolean('idpdisco.enableremember', false)) {
            // saving of IdP choices is disabled
            return false;
        }

        if (array_key_exists('remember', $_GET)) {
            return true;
        }

        return false;
    }


    /**
     * Determine which IdP the user should go to, if any.
     *
     * @return string|null The entity id of the IdP the user should be sent to, or null if the user should choose.
     */
    protected function getTargetIdP()
    {
        // first, check if the user has chosen an IdP
        $idp = $this->getSelectedIdP();
        if ($idp !== null) {
            // the user selected this IdP. Save the choice in a cookie
            $this->setPreviousIdP($idp);

            if ($this->saveIdP()) {
                $this->setCookie('remember', '1');
            } else {
                $this->setCookie('remember', '0');
            }

            return $idp;
        }

        $this->log('getSelectedIdP() returned null');

        // check if the user has saved an choice earlier
        $idp = $this->getSavedIdP();
        if ($idp !== null) {
            $this->log('Using saved choice [' . $idp . '].');
            return $idp;
        }

        // the user has made no choice
        return null;
    }


    /**
     * Retrieve the list of IdPs which are stored in the metadata.
     *
     * @return array An array with entityid => metadata mappings.
     */
    protected function getIdPList()
    {
        $idpList = [];
        foreach ($this->metadataSets as $metadataSet) {
            $newList = $this->metadata->getList($metadataSet);
            /*
             * Note that we merge the entities in reverse order. This ensures that it is the entity in the first
             * metadata set that "wins" if two metadata sets have the same entity.
             */
            $idpList = array_merge($newList, $idpList);
        }

        return $idpList;
    }


    /**
     * Return the list of scoped idp
     *
     * @return array An array of IdP entities
     */
    protected function getScopedIDPList()
    {
        return $this->scopedIDPList;
    }


    /**
     * Filter the list of IdPs.
     *
     * This method returns the IdPs that comply with the following conditions:
     *   - The IdP does not have the 'hide.from.discovery' configuration option.
     *
     * @param array $list An associative array containing metadata for the IdPs to apply the filtering to.
     *
     * @return array An associative array containing metadata for the IdPs that were not filtered out.
     */
    protected function filterList($list)
    {
        foreach ($list as $entity => $metadata) {
            if (array_key_exists('hide.from.discovery', $metadata) && $metadata['hide.from.discovery'] === true) {
                unset($list[$entity]);
            }
        }
        return $list;
    }


    /**
     * Check if an IdP is set or if the request is passive, and redirect accordingly.
     *
     * @return void If there is no IdP targeted and this is not a passive request.
     */
    protected function start()
    {
        $idp = $this->getTargetIdP();
        if ($idp !== null) {
            $extDiscoveryStorage = $this->config->getString('idpdisco.extDiscoveryStorage', null);
            if ($extDiscoveryStorage !== null) {
                $this->log('Choice made [' . $idp . '] (Forwarding to external discovery storage)');
                Utils\HTTP::redirectTrustedURL($extDiscoveryStorage, [
                    'entityID'      => $this->spEntityId,
                    'IdPentityID'   => $idp,
                    'returnIDParam' => $this->returnIdParam,
                    'isPassive'     => 'true',
                    'return'        => $this->returnURL
                ]);
            } else {
                $this->log(
                    'Choice made [' . $idp . '] (Redirecting the user back. returnIDParam='
                    . $this->returnIdParam . ')'
                );
                Utils\HTTP::redirectTrustedURL($this->returnURL, [$this->returnIdParam => $idp]);
            }
        }

        if ($this->isPassive) {
            $this->log('Choice not made. (Redirecting the user back without answer)');
            Utils\HTTP::redirectTrustedURL($this->returnURL);
        }
    }


    /**
     * Handles a request to this discovery service.
     *
     * The IdP disco parameters should be set before calling this function.
     * @return void
     */
    public function handleRequest()
    {
        $this->start();

        // no choice made. Show discovery service page
        $idpList = $this->getIdPList();
        $idpList = $this->filterList($idpList);
        $preferredIdP = $this->getRecommendedIdP();

        $idpintersection = array_intersect(array_keys($idpList), $this->getScopedIDPList());
        if (sizeof($idpintersection) > 0) {
            $idpList = array_intersect_key($idpList, array_fill_keys($idpintersection, null));
        }

        $idpintersection = array_values($idpintersection);

        if (sizeof($idpintersection) == 1) {
            $this->log(
                'Choice made [' . $idpintersection[0] . '] (Redirecting the user back. returnIDParam=' .
                $this->returnIdParam . ')'
            );
            Utils\HTTP::redirectTrustedURL(
                $this->returnURL,
                [$this->returnIdParam => $idpintersection[0]]
            );
        }

        /*
         * Make use of an XHTML template to present the select IdP choice to the user. Currently the supported options
         * is either a drop down menu or a list view.
         */
        switch ($this->config->getString('idpdisco.layout', 'links')) {
            case 'dropdown':
                $templateFile = 'selectidp-dropdown.php';
                break;
            case 'links':
                $templateFile = 'selectidp-links.php';
                break;
            default:
                throw new \Exception('Invalid value for the \'idpdisco.layout\' option.');
        }

        $t = new Template($this->config, $templateFile, 'disco');

        $fallbackLanguage = 'en';
        $defaultLanguage = $this->config->getString('language.default', $fallbackLanguage);
        $translator = $t->getTranslator();
        $language = $translator->getLanguage()->getLanguage();
        $tryLanguages = [0 => $language, 1 => $defaultLanguage, 2 => $fallbackLanguage];

        $newlist = [];
        foreach ($idpList as $entityid => $data) {
            $newlist[$entityid]['entityid'] = $entityid;
            foreach ($tryLanguages as $lang) {
                if ($name = $this->getEntityDisplayName($data, $lang)) {
                    $newlist[$entityid]['name'] = $name;
                    continue;
                }
            }
            if (empty($newlist[$entityid]['name'])) {
                $newlist[$entityid]['name'] = $entityid;
            }
            foreach ($tryLanguages as $lang) {
                if (!empty($data['description'][$lang])) {
                    $newlist[$entityid]['description'] = $data['description'][$lang];
                    continue;
                }
            }
            if (!empty($data['icon'])) {
                $newlist[$entityid]['icon'] = $data['icon'];
                $newlist[$entityid]['iconurl'] = Utils\HTTP::resolveURL($data['icon']);
            }
        }
        usort(
            $newlist,
            /**
             * @param array $idpentry1
             * @param array $idpentry2
             * @return int
             */
            function (array $idpentry1, array $idpentry2) {
                return strcasecmp($idpentry1['name'], $idpentry2['name']);
            }
        );

        $t->data['idplist'] = $newlist;
        $t->data['preferredidp'] = $preferredIdP;
        $t->data['return'] = $this->returnURL;
        $t->data['returnIDParam'] = $this->returnIdParam;
        $t->data['entityID'] = $this->spEntityId;
        $t->data['urlpattern'] = htmlspecialchars(Utils\HTTP::getSelfURLNoQuery());
        $t->data['rememberenabled'] = $this->config->getBoolean('idpdisco.enableremember', false);
        $t->data['rememberchecked'] = $this->config->getBoolean('idpdisco.rememberchecked', false);
        $t->show();
    }


    /**
     * @param array $idpData
     * @param string $language
     * @return string|null
     */
    private function getEntityDisplayName(array $idpData, string $language): ?string
    {
        if (isset($idpData['UIInfo']['DisplayName'][$language])) {
            return $idpData['UIInfo']['DisplayName'][$language];
        } elseif (isset($idpData['name'][$language])) {
            return $idpData['name'][$language];
        } elseif (isset($idpData['OrganizationDisplayName'][$language])) {
            return $idpData['OrganizationDisplayName'][$language];
        }
        return null;
    }
}
