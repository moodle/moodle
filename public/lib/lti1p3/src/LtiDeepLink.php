<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiDeepLink
{
    public function __construct(
        private ILtiRegistration $registration,
        private string $deployment_id,
        private array $deep_link_settings
    ) {}

    public function getResponseJwt(array $resources): string
    {
        $message_jwt = [
            'iss' => $this->registration->getClientId(),
            'aud' => [$this->registration->getIssuer()],
            'exp' => time() + 600,
            'iat' => time(),
            'nonce' => LtiOidcLogin::secureRandomString('nonce-'),
            LtiConstants::DEPLOYMENT_ID => $this->deployment_id,
            LtiConstants::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_DEEPLINK_RESPONSE,
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::DL_CONTENT_ITEMS => array_map(function ($resource) {
                return $resource->toArray();
            }, $resources),
        ];

        // https://www.imsglobal.org/spec/lti-dl/v2p0/#deep-linking-request-message
        // 'data' is an optional property which, if it exists, must be returned by the tool
        if (isset($this->settings()['data'])) {
            $message_jwt[LtiConstants::DL_DATA] = $this->settings()['data'];
        }

        return JWT::encode($message_jwt, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());
    }

    public function settings(): array
    {
        return $this->deep_link_settings;
    }

    public function returnUrl(): string
    {
        return $this->settings()['deep_link_return_url'];
    }

    public function acceptTypes(): array
    {
        return $this->settings()['accept_types'];
    }

    public function canAcceptType(string $acceptType): bool
    {
        return in_array($acceptType, $this->acceptTypes());
    }

    public function acceptPresentationDocumentTargets(): array
    {
        return $this->settings()['accept_presentation_document_targets'];
    }

    public function canAcceptPresentationDocumentTarget(string $target): bool
    {
        return in_array($target, $this->acceptPresentationDocumentTargets());
    }

    public function acceptMediaTypes(): ?string
    {
        return $this->settings()['accept_media_types'] ?? null;
    }

    public function canAcceptMultiple(): bool
    {
        return $this->settings()['accept_multiple'] ?? false;
    }

    public function canAcceptLineitem(): bool
    {
        return $this->settings()['accept_lineitem'] ?? false;
    }

    public function canAutoCreate(): bool
    {
        return $this->settings()['auto_create'] ?? false;
    }

    public function title(): ?string
    {
        return $this->settings()['title'] ?? null;
    }

    public function text(): ?string
    {
        return $this->settings()['text'] ?? null;
    }

}
