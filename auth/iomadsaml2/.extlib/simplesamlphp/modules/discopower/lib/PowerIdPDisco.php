<?php

namespace SimpleSAML\Module\discopower;

use SimpleSAML\Configuration;
use SimpleSAML\Locale\Translate;
use SimpleSAML\Logger;
use SimpleSAML\Session;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\XHTML\Template;
use Webmozart\Assert\Assert;

/**
 * This class implements a generic IdP discovery service, for use in various IdP discovery service pages. This should
 * reduce code duplication.
 *
 * This module extends the basic IdP disco handler, and add features like filtering and tabs.
 *
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */
class PowerIdPDisco extends \SimpleSAML\XHTML\IdPDisco
{
    /**
     * The configuration for this instance.
     *
     * @var \SimpleSAML\Configuration
     */
    private $discoconfig;


    /**
     * The domain to use when saving common domain cookies. This is null if support for common domain cookies is
     * disabled.
     *
     * @var string|null
     */
    private $cdcDomain;


    /**
     * The lifetime of the CDC cookie, in seconds. If set to null, it will only be valid until the browser is closed.
     *
     * @var int|null
     */
    private $cdcLifetime;


    /**
     * Initializes this discovery service.
     *
     * The constructor does the parsing of the request. If this is an invalid request, it will throw an exception.
     *
     * @param array  $metadataSets Array with metadata sets we find remote entities in.
     * @param string $instance The name of this instance of the discovery service.
     */
    public function __construct(array $metadataSets, $instance)
    {
        parent::__construct($metadataSets, $instance);

        $this->discoconfig = Configuration::getConfig('module_discopower.php');

        $this->cdcDomain = $this->discoconfig->getString('cdc.domain', null);
        if ($this->cdcDomain !== null && $this->cdcDomain[0] !== '.') {
            // ensure that the CDC domain starts with a dot ('.') as required by the spec
            $this->cdcDomain = '.' . $this->cdcDomain;
        }

        $this->cdcLifetime = $this->discoconfig->getInteger('cdc.lifetime', null);
    }


    /**
     * Log a message.
     *
     * This is an helper function for logging messages. It will prefix the messages with our discovery service type.
     *
     * @param string $message The message which should be logged.
     * @return void
     */
    protected function log($message)
    {
        Logger::info('PowerIdPDisco.' . $this->instance . ': '.$message);
    }


    /**
     * Compare two entities.
     *
     * This function is used to sort the entity list. It sorts based on english name, and will always put IdP's with
     * names configured before those with only an entityID.
     *
     * @param array $a The metadata of the first entity.
     * @param array $b The metadata of the second entity.
     *
     * @return int How $a compares to $b.
     */
    public static function mcmp(array $a, array $b)
    {
        if (isset($a['name']['en']) && isset($b['name']['en'])) {
            return strcasecmp($a['name']['en'], $b['name']['en']);
        } elseif (isset($a['name']['en'])) {
            return -1; // place name before entity ID
        } elseif (isset($b['name']['en'])) {
            return 1; // Place entity ID after name
        } else {
            return strcasecmp($a['entityid'], $b['entityid']);
        }
    }


    /**
     * Structure the list of IdPs in a hierarchy based upon the tags.
     *
     * @param array $list A list of IdPs.
     *
     * @return array The list of IdPs structured accordingly.
     */
    protected function idplistStructured($list)
    {
        $slist = [];

        $order = $this->discoconfig->getValue('taborder');
        if (is_array($order)) {
            foreach ($order as $oe) {
                $slist[$oe] = [];
            }
        }

        $enableTabs = $this->discoconfig->getValue('tabs', null);

        foreach ($list as $key => $val) {
            $tags = ['misc'];
            if (array_key_exists('tags', $val)) {
                $tags = $val['tags'];
            }
            foreach ($tags as $tag) {
                if (!empty($enableTabs) && !in_array($tag, $enableTabs)) {
                    continue;
                }
                $slist[$tag][$key] = $val;
            }
        }

        foreach ($slist as $tab => $tbslist) {
            uasort($slist[$tab], [self::class, 'mcmp']);
        }

        return $slist;
    }


