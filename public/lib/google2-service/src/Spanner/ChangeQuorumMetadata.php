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

namespace Google\Service\Spanner;

class ChangeQuorumMetadata extends \Google\Model
{
  /**
   * If set, the time at which this operation failed or was completed
   * successfully.
   *
   * @var string
   */
  public $endTime;
  protected $requestType = ChangeQuorumRequest::class;
  protected $requestDataType = '';
  /**
   * Time the request was received.
   *
   * @var string
   */
  public $startTime;

  /**
   * If set, the time at which this operation failed or was completed
   * successfully.
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
   * The request for ChangeQuorum.
   *
   * @param ChangeQuorumRequest $request
   */
  public function setRequest(ChangeQuorumRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return ChangeQuorumRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Time the request was received.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangeQuorumMetadata::class, 'Google_Service_Spanner_ChangeQuorumMetadata');
