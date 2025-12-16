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

namespace Google\Service\DatabaseMigrationService;

class MigrationJobObject extends \Google\Model
{
  /**
   * The phase of the migration job is unknown.
   */
  public const PHASE_PHASE_UNSPECIFIED = 'PHASE_UNSPECIFIED';
  /**
   * The migration job object is in the full dump phase.
   */
  public const PHASE_FULL_DUMP = 'FULL_DUMP';
  /**
   * The migration job object is in CDC phase.
   */
  public const PHASE_CDC = 'CDC';
  /**
   * The migration job object is ready to be promoted.
   */
  public const PHASE_READY_FOR_PROMOTE = 'READY_FOR_PROMOTE';
  /**
   * The migration job object is in running the promote phase.
   */
  public const PHASE_PROMOTE_IN_PROGRESS = 'PROMOTE_IN_PROGRESS';
  /**
   * The migration job is promoted.
   */
  public const PHASE_PROMOTED = 'PROMOTED';
  /**
   * The migration job object is in the differential backup phase.
   */
  public const PHASE_DIFF_BACKUP = 'DIFF_BACKUP';
  /**
   * The state of the migration job object is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The migration job object is not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * The migration job object is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The migration job object is being stopped.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The migration job object is currently stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The migration job object is restarting.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * The migration job object failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The migration job object is deleting.
   */
  public const STATE_REMOVING = 'REMOVING';
  /**
   * The migration job object is not selected for migration.
   */
  public const STATE_NOT_SELECTED = 'NOT_SELECTED';
  /**
   * The migration job object is completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Output only. The creation time of the migration job object.
   *
   * @var string
   */
  public $createTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  protected $heterogeneousMetadataType = HeterogeneousMetadata::class;
  protected $heterogeneousMetadataDataType = '';
  /**
   * The object's name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The phase of the migration job object.
   *
   * @var string
   */
  public $phase;
  protected $sourceObjectType = SourceObjectIdentifier::class;
  protected $sourceObjectDataType = '';
  /**
   * The state of the migration job object.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last update time of the migration job object.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation time of the migration job object.
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
   * Output only. The error details in case of failure.
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
   * Output only. Metadata for heterogeneous migration jobs objects.
   *
   * @param HeterogeneousMetadata $heterogeneousMetadata
   */
  public function setHeterogeneousMetadata(HeterogeneousMetadata $heterogeneousMetadata)
  {
    $this->heterogeneousMetadata = $heterogeneousMetadata;
  }
  /**
   * @return HeterogeneousMetadata
   */
  public function getHeterogeneousMetadata()
  {
    return $this->heterogeneousMetadata;
  }
  /**
   * The object's name.
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
   * Output only. The phase of the migration job object.
   *
   * Accepted values: PHASE_UNSPECIFIED, FULL_DUMP, CDC, READY_FOR_PROMOTE,
   * PROMOTE_IN_PROGRESS, PROMOTED, DIFF_BACKUP
   *
   * @param self::PHASE_* $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return self::PHASE_*
   */
  public function getPhase()
  {
    return $this->phase;
  }
  /**
   * The object identifier in the data source.
   *
   * @param SourceObjectIdentifier $sourceObject
   */
  public function setSourceObject(SourceObjectIdentifier $sourceObject)
  {
    $this->sourceObject = $sourceObject;
  }
  /**
   * @return SourceObjectIdentifier
   */
  public function getSourceObject()
  {
    return $this->sourceObject;
  }
  /**
   * The state of the migration job object.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_STARTED, RUNNING, STOPPING,
   * STOPPED, RESTARTING, FAILED, REMOVING, NOT_SELECTED, COMPLETED
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
   * Output only. The last update time of the migration job object.
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
class_alias(MigrationJobObject::class, 'Google_Service_DatabaseMigrationService_MigrationJobObject');
