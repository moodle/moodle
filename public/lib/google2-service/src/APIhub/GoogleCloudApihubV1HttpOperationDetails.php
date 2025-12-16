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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1HttpOperationDetails extends \Google\Collection
{
  protected $collection_key = 'pathParams';
  protected $httpOperationType = GoogleCloudApihubV1HttpOperation::class;
  protected $httpOperationDataType = '';
  protected $pathParamsType = GoogleCloudApihubV1PathParam::class;
  protected $pathParamsDataType = 'array';
  protected $queryParamsType = GoogleCloudApihubV1QueryParam::class;
  protected $queryParamsDataType = 'map';
  protected $requestType = GoogleCloudApihubV1HttpRequest::class;
  protected $requestDataType = '';
  protected $responseType = GoogleCloudApihubV1HttpResponse::class;
  protected $responseDataType = '';

  /**
   * Required. An HTTP Operation.
   *
   * @param GoogleCloudApihubV1HttpOperation $httpOperation
   */
  public function setHttpOperation(GoogleCloudApihubV1HttpOperation $httpOperation)
  {
    $this->httpOperation = $httpOperation;
  }
  /**
   * @return GoogleCloudApihubV1HttpOperation
   */
  public function getHttpOperation()
  {
    return $this->httpOperation;
  }
  /**
   * Optional. Path params of HttpOperation
   *
   * @param GoogleCloudApihubV1PathParam[] $pathParams
   */
  public function setPathParams($pathParams)
  {
    $this->pathParams = $pathParams;
  }
  /**
   * @return GoogleCloudApihubV1PathParam[]
   */
  public function getPathParams()
  {
    return $this->pathParams;
  }
  /**
   * Optional. Query params of HttpOperation
   *
   * @param GoogleCloudApihubV1QueryParam[] $queryParams
   */
  public function setQueryParams($queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return GoogleCloudApihubV1QueryParam[]
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
  /**
   * Optional. Request metadata.
   *
   * @param GoogleCloudApihubV1HttpRequest $request
   */
  public function setRequest(GoogleCloudApihubV1HttpRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudApihubV1HttpRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Optional. Response metadata.
   *
   * @param GoogleCloudApihubV1HttpResponse $response
   */
  public function setResponse(GoogleCloudApihubV1HttpResponse $response)
  {
    $this->response = $response;
  }
  /**
   * @return GoogleCloudApihubV1HttpResponse
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1HttpOperationDetails::class, 'Google_Service_APIhub_GoogleCloudApihubV1HttpOperationDetails');
