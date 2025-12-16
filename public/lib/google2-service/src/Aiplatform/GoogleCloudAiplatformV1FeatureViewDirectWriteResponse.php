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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureViewDirectWriteResponse extends \Google\Collection
{
  protected $collection_key = 'writeResponses';
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';
  protected $writeResponsesType = GoogleCloudAiplatformV1FeatureViewDirectWriteResponseWriteResponse::class;
  protected $writeResponsesDataType = 'array';

  /**
   * Response status for the keys listed in
   * FeatureViewDirectWriteResponse.write_responses. The error only applies to
   * the listed data keys - the stream will remain open for further
   * FeatureOnlineStoreService.FeatureViewDirectWriteRequest requests. Partial
   * failures (e.g. if the first 10 keys of a request fail, but the rest
   * succeed) from a single request may result in multiple responses - there
   * will be one response for the successful request keys and one response for
   * the failing request keys.
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
  /**
   * Details about write for each key. If status is not OK,
   * WriteResponse.data_key will have the key with error, but
   * WriteResponse.online_store_write_time will not be present.
   *
   * @param GoogleCloudAiplatformV1FeatureViewDirectWriteResponseWriteResponse[] $writeResponses
   */
  public function setWriteResponses($writeResponses)
  {
    $this->writeResponses = $writeResponses;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewDirectWriteResponseWriteResponse[]
   */
  public function getWriteResponses()
  {
    return $this->writeResponses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewDirectWriteResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewDirectWriteResponse');
