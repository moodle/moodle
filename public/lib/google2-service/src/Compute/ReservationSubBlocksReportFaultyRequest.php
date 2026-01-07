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

class ReservationSubBlocksReportFaultyRequest extends \Google\Collection
{
  public const DISRUPTION_SCHEDULE_DISRUPTION_SCHEDULE_UNSPECIFIED = 'DISRUPTION_SCHEDULE_UNSPECIFIED';
  /**
   * All VMs will be disrupted immediately.
   */
  public const DISRUPTION_SCHEDULE_IMMEDIATE = 'IMMEDIATE';
  public const FAILURE_COMPONENT_FAILURE_COMPONENT_UNSPECIFIED = 'FAILURE_COMPONENT_UNSPECIFIED';
  /**
   * Multiple hosts experienced the fault.
   */
  public const FAILURE_COMPONENT_MULTIPLE_FAULTY_HOSTS = 'MULTIPLE_FAULTY_HOSTS';
  /**
   * The NVLink switch experienced the fault.
   */
  public const FAILURE_COMPONENT_NVLINK_SWITCH = 'NVLINK_SWITCH';
  protected $collection_key = 'faultReasons';
  /**
   * The disruption schedule for the subBlock.
   *
   * @var string
   */
  public $disruptionSchedule;
  /**
   * The component that experienced the fault.
   *
   * @var string
   */
  public $failureComponent;
  protected $faultReasonsType = ReservationSubBlocksReportFaultyRequestFaultReason::class;
  protected $faultReasonsDataType = 'array';

  /**
   * The disruption schedule for the subBlock.
   *
   * Accepted values: DISRUPTION_SCHEDULE_UNSPECIFIED, IMMEDIATE
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
   * The component that experienced the fault.
   *
   * Accepted values: FAILURE_COMPONENT_UNSPECIFIED, MULTIPLE_FAULTY_HOSTS,
   * NVLINK_SWITCH
   *
   * @param self::FAILURE_COMPONENT_* $failureComponent
   */
  public function setFailureComponent($failureComponent)
  {
    $this->failureComponent = $failureComponent;
  }
  /**
   * @return self::FAILURE_COMPONENT_*
   */
  public function getFailureComponent()
  {
    return $this->failureComponent;
  }
  /**
   * The reasons for the fault experienced with the subBlock.
   *
   * @param ReservationSubBlocksReportFaultyRequestFaultReason[] $faultReasons
   */
  public function setFaultReasons($faultReasons)
  {
    $this->faultReasons = $faultReasons;
  }
  /**
   * @return ReservationSubBlocksReportFaultyRequestFaultReason[]
   */
  public function getFaultReasons()
  {
    return $this->faultReasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlocksReportFaultyRequest::class, 'Google_Service_Compute_ReservationSubBlocksReportFaultyRequest');
