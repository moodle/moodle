<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Monitoring;

class HttpCheck extends \Google\Collection
{
  /**
   * No content type specified.
   */
  public const CONTENT_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * body is in URL-encoded form. Equivalent to setting the Content-Type to
   * application/x-www-form-urlencoded in the HTTP request.
   */
  public const CONTENT_TYPE_URL_ENCODED = 'URL_ENCODED';
  /**
   * body is in custom_content_type form. Equivalent to setting the Content-Type
   * to the contents of custom_content_type in the HTTP request.
   */
  public const CONTENT_TYPE_USER_PROVIDED = 'USER_PROVIDED';
  /**
   * No request method specified.
   */
  public const REQUEST_METHOD_METHOD_UNSPECIFIED = 'METHOD_UNSPECIFIED';
  /**
   * GET request.
   */
  public const REQUEST_METHOD_GET = 'GET';
  /**
   * POST request.
   */
  public const REQUEST_METHOD_POST = 'POST';
  protected $collection_key = 'acceptedResponseStatusCodes';
  protected $acceptedResponseStatusCodesType = ResponseStatusCode::class;
  protected $acceptedResponseStatusCodesDataType = 'array';
  protected $authInfoType = BasicAuthentication::class;
  protected $authInfoDataType = '';
  /**
   * The request body associated with the HTTP POST request. If content_type is
   * URL_ENCODED, the body passed in must be URL-encoded. Users can provide a
   * Content-Length header via the headers field or the API will do so. If the
   * request_method is GET and body is not empty, the API will return an error.
   * The maximum byte size is 1 megabyte.Note: If client libraries aren't used
   * (which performs the conversion automatically) base64 encode your body data
   * since the field is of bytes type.
   *
   * @var string
   */
  public $body;
  /**
   * The content type header to use for the check. The following configurations
   * result in errors: 1. Content type is specified in both the headers field
   * and the content_type field. 2. Request method is GET and content_type is
   * not TYPE_UNSPECIFIED 3. Request method is POST and content_type is
   * TYPE_UNSPECIFIED. 4. Request method is POST and a "Content-Type" header is
   * provided via headers field. The content_type field should be used instead.
   *
   * @var string
   */
  public $contentType;
  /**
   * A user provided content type header to use for the check. The invalid
   * configurations outlined in the content_type field apply to
   * custom_content_type, as well as the following: 1. content_type is
   * URL_ENCODED and custom_content_type is set. 2. content_type is
   * USER_PROVIDED and custom_content_type is not set.
   *
   * @var string
   */
  public $customContentType;
  /**
   * The list of headers to send as part of the Uptime check request. If two
   * headers have the same key and different values, they should be entered as a
   * single header, with the value being a comma-separated list of all the
   * desired values as described at
   * https://www.w3.org/Protocols/rfc2616/rfc2616.txt (page 31). Entering two
   * separate headers with the same key in a Create call will cause the first to
   * be overwritten by the second. The maximum number of headers allowed is 100.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Boolean specifying whether to encrypt the header information. Encryption
   * should be specified for any headers related to authentication that you do
   * not wish to be seen when retrieving the configuration. The server will be
   * responsible for encrypting the headers. On Get/List calls, if mask_headers
   * is set to true then the headers will be obscured with ******.
   *
   * @var bool
   */
  public $maskHeaders;
  /**
   * Optional (defaults to "/"). The path to the page against which to run the
   * check. Will be combined with the host (specified within the
   * monitored_resource) and port to construct the full URL. If the provided
   * path does not begin with "/", a "/" will be prepended automatically.
   *
   * @var string
   */
  public $path;
  protected $pingConfigType = PingConfig::class;
  protected $pingConfigDataType = '';
  /**
   * Optional (defaults to 80 when use_ssl is false, and 443 when use_ssl is
   * true). The TCP port on the HTTP server against which to run the check. Will
   * be combined with host (specified within the monitored_resource) and path to
   * construct the full URL.
   *
   * @var int
   */
  public $port;
  /**
   * The HTTP request method to use for the check. If set to METHOD_UNSPECIFIED
   * then request_method defaults to GET.
   *
   * @var string
   */
  public $requestMethod;
  protected $serviceAgentAuthenticationType = ServiceAgentAuthentication::class;
  protected $serviceAgentAuthenticationDataType = '';
  /**
   * If true, use HTTPS instead of HTTP to run the check.
   *
   * @var bool
   */
  public $useSsl;
  /**
   * Boolean specifying whether to include SSL certificate validation as a part
   * of the Uptime check. Only applies to checks where monitored_resource is set
   * to uptime_url. If use_ssl is false, setting validate_ssl to true has no
   * effect.
   *
   * @var bool
   */
  public $validateSsl;

