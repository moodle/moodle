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

namespace Google\Service\CloudTasks;

class HttpTarget extends \Google\Collection
{
  /**
   * HTTP method unspecified
   */
  public const HTTP_METHOD_HTTP_METHOD_UNSPECIFIED = 'HTTP_METHOD_UNSPECIFIED';
  /**
   * HTTP POST
   */
  public const HTTP_METHOD_POST = 'POST';
  /**
   * HTTP GET
   */
  public const HTTP_METHOD_GET = 'GET';
  /**
   * HTTP HEAD
   */
  public const HTTP_METHOD_HEAD = 'HEAD';
  /**
   * HTTP PUT
   */
  public const HTTP_METHOD_PUT = 'PUT';
  /**
   * HTTP DELETE
   */
  public const HTTP_METHOD_DELETE = 'DELETE';
  /**
   * HTTP PATCH
   */
  public const HTTP_METHOD_PATCH = 'PATCH';
  /**
   * HTTP OPTIONS
   */
  public const HTTP_METHOD_OPTIONS = 'OPTIONS';
  protected $collection_key = 'headerOverrides';
  protected $headerOverridesType = HeaderOverride::class;
  protected $headerOverridesDataType = 'array';
  /**
   * The HTTP method to use for the request. When specified, it overrides
   * HttpRequest for the task. Note that if the value is set to HttpMethod the
   * HttpRequest of the task will be ignored at execution time.
   *
   * @var string
   */
  public $httpMethod;
  protected $oauthTokenType = OAuthToken::class;
  protected $oauthTokenDataType = '';
  protected $oidcTokenType = OidcToken::class;
  protected $oidcTokenDataType = '';
  protected $uriOverrideType = UriOverride::class;
  protected $uriOverrideDataType = '';

  /**
   * HTTP target headers. This map contains the header field names and values.
   * Headers will be set when running the CreateTask and/or BufferTask. These
   * headers represent a subset of the headers that will be configured for the
   * task's HTTP request. Some HTTP request headers will be ignored or replaced.
   * A partial list of headers that will be ignored or replaced is: * Several
   * predefined headers, prefixed with "X-CloudTasks-", can be used to define
   * properties of the task. * Host: This will be computed by Cloud Tasks and
   * derived from HttpRequest.url. * Content-Length: This will be computed by
   * Cloud Tasks. `Content-Type` won't be set by Cloud Tasks. You can explicitly
   * set `Content-Type` to a media type when the task is created. For
   * example,`Content-Type` can be set to `"application/octet-stream"` or
   * `"application/json"`. The default value is set to "application/json"`. *
   * User-Agent: This will be set to `"Google-Cloud-Tasks"`. Headers which can
   * have multiple values (according to RFC2616) can be specified using comma-
   * separated values. The size of the headers must be less than 80KB. Queue-
   * level headers to override headers of all the tasks in the queue. Do not put
   * business sensitive or personally identifying data in the HTTP Header
   * Override Configuration or other similar fields in accordance with Section
   * 12 (Resource Fields) of the [Service Specific
   * Terms](https://cloud.google.com/terms/service-terms).
   *
   * @param HeaderOverride[] $headerOverrides
   */
  public function setHeaderOverrides($headerOverrides)
  {
    $this->headerOverrides = $headerOverrides;
  }
  /**
   * @return HeaderOverride[]
   */
  public function getHeaderOverrides()
  {
    return $this->headerOverrides;
  }
  /**
   * The HTTP method to use for the request. When specified, it overrides
   * HttpRequest for the task. Note that if the value is set to HttpMethod the
   * HttpRequest of the task will be ignored at execution time.
   *
   * Accepted values: HTTP_METHOD_UNSPECIFIED, POST, GET, HEAD, PUT, DELETE,
   * PATCH, OPTIONS
   *
   * @param self::HTTP_METHOD_* $httpMethod
   */
  public function setHttpMethod($httpMethod)
  {
    $this->httpMethod = $httpMethod;
  }
  /**
   * @return self::HTTP_METHOD_*
   */
  public function getHttpMethod()
  {
    return $this->httpMethod;
  }
  /**
   * If specified, an [OAuth
   * token](https://developers.google.com/identity/protocols/OAuth2) is
   * generated and attached as the `Authorization` header in the HTTP request.
   * This type of authorization should generally be used only when calling
   * Google APIs hosted on *.googleapis.com. Note that both the service account
   * email and the scope MUST be specified when using the queue-level
   * authorization override.
   *
   * @param OAuthToken $oauthToken
   */
  public function setOauthToken(OAuthToken $oauthToken)
  {
    $this->oauthToken = $oauthToken;
  }
  /**
   * @return OAuthToken
   */
  public function getOauthToken()
  {
    return $this->oauthToken;
  }
  /**
   * If specified, an
   * [OIDC](https://developers.google.com/identity/protocols/OpenIDConnect)
   * token is generated and attached as an `Authorization` header in the HTTP
   * request. This type of authorization can be used for many scenarios,
   * including calling Cloud Run, or endpoints where you intend to validate the
   * token yourself. Note that both the service account email and the audience
   * MUST be specified when using the queue-level authorization override.
   *
   * @param OidcToken $oidcToken
   */
  public function setOidcToken(OidcToken $oidcToken)
  {
    $this->oidcToken = $oidcToken;
  }
  /**
   * @return OidcToken
   */
  public function getOidcToken()
  {
    return $this->oidcToken;
  }
  /**
   * URI override. When specified, overrides the execution URI for all the tasks
   * in the queue.
   *
   * @param UriOverride $uriOverride
   */
  public function setUriOverride(UriOverride $uriOverride)
  {
    $this->uriOverride = $uriOverride;
  }
  /**
   * @return UriOverride
   */
  public function getUriOverride()
  {
    return $this->uriOverride;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpTarget::class, 'Google_Service_CloudTasks_HttpTarget');
