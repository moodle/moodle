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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ListServicesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * A token indicating there are more items than page_size. Use it in the next
   * ListServices request to continue.
   *
   * @var string
   */
  public $nextPageToken;
  protected $servicesType = GoogleCloudRunV2Service::class;
  protected $servicesDataType = 'array';
  /**
   * Output only. For global requests, returns the list of regions that could
   * not be reached within the deadline.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A token indicating there are more items than page_size. Use it in the next
   * ListServices request to continue.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The resulting list of Services.
   *
   * @param GoogleCloudRunV2Service[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return GoogleCloudRunV2Service[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Output only. For global requests, returns the list of regions that could
   * not be reached within the deadline.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ListServicesResponse::class, 'Google_Service_CloudRun_GoogleCloudRunV2ListServicesResponse');
