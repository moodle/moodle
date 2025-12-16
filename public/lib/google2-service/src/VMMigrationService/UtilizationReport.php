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

namespace Google\Service\VMMigrationService;

class UtilizationReport extends \Google\Collection
{
  /**
   * The state is unknown. This value is not in use.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The report is in the making.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Report creation completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Report creation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The time frame was not specified and will default to WEEK.
   */
  public const TIME_FRAME_TIME_FRAME_UNSPECIFIED = 'TIME_FRAME_UNSPECIFIED';
  /**
   * One week.
   */
  public const TIME_FRAME_WEEK = 'WEEK';
  /**
   * One month.
   */
  public const TIME_FRAME_MONTH = 'MONTH';
  /**
   * One year.
   */
  public const TIME_FRAME_YEAR = 'YEAR';
  protected $collection_key = 'vms';
  /**
   * Output only. The time the report was created (this refers to the time of
   * the request, not the time the report creation completed).
   *
   * @var string
   */
  public $createTime;
  /**
   * The report display name, as assigned by the user.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The point in time when the time frame ends. Notice that the
   * time frame is counted backwards. For instance if the "frame_end_time" value
   * is 2021/01/20 and the time frame is WEEK then the report covers the week
   * between 2021/01/20 and 2021/01/14.
   *
   * @var string
   */
  public $frameEndTime;
  /**
   * Output only. The report unique name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Current state of the report.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time the state was last set.
   *
   * @var string
   */
  public $stateTime;
  /**
   * Time frame of the report.
   *
   * @var string
   */
  public $timeFrame;
  /**
   * Output only. Total number of VMs included in the report.
   *
   * @var int
   */
  public $vmCount;
  protected $vmsType = VmUtilizationInfo::class;
  protected $vmsDataType = 'array';

  /**
   * Output only. The time the report was created (this refers to the time of
   * the request, not the time the report creation completed).
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
   * The report display name, as assigned by the user.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Provides details on the state of the report in case of an
   * error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The point in time when the time frame ends. Notice that the
   * time frame is counted backwards. For instance if the "frame_end_time" value
   * is 2021/01/20 and the time frame is WEEK then the report covers the week
   * between 2021/01/20 and 2021/01/14.
   *
   * @param string $frameEndTime
   */
  public function setFrameEndTime($frameEndTime)
  {
    $this->frameEndTime = $frameEndTime;
  }
  /**
   * @return string
   */
  public function getFrameEndTime()
  {
    return $this->frameEndTime;
  }
  /**
   * Output only. The report unique name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Current state of the report.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, SUCCEEDED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time the state was last set.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Time frame of the report.
   *
   * Accepted values: TIME_FRAME_UNSPECIFIED, WEEK, MONTH, YEAR
   *
   * @param self::TIME_FRAME_* $timeFrame
   */
  public function setTimeFrame($timeFrame)
  {
    $this->timeFrame = $timeFrame;
  }
  /**
   * @return self::TIME_FRAME_*
   */
  public function getTimeFrame()
  {
    return $this->timeFrame;
  }
  /**
   * Output only. Total number of VMs included in the report.
   *
   * @param int $vmCount
   */
  public function setVmCount($vmCount)
  {
    $this->vmCount = $vmCount;
  }
  /**
   * @return int
   */
  public function getVmCount()
  {
    return $this->vmCount;
  }
  /**
   * List of utilization information per VM. When sent as part of the request,
   * the "vm_id" field is used in order to specify which VMs to include in the
   * report. In that case all other fields are ignored.
   *
   * @param VmUtilizationInfo[] $vms
   */
  public function setVms($vms)
  {
    $this->vms = $vms;
  }
  /**
   * @return VmUtilizationInfo[]
   */
  public function getVms()
  {
    return $this->vms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UtilizationReport::class, 'Google_Service_VMMigrationService_UtilizationReport');
