<?php

namespace SimpleSAML\Module\authtwitter\Auth\Source;

$default = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/oauth/libextinc/OAuth.php';
$travis = dirname(dirname(dirname(dirname(__FILE__)))).'/vendor/simplesamlphp/simplesamlphp/modules/oauth/libextinc/OAuth.php';

if (file_exists($default)) {
    require_once($default);
} else if (file_exists($travis)) {
    require_once($travis);
} else {
    // Probably codecov, but we can't raise an exception here or Travis will fail
}

/**
 * Authenticate using Twitter.
 *
 * @author Andreas Åkre Solberg, UNINETT AS.
 * @package SimpleSAMLphp
 */

class Twitter extends \SimpleSAML\Auth\Source
{
    /**
     * The string used to identify our states.
     */
    const STAGE_INIT = 'twitter:init';

    /**
     * The key of the AuthId field in the state.
     */
    const AUTHID = 'twitter:AuthId';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var bool
     */
    private $force_login;

    /**
     * @var bool
     */
    private $include_email;

    /**
     * Constructor for this authentication source.
     *
     * @param array $info  Information about this authentication source.
     * @param array $config  Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        $configObject = \SimpleSAML\Configuration::loadFromArray(
            $config,
            'authsources['.var_export($this->authId, true).']'
        );

        $this->key = $configObject->getString('key');
        $this->secret = $configObject->getString('secret');
        $this->force_login = $configObject->getBoolean('force_login', false);
        $this->include_email = $configObject->getBoolean('include_email', false);
    }

    /**
     * Log-in using Twitter platform
     *
     * @param array &$state  Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));

        // We are going to need the authId in order to retrieve this authentication source later
        $state[self::AUTHID] = $this->authId;

        $stateID = \SimpleSAML\Auth\State::saveState($state, self::STAGE_INIT);

        $consumer = new \SimpleSAML\Module\oauth\Consumer($this->key, $this->secret);
        // Get the request token
        $linkback = \SimpleSAML\Module::getModuleURL('authtwitter/linkback.php', ['AuthState' => $stateID]);
        $requestToken = $consumer->getRequestToken(
            'https://api.twitter.com/oauth/request_token',
            ['oauth_callback' => $linkback]
        );
        \SimpleSAML\Logger::debug("Got a request token from the OAuth service provider [".
            $requestToken->key."] with the secret [".$requestToken->secret."]");

        $state['authtwitter:authdata:requestToken'] = $requestToken;
        \SimpleSAML\Auth\State::saveState($state, self::STAGE_INIT);

        // Authorize the request token
        $url = 'https://api.twitter.com/oauth/authenticate';
        if ($this->force_login) {
            $url = \SimpleSAML\Utils\HTTP::addURLParameters($url, ['force_login' => 'true']);
        }
        $consumer->getAuthorizeRequest($url, $requestToken);
    }


    /**
     * @param array &$state
     * @return void
     */
    public function finalStep(&$state)
    {
        $requestToken = $state['authtwitter:authdata:requestToken'];
        $parameters = [];

        if (!isset($_REQUEST['oauth_token'])) {
            throw new \SimpleSAML\Error\BadRequest("Missing oauth_token parameter.");
        }
        if ($requestToken->key !== (string) $_REQUEST['oauth_token']) {
            throw new \SimpleSAML\Error\BadRequest("Invalid oauth_token parameter.");
        }

        if (!isset($_REQUEST['oauth_verifier'])) {
            throw new \SimpleSAML\Error\BadRequest("Missing oauth_verifier parameter.");
        }
        $parameters['oauth_verifier'] = (string) $_REQUEST['oauth_verifier'];

        $consumer = new \SimpleSAML\Module\oauth\Consumer($this->key, $this->secret);

        \SimpleSAML\Logger::debug("oauth: Using this request token [".
            $requestToken->key."] with the secret [".$requestToken->secret."]");

        // Replace the request token with an access token
        $accessToken = $consumer->getAccessToken(
            'https://api.twitter.com/oauth/access_token',
            $requestToken,
            $parameters
        );
        \SimpleSAML\Logger::debug("Got an access token from the OAuth service provider [".
            $accessToken->key."] with the secret [".$accessToken->secret."]");

        $verify_credentials_url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
        if ($this->include_email) {
            $verify_credentials_url = $verify_credentials_url.'?include_email=true';
        }
        $userdata = $consumer->getUserInfo($verify_credentials_url, $accessToken);

        if (!isset($userdata['id_str']) || !isset($userdata['screen_name'])) {
            throw new \SimpleSAML\Error\AuthSource(
                $this->authId,
                'Authentication error: id_str and screen_name not set.'
            );
        }

        $attributes = [];
        foreach ($userdata as $key => $value) {
            if (is_string($value)) {
                $attributes['twitter.'.$key] = [(string) $value];
            }
        }

        $attributes['twitter_at_screen_name'] = ['@'.$userdata['screen_name']];
        $attributes['twitter_screen_n_realm'] = [$userdata['screen_name'].'@twitter.com'];
        $attributes['twitter_targetedID'] = ['http://twitter.com!'.$userdata['id_str']];

        $state['Attributes'] = $attributes;
    }
}
