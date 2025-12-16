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

namespace Google\Service\APIManagement;

class HttpOperation extends \Google\Collection
{
  /**
   * Unspecified HTTP method
   */
  public const METHOD_HTTP_METHOD_UNSPECIFIED = 'HTTP_METHOD_UNSPECIFIED';
  /**
   * GET HTTP method
   */
  public const METHOD_GET = 'GET';
  /**
   * HEAD HTTP method
   */
  public const METHOD_HEAD = 'HEAD';
  /**
   * POST HTTP method
   */
  public const METHOD_POST = 'POST';
  /**
   * PUT HTTP method
   */
  public const METHOD_PUT = 'PUT';
  /**
   * PATCH HTTP method
   */
  public const METHOD_PATCH = 'PATCH';
  /**
   * DELETE HTTP method
   */
  public const METHOD_DELETE = 'DELETE';
  /**
   * TRACE HTTP method
   */
  public const METHOD_TRACE = 'TRACE';
  /**
   * OPTIONS HTTP method
   */
  public const METHOD_OPTIONS = 'OPTIONS';
  /**
   * CONNECT HTTP method
   */
  public const METHOD_CONNECT = 'CONNECT';
  protected $collection_key = 'pathParams';
  /**
   * HTTP Method.
   *
   * @var string
   */
  public $method;
  /**
   * Path of the HTTP request.
   *
   * @var string
   */
  public $path;
  protected $pathParamsType = HttpOperationPathParam::class;
  protected $pathParamsDataType = 'array';
  protected $queryParamsType = HttpOperationQueryParam::class;
  protected $queryParamsDataType = 'map';
  protected $requestType = HttpOperationHttpRequest::class;
  protected $requestDataType = '';
  protected $responseType = HttpOperationHttpResponse::class;
  protected $responseDataType = '';

  /**
   * HTTP Method.
   *
   * Accepted values: HTTP_METHOD_UNSPECIFIED, GET, HEAD, POST, PUT, PATCH,
   * DELETE, TRACE, OPTIONS, CONNECT
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Path of the HTTP request.
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
   * Path params of HttpOperation
   *
   * @param HttpOperationPathParam[] $pathParams
   */
  public function setPathParams($pathParams)
  {
    $this->pathParams = $pathParams;
  }
  /**
   * @return HttpOperationPathParam[]
   */
  public function getPathParams()
  {
    return $this->pathParams;
  }
  /**
   * Query params of HttpOperation
   *
   * @param HttpOperationQueryParam[] $queryParams
   */
  public function setQueryParams($queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return HttpOperationQueryParam[]
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
  /**
   * Request metadata.
   *
   * @param HttpOperationHttpRequest $request
   */
  public function setRequest(HttpOperationHttpRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return HttpOperationHttpRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Response metadata.
   *
   * @param HttpOperationHttpResponse $response
   */
  public function setResponse(HttpOperationHttpResponse $response)
  {
    $this->response = $response;
  }
  /**
   * @return HttpOperationHttpResponse
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpOperation::class, 'Google_Service_APIManagement_HttpOperation');
