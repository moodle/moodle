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

namespace Google\Service\CloudScheduler;

class HttpTarget extends \Google\Model
{
  /**
   * HTTP method unspecified. Defaults to POST.
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
  /**
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST, PUT, or PATCH. It is an error to set body on a job with an
   * incompatible HttpMethod.
   *
   * @var string
   */
  public $body;
  /**
   * HTTP request headers. This map contains the header field names and values.
   * The user can specify HTTP request headers to send with the job's HTTP
   * request. Repeated headers are not supported, but a header value can contain
   * commas. The following headers represent a subset of the headers that
   * accompany the job's HTTP request. Some HTTP request headers are ignored or
   * replaced. A partial list of headers that are ignored or replaced is below:
   * * Host: This will be computed by Cloud Scheduler and derived from uri. *
   * `Content-Length`: This will be computed by Cloud Scheduler. * `User-Agent`:
   * This will be set to `"Google-Cloud-Scheduler"`. * `X-Google-*`: Google
   * internal use only. * `X-AppEngine-*`: Google internal use only. *
   * `X-CloudScheduler`: This header will be set to true. * `X-CloudScheduler-
   * JobName`: This header will contain the job name. * `X-CloudScheduler-
   * ScheduleTime`: For Cloud Scheduler jobs specified in the unix-cron format,
   * this header will contain the job schedule as an offset of UTC parsed
   * according to RFC3339. If the job has a body and the following headers are
   * not set by the user, Cloud Scheduler sets default values: * `Content-Type`:
   * This will be set to `"application/octet-stream"`. You can override this
   * default by explicitly setting `Content-Type` to a particular media type
   * when creating the job. For example, you can set `Content-Type` to
   * `"application/json"`. The total size of headers must be less than 80KB.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Which HTTP method to use for the request.
   *
   * @var string
   */
  public $httpMethod;
  protected $oauthTokenType = OAuthToken::class;
  protected $oauthTokenDataType = '';
  protected $oidcTokenType = OidcToken::class;
  protected $oidcTokenDataType = '';
  /**
   * Required. The full URI path that the request will be sent to. This string
   * must begin with either "http://" or "https://". Some examples of valid
   * values for uri are: `http://acme.com` and `https://acme.com/sales:8080`.
   * Cloud Scheduler will encode some characters for safety and compatibility.
   * The maximum allowed URL length is 2083 characters after encoding.
   *
   * @var string
   */
  public $uri;

  /**
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST, PUT, or PATCH. It is an error to set body on a job with an
   * incompatible HttpMethod.
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
   * HTTP request headers. This map contains the header field names and values.
   * The user can specify HTTP request headers to send with the job's HTTP
   * request. Repeated headers are not supported, but a header value can contain
   * commas. The following headers represent a subset of the headers that
   * accompany the job's HTTP request. Some HTTP request headers are ignored or
   * replaced. A partial list of headers that are ignored or replaced is below:
   * * Host: This will be computed by Cloud Scheduler and derived from uri. *
   * `Content-Length`: This will be computed by Cloud Scheduler. * `User-Agent`:
   * This will be set to `"Google-Cloud-Scheduler"`. * `X-Google-*`: Google
   * internal use only. * `X-AppEngine-*`: Google internal use only. *
   * `X-CloudScheduler`: This header will be set to true. * `X-CloudScheduler-
   * JobName`: This header will contain the job name. * `X-CloudScheduler-
   * ScheduleTime`: For Cloud Scheduler jobs specified in the unix-cron format,
   * this header will contain the job schedule as an offset of UTC parsed
   * according to RFC3339. If the job has a body and the following headers are
   * not set by the user, Cloud Scheduler sets default values: * `Content-Type`:
   * This will be set to `"application/octet-stream"`. You can override this
   * default by explicitly setting `Content-Type` to a particular media type
   * when creating the job. For example, you can set `Content-Type` to
   * `"application/json"`. The total size of headers must be less than 80KB.
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
   * Which HTTP method to use for the request.
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
   * token](https://developers.google.com/identity/protocols/OAuth2) will be
   * generated and attached as an `Authorization` header in the HTTP request.
   * This type of authorization should generally only be used when calling
   * Google APIs hosted on *.googleapis.com.
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
   * token will be generated and attached as an `Authorization` header in the
   * HTTP request. This type of authorization can be used for many scenarios,
   * including calling Cloud Run, or endpoints where you intend to validate the
   * token yourself.
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
   * Required. The full URI path that the request will be sent to. This string
   * must begin with either "http://" or "https://". Some examples of valid
   * values for uri are: `http://acme.com` and `https://acme.com/sales:8080`.
   * Cloud Scheduler will encode some characters for safety and compatibility.
   * The maximum allowed URL length is 2083 characters after encoding.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpTarget::class, 'Google_Service_CloudScheduler_HttpTarget');
