<?php

namespace SimpleSAML\module\cdc\Auth\Process;

/**
 * Filter for setting the SAML 2 common domain cookie.
 *
 * @package SimpleSAMLphp
 */

class CDC extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Our CDC domain.
     *
     * @var string
     */
    private $domain;


    /**
     * Our CDC client.
     *
     * @var \SimpleSAML\Module\cdc\Client
     */
    private $client;


    /**
     * Initialize this filter.
     *
     * @param array $config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));

        if (!isset($config['domain'])) {
            throw new \SimpleSAML\Error\Exception('Missing domain option in cdc:CDC filter.');
        }
        $this->domain = (string) $config['domain'];

        $this->client = new \SimpleSAML\Module\cdc\Client($this->domain);
    }


    /**
     * Redirect to page setting CDC.
     *
     * @param array &$state  The request state.
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));

        if (!isset($state['Source']['entityid'])) {
            \SimpleSAML\Logger::warning('saml:CDC: Could not find IdP entityID.');
            return;
        }

        // Save state and build request
        $id = \SimpleSAML\Auth\State::saveState($state, 'cdc:resume');

        $returnTo = \SimpleSAML\Module::getModuleURL('cdc/resume.php', ['domain' => $this->domain]);

        $params = [
            'id' => $id,
            'entityID' => $state['Source']['entityid'],
        ];
        $this->client->sendRequest($returnTo, 'append', $params);
    }
}
