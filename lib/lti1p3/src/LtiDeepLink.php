<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiDeepLink
{
    private $registration;
    private $deployment_id;
    private $deep_link_settings;

    public function __construct(ILtiRegistration $registration, string $deployment_id, array $deep_link_settings)
    {
        $this->registration = $registration;
        $this->deployment_id = $deployment_id;
        $this->deep_link_settings = $deep_link_settings;
    }

    public function getResponseJwt($resources)
    {
        $message_jwt = [
            'iss' => $this->registration->getClientId(),
            'aud' => [$this->registration->getIssuer()],
            'exp' => time() + 600,
            'iat' => time(),
            'nonce' => LtiOidcLogin::secureRandomString('nonce-'),
            LtiConstants::DEPLOYMENT_ID => $this->deployment_id,
            LtiConstants::MESSAGE_TYPE => 'LtiDeepLinkingResponse',
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::DL_CONTENT_ITEMS => array_map(function ($resource) { return $resource->toArray(); }, $resources),
        ];

        // https://www.imsglobal.org/spec/lti-dl/v2p0/#deep-linking-request-message
        // 'data' is an optional property which, if it exists, must be returned by the tool
        if (isset($this->deep_link_settings['data'])) {
            $message_jwt[LtiConstants::DL_DATA] = $this->deep_link_settings['data'];
        }

        return JWT::encode($message_jwt, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());
    }

    public function outputResponseForm($resources)
    {
        $jwt = $this->getResponseJwt($resources);
        /*
         * @todo Fix this
         */ ?>
        <form id="auto_submit" action="<?php echo $this->deep_link_settings['deep_link_return_url']; ?>" method="POST">
            <input type="hidden" name="JWT" value="<?php echo $jwt; ?>" />
            <input type="submit" name="Go" />
        </form>
        <script>
            document.getElementById('auto_submit').submit();
        </script>
        <?php
    }
}
