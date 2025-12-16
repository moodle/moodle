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

namespace Google\Service\NetworkManagement;

class ShowEffectiveFlowLogsConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $effectiveFlowLogsConfigsType = EffectiveVpcFlowLogsConfig::class;
  protected $effectiveFlowLogsConfigsDataType = 'array';
  /**
   * Page token to fetch the next set of configurations.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached (when querying all locations with `-`).
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * List of Effective Vpc Flow Logs configurations.
   *
   * @param EffectiveVpcFlowLogsConfig[] $effectiveFlowLogsConfigs
   */
  public function setEffectiveFlowLogsConfigs($effectiveFlowLogsConfigs)
  {
    $this->effectiveFlowLogsConfigs = $effectiveFlowLogsConfigs;
  }
  /**
   * @return EffectiveVpcFlowLogsConfig[]
   */
  public function getEffectiveFlowLogsConfigs()
  {
    return $this->effectiveFlowLogsConfigs;
  }
  /**
   * Page token to fetch the next set of configurations.
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
   * Locations that could not be reached (when querying all locations with `-`).
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
class_alias(ShowEffectiveFlowLogsConfigsResponse::class, 'Google_Service_NetworkManagement_ShowEffectiveFlowLogsConfigsResponse');
