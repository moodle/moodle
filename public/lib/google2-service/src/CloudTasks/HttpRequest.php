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

class HttpRequest extends \Google\Model
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
  /**
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST, PUT, or PATCH. It is an error to set body on a task with an
   * incompatible HttpMethod.
   *
   * @var string
   */
  public $body;
  /**
   * HTTP request headers. This map contains the header field names and values.
   * Headers can be set when the task is created. These headers represent a
   * subset of the headers that will accompany the task's HTTP request. Some
   * HTTP request headers will be ignored or replaced. A partial list of headers
   * that will be ignored or replaced is: * Host: This will be computed by Cloud
   * Tasks and derived from HttpRequest.url. * Content-Length: This will be
   * computed by Cloud Tasks. * User-Agent: This will be set to `"Google-Cloud-
   * Tasks"`. * `X-Google-*`: Google use only. * `X-AppEngine-*`: Google use
   * only. `Content-Type` won't be set by Cloud Tasks. You can explicitly set
   * `Content-Type` to a media type when the task is created. For example,
   * `Content-Type` can be set to `"application/octet-stream"` or
   * `"application/json"`. Headers which can have multiple values (according to
   * RFC2616) can be specified using comma-separated values. The size of the
   * headers must be less than 80KB.
   *
   * @var string[]
   */
  public $headers;
  /**
   * The HTTP method to use for the request. The default is POST.
   *
   * @var string
   */
  public $httpMethod;
  protected $oauthTokenType = OAuthToken::class;
  protected $oauthTokenDataType = '';
  protected $oidcTokenType = OidcToken::class;
  protected $oidcTokenDataType = '';
  /**
   * Required. The full url path that the request will be sent to. This string
   * must begin with either "http://" or "https://". Some examples are:
   * `http://acme.com` and `https://acme.com/sales:8080`. Cloud Tasks will
   * encode some characters for safety and compatibility. The maximum allowed
   * URL length is 2083 characters after encoding. The `Location` header
   * response from a redirect response [`300` - `399`] may be followed. The
   * redirect is not counted as a separate attempt.
   *
   * @var string
   */
  public $url;

  /**
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST, PUT, or PATCH. It is an error to set body on a task with an
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
   * Headers can be set when the task is created. These headers represent a
   * subset of the headers that will accompany the task's HTTP request. Some
   * HTTP request headers will be ignored or replaced. A partial list of headers
   * that will be ignored or replaced is: * Host: This will be computed by Cloud
   * Tasks and derived from HttpRequest.url. * Content-Length: This will be
   * computed by Cloud Tasks. * User-Agent: This will be set to `"Google-Cloud-
   * Tasks"`. * `X-Google-*`: Google use only. * `X-AppEngine-*`: Google use
   * only. `Content-Type` won't be set by Cloud Tasks. You can explicitly set
   * `Content-Type` to a media type when the task is created. For example,
   * `Content-Type` can be set to `"application/octet-stream"` or
   * `"application/json"`. Headers which can have multiple values (according to
   * RFC2616) can be specified using comma-separated values. The size of the
   * headers must be less than 80KB.
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
   * The HTTP method to use for the request. The default is POST.
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
   * Required. The full url path that the request will be sent to. This string
   * must begin with either "http://" or "https://". Some examples are:
   * `http://acme.com` and `https://acme.com/sales:8080`. Cloud Tasks will
   * encode some characters for safety and compatibility. The maximum allowed
   * URL length is 2083 characters after encoding. The `Location` header
   * response from a redirect response [`300` - `399`] may be followed. The
   * redirect is not counted as a separate attempt.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRequest::class, 'Google_Service_CloudTasks_HttpRequest');
