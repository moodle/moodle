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

class ListInstanceConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'instanceConfigs';
  protected $instanceConfigsType = InstanceConfig::class;
  protected $instanceConfigsDataType = 'array';
  /**
   * `next_page_token` can be sent in a subsequent ListInstanceConfigs call to
   * fetch more of the matching instance configurations.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of requested instance configurations.
   *
   * @param InstanceConfig[] $instanceConfigs
   */
  public function setInstanceConfigs($instanceConfigs)
  {
    $this->instanceConfigs = $instanceConfigs;
  }
  /**
   * @return InstanceConfig[]
   */
  public function getInstanceConfigs()
  {
    return $this->instanceConfigs;
  }
  /**
   * `next_page_token` can be sent in a subsequent ListInstanceConfigs call to
   * fetch more of the matching instance configurations.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListInstanceConfigsResponse::class, 'Google_Service_Spanner_ListInstanceConfigsResponse');
