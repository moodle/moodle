<?php

namespace SimpleSAML\Module\authX509\Controller;

use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\HTTP\RunnableResponse;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Module\authX509\Auth\Source\X509userCert;
use SimpleSAML\Session;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class for the authx509 module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-authx509
 */
class ExpiryWarning
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Session */
    protected $session;

    /**
     * @var \SimpleSAML\Auth\State|string
     * @psalm-var \SimpleSAML\Auth\State|class-string
     */
    protected $authState = Auth\State::class;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and session for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session $session The session to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config,
        Session $session
    ) {
        $this->config = $config;
        $this->session = $session;
    }


    /**
     * Inject the \SimpleSAML\Auth\State dependency.
     *
     * @param \SimpleSAML\Auth\State $authState
     */
    public function setAuthState(Auth\State $authState)
    {
        $this->authState = $authState;
    }


    /**
     * Show expiry warning.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \SimpleSAML\XHTML\Template|\SimpleSAML\HTTP\RunnableResponse
     * @throws \Exception
     */
    public function main(Request $request)
    {
        Logger::info('AuthX509 - Showing expiry warning to user');

        $id = $request->get('StateId', null);
        if ($id === null) {
            throw new Error\BadRequest('Missing required StateId query parameter.');
        }

        $authState = $this->authState;
        $state = $authState::loadState($id, 'warning:expire');

        if (is_null($state)) {
            throw new Error\NoState();
        } elseif ($request->get('proceed', null) !== null) {
            // The user has pressed the proceed-button
            return new RunnableResponse([Auth\ProcessingChain::class, 'resumeProcessing'], [$state]);
        }

        $t = new Template($this->config, 'authX509:X509warning.twig');
        $t->data['target'] = Module::getModuleURL('authX509/expirywarning.php');
        $t->data['data'] = ['StateId' => $id];
        $t->data['daysleft'] = $state['daysleft'];
        $t->data['renewurl'] = $state['renewurl'];
        $t->data['errorcodes'] = Error\ErrorCodes::getAllErrorCodeMessages();
        return $t;
    }
}