  /**
   * If present, the check will only pass if the HTTP response status code is in
   * this set of status codes. If empty, the HTTP status code will only pass if
   * the HTTP status code is 200-299.
   *
   * @param ResponseStatusCode[] $acceptedResponseStatusCodes
   */
  public function setAcceptedResponseStatusCodes($acceptedResponseStatusCodes)
  {
    $this->acceptedResponseStatusCodes = $acceptedResponseStatusCodes;
  }
  /**
   * @return ResponseStatusCode[]
   */
  public function getAcceptedResponseStatusCodes()
  {
    return $this->acceptedResponseStatusCodes;
  }
  /**
   * The authentication information. Optional when creating an HTTP check;
   * defaults to empty. Do not set both auth_method and auth_info.
   *
   * @param BasicAuthentication $authInfo
   */
  public function setAuthInfo(BasicAuthentication $authInfo)
  {
    $this->authInfo = $authInfo;
  }
  /**
   * @return BasicAuthentication
   */
  public function getAuthInfo()
  {
    return $this->authInfo;
  }
  /**
   * The request body associated with the HTTP POST request. If content_type is
   * URL_ENCODED, the body passed in must be URL-encoded. Users can provide a
   * Content-Length header via the headers field or the API will do so. If the
   * request_method is GET and body is not empty, the API will return an error.
   * The maximum byte size is 1 megabyte.Note: If client libraries aren't used
   * (which performs the conversion automatically) base64 encode your body data
   * since the field is of bytes type.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * The content type header to use for the check. The following configurations
   * result in errors: 1. Content type is specified in both the headers field
   * and the content_type field. 2. Request method is GET and content_type is
   * not TYPE_UNSPECIFIED 3. Request method is POST and content_type is
   * TYPE_UNSPECIFIED. 4. Request method is POST and a "Content-Type" header is
   * provided via headers field. The content_type field should be used instead.
   *
   * Accepted values: TYPE_UNSPECIFIED, URL_ENCODED, USER_PROVIDED
   *
   * @param self::CONTENT_TYPE_* $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return self::CONTENT_TYPE_*
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * A user provided content type header to use for the check. The invalid
   * configurations outlined in the content_type field apply to
   * custom_content_type, as well as the following: 1. content_type is
   * URL_ENCODED and custom_content_type is set. 2. content_type is
   * USER_PROVIDED and custom_content_type is not set.
   *
   * @param string $customContentType
   */
  public function setCustomContentType($customContentType)
  {
    $this->customContentType = $customContentType;
  }
  /**
   * @return string
   */
  public function getCustomContentType()
  {
    return $this->customContentType;
  }
  /**
   * The list of headers to send as part of the Uptime check request. If two
   * headers have the same key and different values, they should be entered as a
   * single header, with the value being a comma-separated list of all the
   * desired values as described at
   * https://www.w3.org/Protocols/rfc2616/rfc2616.txt (page 31). Entering two
   * separate headers with the same key in a Create call will cause the first to
   * be overwritten by the second. The maximum number of headers allowed is 100.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Boolean specifying whether to encrypt the header information. Encryption
   * should be specified for any headers related to authentication that you do
   * not wish to be seen when retrieving the configuration. The server will be
   * responsible for encrypting the headers. On Get/List calls, if mask_headers
   * is set to true then the headers will be obscured with ******.
   *
   * @param bool $maskHeaders
   */
  public function setMaskHeaders($maskHeaders)
  {
    $this->maskHeaders = $maskHeaders;
  }
  /**
   * @return bool
   */
  public function getMaskHeaders()
  {
    return $this->maskHeaders;
  }
  /**
   * Optional (defaults to "/"). The path to the page against which to run the
   * check. Will be combined with the host (specified within the
   * monitored_resource) and port to construct the full URL. If the provided
   * path does not begin with "/", a "/" will be prepended automatically.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Contains information needed to add pings to an HTTP check.
   *
   * @param PingConfig $pingConfig
   */
  public function setPingConfig(PingConfig $pingConfig)
  {
    $this->pingConfig = $pingConfig;
  }
  /**
   * @return PingConfig
   */
  public function getPingConfig()
  {
    return $this->pingConfig;
  }
  /**
   * Optional (defaults to 80 when use_ssl is false, and 443 when use_ssl is
   * true). The TCP port on the HTTP server against which to run the check. Will
   * be combined with host (specified within the monitored_resource) and path to
   * construct the full URL.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * The HTTP request method to use for the check. If set to METHOD_UNSPECIFIED
   * then request_method defaults to GET.
   *
   * Accepted values: METHOD_UNSPECIFIED, GET, POST
   *
   * @param self::REQUEST_METHOD_* $requestMethod
   */
  public function setRequestMethod($requestMethod)
  {
    $this->requestMethod = $requestMethod;
  }
  /**
   * @return self::REQUEST_METHOD_*
   */
  public function getRequestMethod()
  {
    return $this->requestMethod;
  }
  /**
   * If specified, Uptime will generate and attach an OIDC JWT token for the
   * Monitoring service agent service account as an Authorization header in the
   * HTTP request when probing.
   *
   * @param ServiceAgentAuthentication $serviceAgentAuthentication
   */
  public function setServiceAgentAuthentication(ServiceAgentAuthentication $serviceAgentAuthentication)
  {
    $this->serviceAgentAuthentication = $serviceAgentAuthentication;
  }
  /**
   * @return ServiceAgentAuthentication
   */
  public function getServiceAgentAuthentication()
  {
    return $this->serviceAgentAuthentication;
  }
  /**
   * If true, use HTTPS instead of HTTP to run the check.
   *
   * @param bool $useSsl
   */
  public function setUseSsl($useSsl)
  {
    $this->useSsl = $useSsl;
  }
  /**
   * @return bool
   */
  public function getUseSsl()
  {
    return $this->useSsl;
  }
  /**
   * Boolean specifying whether to include SSL certificate validation as a part
   * of the Uptime check. Only applies to checks where monitored_resource is set
   * to uptime_url. If use_ssl is false, setting validate_ssl to true has no
   * effect.
   *
   * @param bool $validateSsl
   */
  public function setValidateSsl($validateSsl)
  {
    $this->validateSsl = $validateSsl;
  }
  /**
   * @return bool
   */
  public function getValidateSsl()
  {
    return $this->validateSsl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpCheck::class, 'Google_Service_Monitoring_HttpCheck');