    /**
     * Do the actual filtering according the rules defined.
     *
     * @param array   $filter A set of rules regarding filtering.
     * @param array   $entry An entry to be evaluated by the filters.
     * @param boolean $default What to do in case the entity does not match any rules. Defaults to true.
     *
     * @return boolean True if the entity should be kept, false if it should be discarded according to the filters.
     */
    private function processFilter($filter, $entry, $default = true)
    {
        if (in_array($entry['entityid'], $filter['entities.include'])) {
            return true;
        }
        if (in_array($entry['entityid'], $filter['entities.exclude'])) {
            return false;
        }

        if (array_key_exists('tags', $entry)) {
            foreach ($filter['tags.include'] as $fe) {
                if (in_array($fe, $entry['tags'])) {
                    return true;
                }
            }
            foreach ($filter['tags.exclude'] as $fe) {
                if (in_array($fe, $entry['tags'])) {
                    return false;
                }
            }
        }
        return $default;
    }


    /**
     * Filter a list of entities according to any filters defined in the parent class, plus discopower configuration
     * options regarding filtering.
     *
     * @param array $list A list of entities to filter.
     *
     * @return array The list in $list after filtering entities.
     */
    protected function filterList($list)
    {
        $list = parent::filterList($list);

        try {
            $spmd = $this->metadata->getMetaData($this->spEntityId, 'saml20-sp-remote');
        } catch (\Exception $e) {
            return $list;
        }

        if (!isset($spmd)) {
            return $list;
        }
        if (!array_key_exists('discopower.filter', $spmd)) {
            return $list;
        }
        $filter = $spmd['discopower.filter'];

        if (!array_key_exists('entities.include', $filter)) {
            $filter['entities.include'] = [];
        }
        if (!array_key_exists('entities.exclude', $filter)) {
            $filter['entities.exclude'] = [];
        }
        if (!array_key_exists('tags.include', $filter)) {
            $filter['tags.include'] = [];
        }
        if (!array_key_exists('tags.exclude', $filter)) {
            $filter['tags.exclude'] = [];
        }

        $defaultrule = true;
        if (
            array_key_exists('entities.include', $spmd['discopower.filter'])
            || array_key_exists('tags.include', $spmd['discopower.filter'])
        ) {
            $defaultrule = false;
        }

        $returnlist = [];
        foreach ($list as $key => $entry) {
            if ($this->processFilter($filter, $entry, $defaultrule)) {
                $returnlist[$key] = $entry;
            }
        }
        return $returnlist;
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
        $idpList = $this->idplistStructured($this->filterList($idpList));
        $preferredIdP = $this->getRecommendedIdP();

        $t = new Template($this->config, 'discopower:disco.tpl.php', 'disco');
        $translator = $t->getTranslator();

        $t->data['return'] = $this->returnURL;
        $t->data['returnIDParam'] = $this->returnIdParam;
        $t->data['entityID'] = $this->spEntityId;
        $t->data['defaulttab'] = $this->discoconfig->getValue('defaulttab', 0);

        $idpList = $this->processMetadata($t, $idpList, $preferredIdP);

        $t->data['idplist'] = $idpList;
        $t->data['faventry'] = null;
        foreach ($idpList as $tab => $slist) {
            if (!empty($preferredIdP) && array_key_exists($preferredIdP, $slist)) {
                $t->data['faventry'] = $slist[$preferredIdP];
                break;
            }
        }

        if (isset($t->data['faventry'])) {
            $t->data['autofocus'] = 'favouritesubmit';
        }

        /* store the tab list in the session */
        $session = Session::getSessionFromRequest();
        if (array_key_exists('faventry', $t->data)) {
            $session->setData('discopower:tabList', 'faventry', $t->data['faventry']);
        }
        $session->setData('discopower:tabList', 'tabs', array_keys($idpList));
        $session->setData('discopower:tabList', 'defaulttab', $t->data['defaulttab']);

        $t->data['score'] = $this->discoconfig->getValue('score', 'quicksilver');
        $t->data['preferredidp'] = $preferredIdP;
        $t->data['urlpattern'] = htmlspecialchars(HTTP::getSelfURLNoQuery());
        $t->data['rememberenabled'] = $this->config->getBoolean('idpdisco.enableremember', false);
        $t->data['rememberchecked'] = $this->config->getBoolean('idpdisco.rememberchecked', false);
        foreach (array_keys($idpList) as $tab) {
            if ($translator->getTag('{discopower:tabs:' . $tab . '}') === null) {
                $translator->includeInlineTranslation('{discopower:tabs:' . $tab . '}', $tab);
            }
            $t->data['tabNames'][$tab] = $translator::noop('{discopower:tabs:' . $tab . '}');
        }
        $t->show();
    }


