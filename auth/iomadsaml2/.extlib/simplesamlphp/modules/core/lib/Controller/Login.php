<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Controller;

use SimpleSAML\Auth;
use SimpleSAML\Auth\AuthenticationFactory;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\HTTP\RunnableResponse;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Controller class for the core module.
 *
 * This class serves the different views available in the module.
 *
 * @package SimpleSAML\Module\core
 */
class Login
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Auth\AuthenticationFactory */
    protected $factory;

    /** @var \SimpleSAML\Session */
    protected $session;

    /** @var array */
    protected $sources;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and auth source configuration for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration              $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session                    $session The session to use by the controllers.
     * @param \SimpleSAML\Auth\AuthenticationFactory $factory A factory to instantiate \SimpleSAML\Auth\Simple objects.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config,
        Session $session,
        AuthenticationFactory $factory
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->sources = $config::getOptionalConfig('authsources.php')->toArray();
        $this->session = $session;
    }


    /**
     * Show account information for a given authentication source.
     *
     * @param string $as The identifier of the authentication source.
     *
     * @return \SimpleSAML\XHTML\Template|\Symfony\Component\HttpFoundation\RedirectResponse
     * An HTML template or a redirection if we are not authenticated.
     *
     * @throws \SimpleSAML\Error\Exception An exception in case the auth source specified is invalid.
     */
    public function account(string $as): Response
    {
        if (!array_key_exists($as, $this->sources)) {
            throw new Error\Exception('Invalid authentication source');
        }

        $auth = $this->factory->create($as);
        if (!$auth->isAuthenticated()) {
            // not authenticated, start auth with specified source
            return new RedirectResponse(Module::getModuleURL('core/login/' . urlencode($as)));
        }

        $attributes = $auth->getAttributes();

        $session = Session::getSessionFromRequest();

        $t = new Template($this->config, 'auth_status.twig', 'attributes');
        $l = $t->getLocalization();
        $l->addDomain($l->getLocaleDir(), 'attributes');
        $t->data['header'] = '{status:header_saml20_sp}';
        $t->data['attributes'] = $attributes;
        $t->data['nameid'] = !is_null($auth->getAuthData('saml:sp:NameID'))
            ? $auth->getAuthData('saml:sp:NameID')
            : false;
        $t->data['authData'] = $auth->getAuthDataArray();
        $t->data['trackid'] = $session->getTrackID();
        $t->data['logouturl'] = Module::getModuleURL('core/logout/' . urlencode($as));
        $t->data['remaining'] = $this->session->getAuthData($as, 'Expire') - time();
        $t->setStatusCode(200);

        return $t;
    }


    /**
     * Perform a login operation.
     *
     * This controller will either start a login operation (if that was requested, or if only one authentication
     * source is available), or show a template allowing the user to choose which auth source to use.
     *
     * @param Request $request The request that lead to this login operation.
     * @param string|null $as The name of the authentication source to use, if any. Optional.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * An HTML template, a redirect or a "runnable" response.
     *
     * @throws \SimpleSAML\Error\Exception
     */
    public function login(Request $request, string $as = null): Response
    {
        // delete admin
        if (isset($this->sources['admin'])) {
            unset($this->sources['admin']);
        }

        if (count($this->sources) === 1 && $as === null) { // we only have one source available
            $as = key($this->sources);
        }

        $default = false;
        if (array_key_exists('default', $this->sources) && is_array($this->sources['default'])) {
            $default = $this->sources['default'];
        }

        if ($as === null) { // no authentication source specified
            if (!$default) {
                $t = new Template($this->config, 'core:login.twig');
                $t->data['loginurl'] = Utils\Auth::getAdminLoginURL();
                $t->data['sources'] = $this->sources;
                return $t;
            }

            // we have a default, use that one
            $as = 'default';
            foreach ($this->sources as $id => $source) {
                if ($id === 'default') {
                    continue;
                }
                if ($source === $this->sources['default']) {
                    $as = $id;
                    break;
                }
            }
        }

        // auth source defined, check if valid
        if (!array_key_exists($as, $this->sources)) {
            throw new Error\Exception('Invalid authentication source');
        }

        // at this point, we have a valid auth source selected, start auth
        $auth = $this->factory->create($as);
        $as = urlencode($as);

        if ($request->get(Auth\State::EXCEPTION_PARAM, false) !== false) {
            // This is just a simple example of an error

            /** @var array $state */
            $state = Auth\State::loadExceptionState();

            Assert::keyExists($state, Auth\State::EXCEPTION_DATA);
            $e = $state[Auth\State::EXCEPTION_DATA];

            throw $e;
        }

        if ($auth->isAuthenticated()) {
            return new RedirectResponse(Module::getModuleURL('core/account/' . $as));
        }

        // we're not logged in, start auth
        $url = Module::getModuleURL('core/login/' . $as);
        $params = array(
            'ErrorURL' => $url,
            'ReturnTo' => $url,
        );
        return new RunnableResponse([$auth, 'login'], [$params]);
    }


    /**
     * Log the user out of a given authentication source.
     *
     * @param string $as The name of the auth source.
     *
     * @return \SimpleSAML\HTTP\RunnableResponse A runnable response which will actually perform logout.
     *
     * @throws \SimpleSAML\Error\CriticalConfigurationError
     */
    public function logout(string $as): Response
    {
        $auth = new Auth\Simple($as);
        return new RunnableResponse(
            [$auth, 'logout'],
            [$this->config->getBasePath()]
        );
    }


    /**
     * This clears the user's IdP discovery choices.
     *
     * @param Request $request The request that lead to this login operation.
     * @return void
     */
    public function cleardiscochoices(Request $request)
    {
        // The base path for cookies. This should be the installation directory for SimpleSAMLphp.
        $cookiePath = $this->config->getBasePath();

        // We delete all cookies which starts with 'idpdisco_'
        foreach ($request->cookies->all() as $cookieName => $value) {
            if (substr($cookieName, 0, 9) !== 'idpdisco_') {
                // Not a idpdisco cookie.
                continue;
            }

            Utils\HTTP::setCookie($cookieName, null, ['path' => $cookiePath, 'httponly' => false], false);
        }

        // Find where we should go now.
        $returnTo = $request->get('ReturnTo', false);
        if ($returnTo !== false) {
            $returnTo = Utils\HTTP::checkURLAllowed($returnTo);
        } else {
            // Return to the front page if no other destination is given. This is the same as the base cookie path.
            $returnTo = $cookiePath;
        }

        // Redirect to destination.
        Utils\HTTP::redirectTrustedURL($returnTo);
    }
}
