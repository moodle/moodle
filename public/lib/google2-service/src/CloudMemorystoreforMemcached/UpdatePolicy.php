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

namespace Google\Service\CloudMemorystoreforMemcached;

class UpdatePolicy extends \Google\Collection
{
  /**
   * Unspecified channel.
   */
  public const CHANNEL_UPDATE_CHANNEL_UNSPECIFIED = 'UPDATE_CHANNEL_UNSPECIFIED';
  /**
   * Early channel within a customer project.
   */
  public const CHANNEL_EARLIER = 'EARLIER';
  /**
   * Later channel within a customer project.
   */
  public const CHANNEL_LATER = 'LATER';
  /**
   * ! ! The follow channels can ONLY be used if you adopt the new MW system! !
   * ! NOTE: all WEEK channels are assumed to be under a weekly window. ! There
   * is currently no dedicated channel definitions for Daily windows. ! If you
   * use Daily window, the system will assume a 1d (24Hours) advanced !
   * notification period b/w EARLY and LATER. ! We may consider support more
   * flexible daily channel specifications in ! the future. WEEK1 == EARLIER
   * with minimum 7d advanced notification. {7d, 14d} The system will treat them
   * equally and will use WEEK1 whenever it can. New customers are encouraged to
   * use this channel annotation.
   */
  public const CHANNEL_WEEK1 = 'WEEK1';
  /**
   * WEEK2 == LATER with minimum 14d advanced notification {14d, 21d}.
   */
  public const CHANNEL_WEEK2 = 'WEEK2';
  /**
   * WEEK5 == 40d support. minimum 35d advanced notification {35d, 42d}.
   */
  public const CHANNEL_WEEK5 = 'WEEK5';
  protected $collection_key = 'denyMaintenancePeriods';
  /**
   * Optional. Relative scheduling channel applied to resource.
   *
   * @var string
   */
  public $channel;
  protected $denyMaintenancePeriodsType = DenyMaintenancePeriod::class;
  protected $denyMaintenancePeriodsDataType = 'array';
  protected $windowType = MaintenanceWindow::class;
  protected $windowDataType = '';

  /**
   * Optional. Relative scheduling channel applied to resource.
   *
   * Accepted values: UPDATE_CHANNEL_UNSPECIFIED, EARLIER, LATER, WEEK1, WEEK2,
   * WEEK5
   *
   * @param self::CHANNEL_* $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return self::CHANNEL_*
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Deny Maintenance Period that is applied to resource to indicate when
   * maintenance is forbidden. The protocol supports zero-to-many such periods,
   * but the current SLM Rollout implementation only supports zero-to-one.
   *
   * @param DenyMaintenancePeriod[] $denyMaintenancePeriods
   */
  public function setDenyMaintenancePeriods($denyMaintenancePeriods)
  {
    $this->denyMaintenancePeriods = $denyMaintenancePeriods;
  }
  /**
   * @return DenyMaintenancePeriod[]
   */
  public function getDenyMaintenancePeriods()
  {
    return $this->denyMaintenancePeriods;
  }
  /**
   * Optional. Maintenance window that is applied to resources covered by this
   * policy.
   *
   * @param MaintenanceWindow $window
   */
  public function setWindow(MaintenanceWindow $window)
  {
    $this->window = $window;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getWindow()
  {
    return $this->window;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatePolicy::class, 'Google_Service_CloudMemorystoreforMemcached_UpdatePolicy');
