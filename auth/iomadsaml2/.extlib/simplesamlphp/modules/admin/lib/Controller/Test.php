<?php

declare(strict_types=1);

namespace SimpleSAML\Module\admin\Controller;

use SAML2\Constants;
use SAML2\XML\saml\NameID;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\HTTP\RunnableResponse;
use SimpleSAML\Locale\Translate;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Controller class for the admin module.
 *
 * This class serves the 'Test authentication sources' views available in the module.
 *
 * @package SimpleSAML\Module\admin
 */
class Test
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var Menu */
    protected $menu;

    /** @var \SimpleSAML\Session */
    protected $session;


    /**
     * ConfigController constructor.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use.
     * @param \SimpleSAML\Session $session The current user session.
     */
    public function __construct(Configuration $config, Session $session)
    {
        $this->config = $config;
        $this->session = $session;
        $this->menu = new Menu();
    }


    /**
     * Display the list of available authsources.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string|null $as
     * @return \SimpleSAML\XHTML\Template
     */
    public function main(Request $request, $as)
    {
        Utils\Auth::requireAdmin();
        if (is_null($as)) {
            $t = new Template($this->config, 'admin:authsource_list.twig');
            $t->data = [
                'sources' => Auth\Source::getSources(),
            ];
        } else {
            $authsource = new Auth\Simple($as);
            if (!is_null($request->query->get('logout'))) {
                $authsource->logout($this->config->getBasePath() . 'logout.php');
            } elseif (!is_null($request->query->get(Auth\State::EXCEPTION_PARAM))) {
                // This is just a simple example of an error
                /** @var array $state */
                $state = Auth\State::loadExceptionState();
                Assert::keyExists($state, Auth\State::EXCEPTION_DATA);
                throw $state[Auth\State::EXCEPTION_DATA];
            }

            if (!$authsource->isAuthenticated()) {
                $url = Module::getModuleURL('admin/test/' . $as, []);
                $params = [
                    'ErrorURL' => $url,
                    'ReturnTo' => $url,
                ];
                $authsource->login($params);
            }

            $attributes = $authsource->getAttributes();
            $authData = $authsource->getAuthDataArray();
            $nameId = !is_null($authsource->getAuthData('saml:sp:NameID'))
                ? $authsource->getAuthData('saml:sp:NameID')
                : false;

            $t = new Template($this->config, 'admin:status.twig', 'attributes');
            $t->data = [
                'attributes' => $attributes,
                'attributesHtml' => $this->getAttributesHTML($t, $attributes, ''),
                'authData' => $authData,
                'nameid' => $nameId,
                'logouturl' => Utils\HTTP::getSelfURLNoQuery() . '?as=' . urlencode($as) . '&logout',
            ];

            if ($nameId !== false) {
                $t->data['nameidHtml'] = $this->getNameIDHTML($t, $nameId);
            }
        }

        Module::callHooks('configpage', $t);
        $this->menu->addOption('logout', Utils\Auth::getAdminLogoutURL(), Translate::noop('Log out'));
        return $this->menu->insert($t);
    }


    /**
     * @param \SimpleSAML\XHTML\Template $t
     * @param \SAML2\XML\saml\NameID $nameId
     * @return string
     */
    private function getNameIDHTML(Template $t, NameID $nameId): string
    {
        $translator = $t->getTranslator();
        $result = '';

        /** @psalm-suppress TypeDoesNotContainNull  Remove if-case in 2.0 */
        if ($nameId->getValue() === null) {
            $list = ["NameID" => [$translator->t('{status:subject_notset}')]];
            /** @var string $notset */
            $notset = $translator->t('{status:subject_notset}');
            $result .= "<p>NameID: <span class=\"notset\">" . $notset . "</span></p>";
        } else {
            $list = [
                "NameId" => [$nameId->getValue()],
            ];
            if ($nameId->getFormat() !== null) {
                /** @var string $format */
                $format = $translator->getPreferredTranslation(
                    $translator->getTag('{status:subject_format}') ?? ['en'=>'Format']
                );
                $list[$format] = [$nameId->getFormat()];
            }
            if ($nameId->getNameQualifier() !== null) {
                $list['NameQualifier'] = [$nameId->getNameQualifier()];
            }
            if ($nameId->getSPNameQualifier() !== null) {
                $list['SPNameQualifier'] = [$nameId->getSPNameQualifier()];
            }
            if ($nameId->getSPProvidedID() !== null) {
                $list['SPProvidedID'] = [$nameId->getSPProvidedID()];
            }
        }
        return $result . $this->getAttributesHTML($t, $list, '');
    }


    /**
     * @param \SimpleSAML\XHTML\Template $t
     * @param array $attributes
     * @param string $nameParent
     * @return string
     */
    private function getAttributesHTML(Template $t, array $attributes, string $nameParent): string
    {
        $alternate = ['pure-table-odd', 'pure-table-even'];
        $i = 0;
        $parentStr = (strlen($nameParent) > 0) ? strtolower($nameParent) . '_' : '';
        $str = (strlen($nameParent) > 0)
            ? '<table class="pure-table pure-table-attributes" summary="attribute overview">'
            : '<table id="table_with_attributes" class="pure-table pure-table-attributes"'
            . ' summary="attribute overview">';
        foreach ($attributes as $name => $value) {
            $nameraw = $name;
            $trans = $t->getTranslator();
            $name = $trans->getAttributeTranslation($parentStr . $nameraw);
            if (preg_match('/^child_/', $nameraw)) {
                $parentName = preg_replace('/^child_/', '', $nameraw);
                foreach ($value as $child) {
                    $str .= '<tr class="odd"><td colspan="2" style="padding: 2em">' .
                        $this->getAttributesHTML($t, $child, $parentName) . '</td></tr>';
                }
            } else {
                if (sizeof($value) > 1) {
                    $str .= '<tr class="' . $alternate[($i++ % 2)] . '"><td class="attrname">';
                    if ($nameraw !== $name) {
                        $str .= htmlspecialchars($name) . '<br/>';
                    }
                    $str .= '<code>' . htmlspecialchars($nameraw) . '</code>';
                    $str .= '</td><td class="attrvalue"><ul>';
                    foreach ($value as $listitem) {
                        if ($nameraw === 'jpegPhoto') {
                            $str .= '<li><img src="data:image/jpeg;base64,' . htmlspecialchars($listitem) . '" /></li>';
                        } else {
                            $str .= '<li>' . $this->presentAssoc($listitem) . '</li>';
                        }
                    }
                    $str .= '</ul></td></tr>';
                } elseif (isset($value[0])) {
                    $str .= '<tr class="' . $alternate[($i++ % 2)] . '"><td class="attrname">';
                    if ($nameraw !== $name) {
                        $str .= htmlspecialchars($name) . '<br/>';
                    }
                    $str .= '<code>' . htmlspecialchars($nameraw) . '</code>';
                    $str .= '</td>';
                    if ($nameraw === 'jpegPhoto') {
                        $str .= '<td class="attrvalue"><img src="data:image/jpeg;base64,' . htmlspecialchars($value[0])
                            . '" /></td></tr>';
                    } elseif (is_a($value[0], 'DOMNodeList')) {
                        // try to see if we have a NameID here
                        /** @var \DOMNodeList $value[0] */
                        $n = $value[0]->length;
                        for ($idx = 0; $idx < $n; $idx++) {
                            $elem = $value[0]->item($idx);
                            /* @var \DOMElement $elem */
                            if (!($elem->localName === 'NameID' && $elem->namespaceURI === Constants::NS_SAML)) {
                                continue;
                            }
                            $str .= $this->presentEptid($trans, new NameID($elem));
                            break; // we only support one NameID here
                        }
                        $str .= '</td></tr>';
                    } elseif (is_a($value[0], '\SAML2\XML\saml\NameID')) {
                        $str .= $this->presentEptid($trans, $value[0]);
                        $str .= '</td></tr>';
                    } else {
                        $str .= '<td class="attrvalue">' . htmlspecialchars($value[0]) . '</td></tr>';
                    }
                }
            }
            $str .= "\n";
        }
        $str .= '</table>';
        return $str;
    }


    /**
     * @param array|string $attr
     * @return string
     */
    private function presentList($attr): string
    {
        if (is_array($attr) && count($attr) > 1) {
            $str = '<ul>';
            foreach ($attr as $value) {
                $str .= '<li>' . htmlspecialchars(strval($attr)) . '</li>';
            }
            $str .= '</ul>';
            return $str;
        } else {
            return htmlspecialchars($attr[0]);
        }
    }


    /**
     * @param array|string $attr
     * @return string
     */
    private function presentAssoc($attr): string
    {
        if (is_array($attr)) {
            $str = '<dl>';
            foreach ($attr as $key => $value) {
                $str .= "\n" . '<dt>' . htmlspecialchars($key) . '</dt><dd>' . $this->presentList($value) . '</dd>';
            }
            $str .= '</dl>';
            return $str;
        } else {
            return htmlspecialchars($attr);
        }
    }


    /**
     * @param \SimpleSAML\Locale\Translate $t
     * @param \SAML2\XML\saml\NameID $nameID
     * @return string
     */
    private function presentEptid(Translate $t, NameID $nameID): string
    {
        $eptid = [
            'NameID' => [$nameID->getValue()],
        ];
        if ($nameID->getFormat() !== null) {
            $format = $t->getPreferredTranslation(
                $t->getTag('{status:subject_format}') ?? ['en' => 'Format']
            );
            $eptid[$format] = [$nameID->getFormat()];
        }
        if ($nameID->getNameQualifier() !== null) {
            $eptid['NameQualifier'] = [$nameID->getNameQualifier()];
        }
        if ($nameID->getSPNameQualifier() !== null) {
            $eptid['SPNameQualifier'] = [$nameID->getSPNameQualifier()];
        }
        if ($nameID->getSPProvidedID() !== null) {
            $eptid['SPProvidedID'] = [$nameID->getSPProvidedID()];
        }
        return '<td class="attrvalue">' . $this->presentAssoc($eptid);
    }
}
