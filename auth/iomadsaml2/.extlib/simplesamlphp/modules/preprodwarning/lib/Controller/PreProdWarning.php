<?php

namespace SimpleSAML\Module\preprodwarning\Controller;

use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\HTTP\RunnableResponse;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class for the preprodwarning module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-preprodwarning
 */
class PreProdWarning
{
    /**
     * @var \SimpleSAML\Auth\State|string
     * @psalm-var \SimpleSAML\Auth\State|class-string
     */
    protected $authState = Auth\State::class;

    /**
     * @var \SimpleSAML\Auth\ProcessingChain|string
     * @psalm-var \SimpleSAML\Auth\ProcessingChain|class-string
     */
    protected $procChain = Auth\ProcessingChain::class;

    /**
     * @var \SimpleSAML\Logger|string
     * @psalm-var \SimpleSAML\Logger|class-string
     */
    protected $logger = Logger::class;

    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Session */
    protected $session;


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
     * Inject the \SimpleSAML\Auth\ProcessingChain dependency.
     *
     * @param \SimpleSAML\Auth\ProcessingChain $procChain
     */
    public function setProcessingChain(Auth\ProcessingChain $procChain)
    {
        $this->procChain = $procChain;
    }


    /**
     * Inject the \SimpleSAML\Logger dependency.
     *
     * @param \SimpleSAML\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Show warning.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function main(Request $request)
    {
        $this->logger::info('PreProdWarning - Showing warning to user');

        $id = $request->get('StateId', null);
        if ($id === null) {
            throw new Error\BadRequest('Missing required StateId query parameter.');
        }

        /** @psalm-var array $state */
        $state = $this->authState::loadState($id, 'warning:request');

        if ($request->get('yes')) {
            // The user has pressed the yes-button
            return new RunnableResponse([$this->procChain, 'resumeProcessing'], [$state]);
        }

        $t = new Template($this->config, 'preprodwarning:warning.twig');
        $t->data['yesTarget'] = Module::getModuleURL('preprodwarning/warning');
        $t->data['yesData'] = ['StateId' => $id];
        return $t;
    }
}
