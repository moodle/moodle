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

class GoogleCloudAiplatformV1GenericOperationMetadata extends \Google\Collection
{
  protected $collection_key = 'partialFailures';
  /**
   * Output only. Time when the operation was created.
   *
   * @var string
   */
  public $createTime;
  protected $partialFailuresType = GoogleRpcStatus::class;
  protected $partialFailuresDataType = 'array';
  /**
   * Output only. Time when the operation was updated for the last time. If the
   * operation has finished (successfully or not), this is the finish time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the operation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Partial failures encountered. E.g. single files that couldn't
   * be read. This field should never exceed 20 entries. Status details field
   * will contain standard Google Cloud error details.
   *
   * @param GoogleRpcStatus[] $partialFailures
   */
  public function setPartialFailures($partialFailures)
  {
    $this->partialFailures = $partialFailures;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialFailures()
  {
    return $this->partialFailures;
  }
  /**
   * Output only. Time when the operation was updated for the last time. If the
   * operation has finished (successfully or not), this is the finish time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenericOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenericOperationMetadata');
