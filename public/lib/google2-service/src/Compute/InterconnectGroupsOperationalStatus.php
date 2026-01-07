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

namespace Google\Service\Compute;

class InterconnectGroupsOperationalStatus extends \Google\Collection
{
  public const GROUP_STATUS_DEGRADED = 'DEGRADED';
  public const GROUP_STATUS_FULLY_DOWN = 'FULLY_DOWN';
  public const GROUP_STATUS_FULLY_UP = 'FULLY_UP';
  public const GROUP_STATUS_GROUPS_STATUS_UNSPECIFIED = 'GROUPS_STATUS_UNSPECIFIED';
  protected $collection_key = 'interconnectStatuses';
  protected $configuredType = InterconnectGroupConfigured::class;
  protected $configuredDataType = '';
  /**
   * Output only. Summarizes the status of the group.
   *
   * @var string
   */
  public $groupStatus;
  protected $intentType = InterconnectGroupIntent::class;
  protected $intentDataType = '';
  protected $interconnectStatusesType = InterconnectGroupsOperationalStatusInterconnectStatus::class;
  protected $interconnectStatusesDataType = 'array';
  protected $operationalType = InterconnectGroupConfigured::class;
  protected $operationalDataType = '';

  /**
   * Output only. The configuration analysis, as returned by Get.
   *
   * @param InterconnectGroupConfigured $configured
   */
  public function setConfigured(InterconnectGroupConfigured $configured)
  {
    $this->configured = $configured;
  }
  /**
   * @return InterconnectGroupConfigured
   */
  public function getConfigured()
  {
    return $this->configured;
  }
  /**
   * Output only. Summarizes the status of the group.
   *
   * Accepted values: DEGRADED, FULLY_DOWN, FULLY_UP, GROUPS_STATUS_UNSPECIFIED
   *
   * @param self::GROUP_STATUS_* $groupStatus
   */
  public function setGroupStatus($groupStatus)
  {
    $this->groupStatus = $groupStatus;
  }
  /**
   * @return self::GROUP_STATUS_*
   */
  public function getGroupStatus()
  {
    return $this->groupStatus;
  }
  /**
   * Output only. The intent of the resource, as returned by Get.
   *
   * @param InterconnectGroupIntent $intent
   */
  public function setIntent(InterconnectGroupIntent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return InterconnectGroupIntent
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * @param InterconnectGroupsOperationalStatusInterconnectStatus[] $interconnectStatuses
   */
  public function setInterconnectStatuses($interconnectStatuses)
  {
    $this->interconnectStatuses = $interconnectStatuses;
  }
  /**
   * @return InterconnectGroupsOperationalStatusInterconnectStatus[]
   */
  public function getInterconnectStatuses()
  {
    return $this->interconnectStatuses;
  }
  /**
   * Output only. The operational state of the group, including only active
   * Interconnects.
   *
   * @param InterconnectGroupConfigured $operational
   */
  public function setOperational(InterconnectGroupConfigured $operational)
  {
    $this->operational = $operational;
  }
  /**
   * @return InterconnectGroupConfigured
   */
  public function getOperational()
  {
    return $this->operational;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupsOperationalStatus::class, 'Google_Service_Compute_InterconnectGroupsOperationalStatus');
