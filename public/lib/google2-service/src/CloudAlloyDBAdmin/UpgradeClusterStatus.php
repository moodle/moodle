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

namespace Google\Service\CloudAlloyDBAdmin;

class UpgradeClusterStatus extends \Google\Collection
{
  /**
   * This is an unknown database version.
   */
  public const SOURCE_VERSION_DATABASE_VERSION_UNSPECIFIED = 'DATABASE_VERSION_UNSPECIFIED';
  /**
   * DEPRECATED - The database version is Postgres 13.
   *
   * @deprecated
   */
  public const SOURCE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is Postgres 14.
   */
  public const SOURCE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is Postgres 15.
   */
  public const SOURCE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is Postgres 16.
   */
  public const SOURCE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is Postgres 17.
   */
  public const SOURCE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * Unspecified status.
   */
  public const STATE_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const STATE_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const STATE_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const STATE_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * This is an unknown database version.
   */
  public const TARGET_VERSION_DATABASE_VERSION_UNSPECIFIED = 'DATABASE_VERSION_UNSPECIFIED';
  /**
   * DEPRECATED - The database version is Postgres 13.
   *
   * @deprecated
   */
  public const TARGET_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is Postgres 14.
   */
  public const TARGET_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is Postgres 15.
   */
  public const TARGET_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is Postgres 16.
   */
  public const TARGET_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is Postgres 17.
   */
  public const TARGET_VERSION_POSTGRES_17 = 'POSTGRES_17';
  protected $collection_key = 'stages';
  /**
   * Whether the operation is cancellable.
   *
   * @var bool
   */
  public $cancellable;
  /**
   * Source database major version.
   *
   * @var string
   */
  public $sourceVersion;
  protected $stagesType = StageStatus::class;
  protected $stagesDataType = 'array';
  /**
   * Cluster Major Version Upgrade state.
   *
   * @var string
   */
  public $state;
  /**
   * Target database major version.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * Whether the operation is cancellable.
   *
   * @param bool $cancellable
   */
  public function setCancellable($cancellable)
  {
    $this->cancellable = $cancellable;
  }
  /**
   * @return bool
   */
  public function getCancellable()
  {
    return $this->cancellable;
  }
  /**
   * Source database major version.
   *
   * Accepted values: DATABASE_VERSION_UNSPECIFIED, POSTGRES_13, POSTGRES_14,
   * POSTGRES_15, POSTGRES_16, POSTGRES_17
   *
   * @param self::SOURCE_VERSION_* $sourceVersion
   */
  public function setSourceVersion($sourceVersion)
  {
    $this->sourceVersion = $sourceVersion;
  }
  /**
   * @return self::SOURCE_VERSION_*
   */
  public function getSourceVersion()
  {
    return $this->sourceVersion;
  }
  /**
   * Status of all upgrade stages.
   *
   * @param StageStatus[] $stages
   */
  public function setStages($stages)
  {
    $this->stages = $stages;
  }
  /**
   * @return StageStatus[]
   */
  public function getStages()
  {
    return $this->stages;
  }
  /**
   * Cluster Major Version Upgrade state.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
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
   * Target database major version.
   *
   * Accepted values: DATABASE_VERSION_UNSPECIFIED, POSTGRES_13, POSTGRES_14,
   * POSTGRES_15, POSTGRES_16, POSTGRES_17
   *
   * @param self::TARGET_VERSION_* $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return self::TARGET_VERSION_*
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeClusterStatus::class, 'Google_Service_CloudAlloyDBAdmin_UpgradeClusterStatus');
