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

class DiskMigrationJob extends \Google\Collection
{
  /**
   * The state is unspecified. This is not in use.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The initial state of the disk migration. In this state the customers can
   * update the target details.
   */
  public const STATE_READY = 'READY';
  /**
   * The migration is active, and it's running or scheduled to run.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The migration completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Migration cancellation was initiated.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The migration was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The migration process encountered an unrecoverable error and was aborted.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'steps';
  protected $awsSourceDiskDetailsType = AwsSourceDiskDetails::class;
  protected $awsSourceDiskDetailsDataType = '';
  /**
   * Output only. The time the DiskMigrationJob resource was created.
   *
   * @var string
   */
  public $createTime;
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Identifier. The identifier of the DiskMigrationJob.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the DiskMigrationJob.
   *
   * @var string
   */
  public $state;
  protected $stepsType = DiskMigrationStep::class;
  protected $stepsDataType = 'array';
  protected $targetDetailsType = DiskMigrationJobTargetDetails::class;
  protected $targetDetailsDataType = '';
  /**
   * Output only. The last time the DiskMigrationJob resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Details of the unattached AWS source disk.
   *
   * @param AwsSourceDiskDetails $awsSourceDiskDetails
   */
  public function setAwsSourceDiskDetails(AwsSourceDiskDetails $awsSourceDiskDetails)
  {
    $this->awsSourceDiskDetails = $awsSourceDiskDetails;
  }
  /**
   * @return AwsSourceDiskDetails
   */
  public function getAwsSourceDiskDetails()
  {
    return $this->awsSourceDiskDetails;
  }
  /**
   * Output only. The time the DiskMigrationJob resource was created.
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
   * Output only. Provides details on the errors that led to the disk migration
   * job's state in case of an error.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Identifier. The identifier of the DiskMigrationJob.
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
   * Output only. State of the DiskMigrationJob.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, RUNNING, SUCCEEDED, CANCELLING,
   * CANCELLED, FAILED
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
   * Output only. The disk migration steps list representing its progress.
   *
   * @param DiskMigrationStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return DiskMigrationStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * Required. Details of the target Disk in Compute Engine.
   *
   * @param DiskMigrationJobTargetDetails $targetDetails
   */
  public function setTargetDetails(DiskMigrationJobTargetDetails $targetDetails)
  {
    $this->targetDetails = $targetDetails;
  }
  /**
   * @return DiskMigrationJobTargetDetails
   */
  public function getTargetDetails()
  {
    return $this->targetDetails;
  }
  /**
   * Output only. The last time the DiskMigrationJob resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskMigrationJob::class, 'Google_Service_VMMigrationService_DiskMigrationJob');
