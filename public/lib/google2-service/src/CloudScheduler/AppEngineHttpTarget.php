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

class AppEngineHttpTarget extends \Google\Model
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
  protected $appEngineRoutingType = AppEngineRouting::class;
  protected $appEngineRoutingDataType = '';
  /**
   * Body. HTTP request body. A request body is allowed only if the HTTP method
   * is POST or PUT. It will result in invalid argument error to set a body on a
   * job with an incompatible HttpMethod.
   *
   * @var string
   */
  public $body;
  /**
   * HTTP request headers. This map contains the header field names and values.
   * Headers can be set when the job is created. Cloud Scheduler sets some
   * headers to default values: * `User-Agent`: By default, this header is
   * `"AppEngine-Google; (+http://code.google.com/appengine)"`. This header can
   * be modified, but Cloud Scheduler will append `"AppEngine-Google;
   * (+http://code.google.com/appengine)"` to the modified `User-Agent`. *
   * `X-CloudScheduler`: This header will be set to true. * `X-CloudScheduler-
   * JobName`: This header will contain the job name. * `X-CloudScheduler-
   * ScheduleTime`: For Cloud Scheduler jobs specified in the unix-cron format,
   * this header will contain the job schedule as an offset of UTC parsed
   * according to RFC3339. If the job has a body and the following headers are
   * not set by the user, Cloud Scheduler sets default values: * `Content-Type`:
   * This will be set to `"application/octet-stream"`. You can override this
   * default by explicitly setting `Content-Type` to a particular media type
   * when creating the job. For example, you can set `Content-Type` to
   * `"application/json"`. The headers below are output only. They cannot be set
   * or overridden: * `Content-Length`: This is computed by Cloud Scheduler. *
   * `X-Google-*`: For Google internal use only. * `X-AppEngine-*`: For Google
   * internal use only. In addition, some App Engine headers, which contain job-
   * specific information, are also be sent to the job handler.
   *
   * @var string[]
   */
  public $headers;
  /**
   * The HTTP method to use for the request. PATCH and OPTIONS are not
   * permitted.
   *
   * @var string
   */
  public $httpMethod;
  /**
   * The relative URI. The relative URL must begin with "/" and must be a valid
   * HTTP relative URL. It can contain a path, query string arguments, and `#`
   * fragments. If the relative URL is empty, then the root path "/" will be
   * used. No spaces are allowed, and the maximum length allowed is 2083
   * characters.
   *
   * @var string
   */
  public $relativeUri;

  /**
   * App Engine Routing setting for the job.
   *
   * @param AppEngineRouting $appEngineRouting
   */
  public function setAppEngineRouting(AppEngineRouting $appEngineRouting)
  {
    $this->appEngineRouting = $appEngineRouting;
  }
  /**
   * @return AppEngineRouting
   */
  public function getAppEngineRouting()
  {
    return $this->appEngineRouting;
  }
  /**
   * Body. HTTP request body. A request body is allowed only if the HTTP method
   * is POST or PUT. It will result in invalid argument error to set a body on a
   * job with an incompatible HttpMethod.
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
   * Headers can be set when the job is created. Cloud Scheduler sets some
   * headers to default values: * `User-Agent`: By default, this header is
   * `"AppEngine-Google; (+http://code.google.com/appengine)"`. This header can
   * be modified, but Cloud Scheduler will append `"AppEngine-Google;
   * (+http://code.google.com/appengine)"` to the modified `User-Agent`. *
   * `X-CloudScheduler`: This header will be set to true. * `X-CloudScheduler-
   * JobName`: This header will contain the job name. * `X-CloudScheduler-
   * ScheduleTime`: For Cloud Scheduler jobs specified in the unix-cron format,
   * this header will contain the job schedule as an offset of UTC parsed
   * according to RFC3339. If the job has a body and the following headers are
   * not set by the user, Cloud Scheduler sets default values: * `Content-Type`:
   * This will be set to `"application/octet-stream"`. You can override this
   * default by explicitly setting `Content-Type` to a particular media type
   * when creating the job. For example, you can set `Content-Type` to
   * `"application/json"`. The headers below are output only. They cannot be set
   * or overridden: * `Content-Length`: This is computed by Cloud Scheduler. *
   * `X-Google-*`: For Google internal use only. * `X-AppEngine-*`: For Google
   * internal use only. In addition, some App Engine headers, which contain job-
   * specific information, are also be sent to the job handler.
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
   * The HTTP method to use for the request. PATCH and OPTIONS are not
   * permitted.
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
   * The relative URI. The relative URL must begin with "/" and must be a valid
   * HTTP relative URL. It can contain a path, query string arguments, and `#`
   * fragments. If the relative URL is empty, then the root path "/" will be
   * used. No spaces are allowed, and the maximum length allowed is 2083
   * characters.
   *
   * @param string $relativeUri
   */
  public function setRelativeUri($relativeUri)
  {
    $this->relativeUri = $relativeUri;
  }
  /**
   * @return string
   */
  public function getRelativeUri()
  {
    return $this->relativeUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppEngineHttpTarget::class, 'Google_Service_CloudScheduler_AppEngineHttpTarget');
