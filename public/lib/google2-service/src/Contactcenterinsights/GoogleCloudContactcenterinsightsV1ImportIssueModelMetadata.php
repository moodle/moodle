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

class GoogleCloudContactcenterinsightsV1ImportIssueModelMetadata extends \Google\Model
{
  /**
   * The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  protected $requestType = GoogleCloudContactcenterinsightsV1ImportIssueModelRequest::class;
  protected $requestDataType = '';

  /**
   * The time the operation was created.
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
   * The time the operation finished running.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The original import request.
   *
   * @param GoogleCloudContactcenterinsightsV1ImportIssueModelRequest $request
   */
  public function setRequest(GoogleCloudContactcenterinsightsV1ImportIssueModelRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ImportIssueModelRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ImportIssueModelMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ImportIssueModelMetadata');
