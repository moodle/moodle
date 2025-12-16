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

class AppEngineHttpRequest extends \Google\Model
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
  protected $appEngineRoutingType = AppEngineRouting::class;
  protected $appEngineRoutingDataType = '';
  /**
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST or PUT. It is an error to set a body on a task with an incompatible
   * HttpMethod.
   *
   * @var string
   */
  public $body;
  /**
   * HTTP request headers. This map contains the header field names and values.
   * Headers can be set when the task is created. Repeated headers are not
   * supported but a header value can contain commas. Cloud Tasks sets some
   * headers to default values: * `User-Agent`: By default, this header is
   * `"AppEngine-Google; (+http://code.google.com/appengine)"`. This header can
   * be modified, but Cloud Tasks will append `"AppEngine-Google;
   * (+http://code.google.com/appengine)"` to the modified `User-Agent`. If the
   * task has a body, Cloud Tasks sets the following headers: * `Content-Type`:
   * By default, the `Content-Type` header is set to `"application/octet-
   * stream"`. The default can be overridden by explicitly setting `Content-
   * Type` to a particular media type when the task is created. For example,
   * `Content-Type` can be set to `"application/json"`. * `Content-Length`: This
   * is computed by Cloud Tasks. This value is output only. It cannot be
   * changed. The headers below cannot be set or overridden: * `Host` *
   * `X-Google-*` * `X-AppEngine-*` In addition, Cloud Tasks sets some headers
   * when the task is dispatched, such as headers containing information about
   * the task; see [request
   * headers](https://cloud.google.com/tasks/docs/creating-appengine-
   * handlers#reading_request_headers). These headers are set only when the task
   * is dispatched, so they are not visible when the task is returned in a Cloud
   * Tasks response. Although there is no specific limit for the maximum number
   * of headers or the size, there is a limit on the maximum size of the Task.
   * For more information, see the CreateTask documentation.
   *
   * @var string[]
   */
  public $headers;
  /**
   * The HTTP method to use for the request. The default is POST. The app's
   * request handler for the task's target URL must be able to handle HTTP
   * requests with this http_method, otherwise the task attempt fails with error
   * code 405 (Method Not Allowed). See [Writing a push task request handler](ht
   * tps://cloud.google.com/appengine/docs/java/taskqueue/push/creating-
   * handlers#writing_a_push_task_request_handler) and the App Engine
   * documentation for your runtime on [How Requests are
   * Handled](https://cloud.google.com/appengine/docs/standard/python3/how-
   * requests-are-handled).
   *
   * @var string
   */
  public $httpMethod;
  /**
   * The relative URI. The relative URI must begin with "/" and must be a valid
   * HTTP relative URI. It can contain a path and query string arguments. If the
   * relative URI is empty, then the root path "/" will be used. No spaces are
   * allowed, and the maximum length allowed is 2083 characters.
   *
   * @var string
   */
  public $relativeUri;

  /**
   * Task-level setting for App Engine routing. * If app_engine_routing_override
   * is set on the queue, this value is used for all tasks in the queue, no
   * matter what the setting is for the task-level app_engine_routing.
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
   * HTTP request body. A request body is allowed only if the HTTP method is
   * POST or PUT. It is an error to set a body on a task with an incompatible
   * HttpMethod.
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
   * Headers can be set when the task is created. Repeated headers are not
   * supported but a header value can contain commas. Cloud Tasks sets some
   * headers to default values: * `User-Agent`: By default, this header is
   * `"AppEngine-Google; (+http://code.google.com/appengine)"`. This header can
   * be modified, but Cloud Tasks will append `"AppEngine-Google;
   * (+http://code.google.com/appengine)"` to the modified `User-Agent`. If the
   * task has a body, Cloud Tasks sets the following headers: * `Content-Type`:
   * By default, the `Content-Type` header is set to `"application/octet-
   * stream"`. The default can be overridden by explicitly setting `Content-
   * Type` to a particular media type when the task is created. For example,
   * `Content-Type` can be set to `"application/json"`. * `Content-Length`: This
   * is computed by Cloud Tasks. This value is output only. It cannot be
   * changed. The headers below cannot be set or overridden: * `Host` *
   * `X-Google-*` * `X-AppEngine-*` In addition, Cloud Tasks sets some headers
   * when the task is dispatched, such as headers containing information about
   * the task; see [request
   * headers](https://cloud.google.com/tasks/docs/creating-appengine-
   * handlers#reading_request_headers). These headers are set only when the task
   * is dispatched, so they are not visible when the task is returned in a Cloud
   * Tasks response. Although there is no specific limit for the maximum number
   * of headers or the size, there is a limit on the maximum size of the Task.
   * For more information, see the CreateTask documentation.
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
   * The HTTP method to use for the request. The default is POST. The app's
   * request handler for the task's target URL must be able to handle HTTP
   * requests with this http_method, otherwise the task attempt fails with error
   * code 405 (Method Not Allowed). See [Writing a push task request handler](ht
   * tps://cloud.google.com/appengine/docs/java/taskqueue/push/creating-
   * handlers#writing_a_push_task_request_handler) and the App Engine
   * documentation for your runtime on [How Requests are
   * Handled](https://cloud.google.com/appengine/docs/standard/python3/how-
   * requests-are-handled).
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
   * The relative URI. The relative URI must begin with "/" and must be a valid
   * HTTP relative URI. It can contain a path and query string arguments. If the
   * relative URI is empty, then the root path "/" will be used. No spaces are
   * allowed, and the maximum length allowed is 2083 characters.
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
class_alias(AppEngineHttpRequest::class, 'Google_Service_CloudTasks_AppEngineHttpRequest');
