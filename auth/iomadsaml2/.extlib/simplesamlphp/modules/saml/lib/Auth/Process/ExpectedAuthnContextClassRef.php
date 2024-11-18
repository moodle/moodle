<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\Auth\Process;

use SimpleSAML\Auth;
use SimpleSAML\Error;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Utils;

/**
 * Attribute filter to validate AuthnContextClassRef values.
 *
 * Example configuration:
 *
 * 91 => array(
 *      'class' => 'saml:ExpectedAuthnContextClassRef',
 *      'accepted' => array(
 *         'urn:oasis:names:tc:SAML:2.0:post:ac:classes:nist-800-63:3',
 *         'urn:oasis:names:tc:SAML:2.0:ac:classes:Password',
 *         ),
 *       ),
 *
 * @package SimpleSAMLphp
 */

class ExpectedAuthnContextClassRef extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Array of accepted AuthnContextClassRef
     * @var array
     */
    private $accepted;


    /**
     * AuthnContextClassRef of the assertion
     * @var string|null
     */
    private $AuthnContextClassRef = null;


    /**
     * Initialize this filter, parse configuration
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception if the mandatory 'accepted' configuration option is missing.
     */
    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));
        if (empty($config['accepted'])) {
            Logger::error(
                'ExpectedAuthnContextClassRef: Configuration error. There is no accepted AuthnContextClassRef.'
            );
            throw new Error\Exception(
                'ExpectedAuthnContextClassRef: Configuration error. There is no accepted AuthnContextClassRef.'
            );
        }
        $this->accepted = $config['accepted'];
    }


    /**
     *
     * @param array &$request The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('saml:sp:State', $request));

        $this->AuthnContextClassRef = $request['saml:sp:State']['saml:sp:AuthnContext'];

        if (!in_array($this->AuthnContextClassRef, $this->accepted, true)) {
            $this->unauthorized($request);
        }
    }


    /**
     * When the process logic determines that the user is not
     * authorized for this service, then forward the user to
     * an 403 unauthorized page.
     *
     * Separated this code into its own method so that child
     * classes can override it and change the action. Forward
     * thinking in case a "chained" ACL is needed, more complex
     * permission logic.
     *
     * @param array $request
     * @return void
     */
    protected function unauthorized(&$request)
    {
        Logger::error(
            'ExpectedAuthnContextClassRef: Invalid authentication context: ' . strval($this->AuthnContextClassRef) .
            '. Accepted values are: ' . var_export($this->accepted, true)
        );

        $id = Auth\State::saveState($request, 'saml:ExpectedAuthnContextClassRef:unauthorized');
        $url = Module::getModuleURL(
            'saml/sp/wrong_authncontextclassref.php'
        );
        Utils\HTTP::redirectTrustedURL($url, ['StateId' => $id]);
    }
}
