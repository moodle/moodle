<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\IServiceRequest;

class ServiceRequest implements IServiceRequest
{
    // Request methods
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';

    // Request types
    public const TYPE_UNSUPPORTED = 'unsupported';
    public const TYPE_AUTH = 'auth';

    // MessageLaunch
    public const TYPE_GET_KEYSET = 'get_keyset';

    // AGS
    public const TYPE_GET_GRADES = 'get_grades';
    public const TYPE_SYNC_GRADE = 'sync_grades';
    public const TYPE_CREATE_LINEITEM = 'create_lineitem';
    public const TYPE_DELETE_LINEITEM = 'delete_lineitem';
    public const TYPE_GET_LINEITEMS = 'get_lineitems';
    public const TYPE_GET_LINEITEM = 'get_lineitem';
    public const TYPE_UPDATE_LINEITEM = 'update_lineitem';

    // CGS
    public const TYPE_GET_GROUPS = 'get_groups';
    public const TYPE_GET_SETS = 'get_sets';

    // NRPS
    public const TYPE_GET_MEMBERSHIPS = 'get_memberships';
    private $body;
    private $payload;
    private $accessToken;
    private $contentType = 'application/json';
    private $accept = 'application/json';

    // Other
    private $maskResponseLogs = false;

    public function __construct(
        private string $method,
        private string $url,
        private string $type = self::TYPE_UNSUPPORTED
    ) {}

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPayload(): array
    {
        if (isset($this->payload)) {
            return $this->payload;
        }

        $payload = [
            'headers' => $this->getHeaders(),
        ];

        $body = $this->getBody();
        if ($body) {
            $payload['body'] = $body;
        }

        return $payload;
    }

    public function setUrl(string $url): IServiceRequest
    {
        $this->url = $url;

        return $this;
    }

    public function setAccessToken(string $accessToken): IServiceRequest
    {
        $this->accessToken = 'Bearer '.$accessToken;

        return $this;
    }

    public function setBody(string $body): IServiceRequest
    {
        $this->body = $body;

        return $this;
    }

    public function setPayload(array $payload): IServiceRequest
    {
        $this->payload = $payload;

        return $this;
    }

    public function setAccept(string $accept): IServiceRequest
    {
        $this->accept = $accept;

        return $this;
    }

    public function setContentType(string $contentType): IServiceRequest
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getMaskResponseLogs(): bool
    {
        return $this->maskResponseLogs;
    }

    public function setMaskResponseLogs(bool $shouldMask): IServiceRequest
    {
        $this->maskResponseLogs = $shouldMask;

        return $this;
    }

    public function getErrorPrefix(): string
    {
        $defaultMessage = 'Logging request data:';
        $errorMessages = [
            static::TYPE_UNSUPPORTED => $defaultMessage,
            static::TYPE_AUTH => 'Authenticating:',
            static::TYPE_GET_KEYSET => 'Getting key set:',
            static::TYPE_GET_GRADES => 'Getting grades:',
            static::TYPE_SYNC_GRADE => 'Syncing grade for this lti_user_id:',
            static::TYPE_CREATE_LINEITEM => 'Creating lineitem:',
            static::TYPE_DELETE_LINEITEM => 'Deleting lineitem:',
            static::TYPE_GET_LINEITEMS => 'Getting lineitems:',
            static::TYPE_GET_LINEITEM => 'Getting a lineitem:',
            static::TYPE_UPDATE_LINEITEM => 'Updating lineitem:',
            static::TYPE_GET_GROUPS => 'Getting groups:',
            static::TYPE_GET_SETS => 'Getting sets:',
            static::TYPE_GET_MEMBERSHIPS => 'Getting memberships:',
        ];

        return $errorMessages[$this->type] ?? $defaultMessage;
    }

    private function getHeaders(): array
    {
        $headers = [
            'Accept' => $this->accept,
        ];

        if (isset($this->accessToken)) {
            $headers['Authorization'] = $this->accessToken;
        }

        // Include Content-Type for POST and PUT requests
        if (in_array($this->getMethod(), [ServiceRequest::METHOD_POST, ServiceRequest::METHOD_PUT])) {
            $headers['Content-Type'] = $this->contentType;
        }

        return $headers;
    }

    private function getBody(): ?string
    {
        return $this->body;
    }
}