    /**
     * @param \SimpleSAML\XHTML\Template $t
     * @param array $metadata
     * @param string $favourite
     * @return array
     */
    private function processMetadata($t, $metadata, $favourite)
    {
        $basequerystring = '?'.
            'entityID=' . urlencode($t->data['entityID']) . '&amp;' .
            'return=' . urlencode($t->data['return']) . '&amp;' .
            'returnIDParam=' . urlencode($t->data['returnIDParam']) . '&amp;idpentityid=';

        foreach ($metadata as $tab => $idps) {
            foreach ($idps as $entityid => $entity) {
                $translation = false;

                // Translate name
                if (isset($entity['UIInfo']['DisplayName'])) {
                    $displayName = $entity['UIInfo']['DisplayName'];

                    // Should always be an array of language code -> translation
                    assert(is_array($displayName));

                    if (!empty($displayName)) {
                        $translation = $t->getTranslator()->getPreferredTranslation($displayName);
                    }
                }

                if (($translation === false) && array_key_exists('name', $entity)) {
                    if (is_array($entity['name'])) {
                        $translation = $t->getTranslator()->getPreferredTranslation($entity['name']);
                    } else {
                        $translation = $entity['name'];
                    }
                }

                if ($translation === false) {
                    $translation = $entity['entityid'];
                }
                $entity['translated'] = $translation;

                // HTML output
                if ($entity['entityid'] === $favourite) {
                    $html = '<a class="metaentry favourite" href="' .
                        $basequerystring.urlencode($entity['entityid']) . '">';
                } else {
                    $html = '<a class="metaentry" href="' .
                        $basequerystring.urlencode($entity['entityid']) . '">';
                }
                $html .= $entity['translated'];
                if (array_key_exists('icon', $entity) && $entity['icon'] !== null) {
                    $iconUrl = HTTP::resolveURL($entity['icon']);
                    $html .= '<img alt="Icon for identity provider" class="entryicon" src="' .
                        htmlspecialchars($iconUrl) . '" />';
                }
                $html .= '</a>';
                $entity['html'] = $html;

                // Save processed data
                $metadata[$tab][$entityid] = $entity;
            }
        }
        return $metadata;
    }

    /**
     * Get the IdP entities saved in the common domain cookie.
     *
     * @return array List of IdP entities.
     */
    private function getCDC()
    {
        if (!isset($_COOKIE['_saml_idp'])) {
            return [];
        }

        $ret = (string) $_COOKIE['_saml_idp'];
        $ret = explode(' ', $ret);
        foreach ($ret as &$idp) {
            $idp = base64_decode($idp);
            if ($idp === false) {
                // not properly base64 encoded
                return [];
            }
        }

        return $ret;
    }


    /**
     * Save the current IdP choice to a cookie.
     *
     * This function overrides the corresponding function in the parent class, to add support for common domain cookie.
     *
     * @param string $idp The entityID of the IdP.
     * @return void
     */
    protected function setPreviousIdP($idp)
    {
        assert(is_string($idp));

        if ($this->cdcDomain === null) {
            parent::setPreviousIdP($idp);
            return;
        }

        $list = $this->getCDC();

        $prevIndex = array_search($idp, $list, true);
        if ($prevIndex !== false) {
            unset($list[$prevIndex]);
        }
        $list[] = $idp;

        foreach ($list as &$value) {
            $value = base64_encode($value);
        }
        $newCookie = implode(' ', $list);

        while (strlen($newCookie) > 4000) {
            // the cookie is too long. Remove the oldest elements until it is short enough
            $tmp = explode(' ', $newCookie, 2);
            if (count($tmp) === 1) {
                // we are left with a single entityID whose base64 representation is too long to fit in a cookie
                break;
            }
            $newCookie = $tmp[1];
        }

        $params = [
            'lifetime' => $this->cdcLifetime,
            'domain'   => $this->cdcDomain,
            'secure'   => true,
            'httponly' => false,
        ];
        HTTP::setCookie('_saml_idp', $newCookie, $params, false);
    }


    /**
     * Retrieve the previous IdP the user used.
     *
     * This function overrides the corresponding function in the parent class, to add support for common domain cookie.
     *
     * @return string|null The entity id of the previous IdP the user used, or null if this is the first time.
     */
    protected function getPreviousIdP()
    {
        if ($this->cdcDomain === null) {
            return parent::getPreviousIdP();
        }

        $prevIdPs = $this->getCDC();
        while (count($prevIdPs) > 0) {
            $idp = array_pop($prevIdPs);
            $idp = $this->validateIdP($idp);
            if ($idp !== null) {
                return $idp;
            }
        }

        return null;
    }
}
