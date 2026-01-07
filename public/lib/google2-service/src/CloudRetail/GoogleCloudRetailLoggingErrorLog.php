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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailLoggingErrorLog extends \Google\Model
{
  protected $contextType = GoogleCloudRetailLoggingErrorContext::class;
  protected $contextDataType = '';
  protected $importPayloadType = GoogleCloudRetailLoggingImportErrorContext::class;
  protected $importPayloadDataType = '';
  /**
   * A message describing the error.
   *
   * @var string
   */
  public $message;
  /**
   * The API request payload, represented as a protocol buffer. Most API request
   * types are supported. For example: "type.googleapis.com/google.cloud.retail.
   * v2.ProductService.CreateProductRequest" "type.googleapis.com/google.cloud.r
   * etail.v2.UserEventService.WriteUserEventRequest"
   *
   * @var array[]
   */
  public $requestPayload;
  /**
   * The API response payload, represented as a protocol buffer. This is used to
   * log some "soft errors", where the response is valid but we consider there
   * are some quality issues like unjoined events. The following API responses
   * are supported and no PII is included:
   * "google.cloud.retail.v2.PredictionService.Predict"
   * "google.cloud.retail.v2.UserEventService.WriteUserEvent"
   * "google.cloud.retail.v2.UserEventService.CollectUserEvent"
   *
   * @var array[]
   */
  public $responsePayload;
  protected $serviceContextType = GoogleCloudRetailLoggingServiceContext::class;
  protected $serviceContextDataType = '';
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * A description of the context in which the error occurred.
   *
   * @param GoogleCloudRetailLoggingErrorContext $context
   */
  public function setContext(GoogleCloudRetailLoggingErrorContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return GoogleCloudRetailLoggingErrorContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * The error payload that is populated on LRO import APIs.
   *
   * @param GoogleCloudRetailLoggingImportErrorContext $importPayload
   */
  public function setImportPayload(GoogleCloudRetailLoggingImportErrorContext $importPayload)
  {
    $this->importPayload = $importPayload;
  }
  /**
   * @return GoogleCloudRetailLoggingImportErrorContext
   */
  public function getImportPayload()
  {
    return $this->importPayload;
  }
  /**
   * A message describing the error.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The API request payload, represented as a protocol buffer. Most API request
   * types are supported. For example: "type.googleapis.com/google.cloud.retail.
   * v2.ProductService.CreateProductRequest" "type.googleapis.com/google.cloud.r
   * etail.v2.UserEventService.WriteUserEventRequest"
   *
   * @param array[] $requestPayload
   */
  public function setRequestPayload($requestPayload)
  {
    $this->requestPayload = $requestPayload;
  }
  /**
   * @return array[]
   */
  public function getRequestPayload()
  {
    return $this->requestPayload;
  }
  /**
   * The API response payload, represented as a protocol buffer. This is used to
   * log some "soft errors", where the response is valid but we consider there
   * are some quality issues like unjoined events. The following API responses
   * are supported and no PII is included:
   * "google.cloud.retail.v2.PredictionService.Predict"
   * "google.cloud.retail.v2.UserEventService.WriteUserEvent"
   * "google.cloud.retail.v2.UserEventService.CollectUserEvent"
   *
   * @param array[] $responsePayload
   */
  public function setResponsePayload($responsePayload)
  {
    $this->responsePayload = $responsePayload;
  }
  /**
   * @return array[]
   */
  public function getResponsePayload()
  {
    return $this->responsePayload;
  }
  /**
   * The service context in which this error has occurred.
   *
   * @param GoogleCloudRetailLoggingServiceContext $serviceContext
   */
  public function setServiceContext(GoogleCloudRetailLoggingServiceContext $serviceContext)
  {
    $this->serviceContext = $serviceContext;
  }
  /**
   * @return GoogleCloudRetailLoggingServiceContext
   */
  public function getServiceContext()
  {
    return $this->serviceContext;
  }
  /**
   * The RPC status associated with the error log.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailLoggingErrorLog::class, 'Google_Service_CloudRetail_GoogleCloudRetailLoggingErrorLog');
