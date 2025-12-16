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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsMetadata extends \Google\Collection
{
  protected $collection_key = 'partialErrors';
  protected $partialErrorsType = GoogleRpcStatus::class;
  protected $partialErrorsDataType = 'array';
  protected $requestType = GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest::class;
  protected $requestDataType = '';

  /**
   * Partial errors during deletion operation that might cause the operation
   * output to be incomplete.
   *
   * @param GoogleRpcStatus[] $partialErrors
   */
  public function setPartialErrors($partialErrors)
  {
    $this->partialErrors = $partialErrors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialErrors()
  {
    return $this->partialErrors;
  }
  /**
   * Output only. The original request for delete.
   *
   * @param GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest $request
   */
  public function setRequest(GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsMetadata');
