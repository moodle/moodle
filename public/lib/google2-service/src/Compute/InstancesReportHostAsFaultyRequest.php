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

class InstancesReportHostAsFaultyRequest extends \Google\Collection
{
  /**
   * Not used. Required as per aip/126.
   */
  public const DISRUPTION_SCHEDULE_DISRUPTION_SCHEDULE_UNSPECIFIED = 'DISRUPTION_SCHEDULE_UNSPECIFIED';
  /**
   * Delay disruption for caller control. Will be default soon.
   */
  public const DISRUPTION_SCHEDULE_FUTURE = 'FUTURE';
  /**
   * Default value. Disrupt the VM immediately.
   */
  public const DISRUPTION_SCHEDULE_IMMEDIATE = 'IMMEDIATE';
  protected $collection_key = 'faultReasons';
  /**
   * The disruption schedule for the VM. Required field, only allows IMMEDIATE.
   *
   * @var string
   */
  public $disruptionSchedule;
  protected $faultReasonsType = InstancesReportHostAsFaultyRequestFaultReason::class;
  protected $faultReasonsDataType = 'array';

  /**
   * The disruption schedule for the VM. Required field, only allows IMMEDIATE.
   *
   * Accepted values: DISRUPTION_SCHEDULE_UNSPECIFIED, FUTURE, IMMEDIATE
   *
   * @param self::DISRUPTION_SCHEDULE_* $disruptionSchedule
   */
  public function setDisruptionSchedule($disruptionSchedule)
  {
    $this->disruptionSchedule = $disruptionSchedule;
  }
  /**
   * @return self::DISRUPTION_SCHEDULE_*
   */
  public function getDisruptionSchedule()
  {
    return $this->disruptionSchedule;
  }
  /**
   * @param InstancesReportHostAsFaultyRequestFaultReason[] $faultReasons
   */
  public function setFaultReasons($faultReasons)
  {
    $this->faultReasons = $faultReasons;
  }
  /**
   * @return InstancesReportHostAsFaultyRequestFaultReason[]
   */
  public function getFaultReasons()
  {
    return $this->faultReasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesReportHostAsFaultyRequest::class, 'Google_Service_Compute_InstancesReportHostAsFaultyRequest');
