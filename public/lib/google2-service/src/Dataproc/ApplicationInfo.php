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

namespace Google\Service\Dataproc;

class ApplicationInfo extends \Google\Collection
{
  public const APPLICATION_CONTEXT_INGESTION_STATUS_APPLICATION_CONTEXT_INGESTION_STATUS_UNSPECIFIED = 'APPLICATION_CONTEXT_INGESTION_STATUS_UNSPECIFIED';
  public const APPLICATION_CONTEXT_INGESTION_STATUS_APPLICATION_CONTEXT_INGESTION_STATUS_COMPLETED = 'APPLICATION_CONTEXT_INGESTION_STATUS_COMPLETED';
  public const QUANTILE_DATA_STATUS_QUANTILE_DATA_STATUS_UNSPECIFIED = 'QUANTILE_DATA_STATUS_UNSPECIFIED';
  public const QUANTILE_DATA_STATUS_QUANTILE_DATA_STATUS_COMPLETED = 'QUANTILE_DATA_STATUS_COMPLETED';
  public const QUANTILE_DATA_STATUS_QUANTILE_DATA_STATUS_FAILED = 'QUANTILE_DATA_STATUS_FAILED';
  protected $collection_key = 'attempts';
  /**
   * @var string
   */
  public $applicationContextIngestionStatus;
  /**
   * @var string
   */
  public $applicationId;
  protected $attemptsType = ApplicationAttemptInfo::class;
  protected $attemptsDataType = 'array';
  /**
   * @var int
   */
  public $coresGranted;
  /**
   * @var int
   */
  public $coresPerExecutor;
  /**
   * @var int
   */
  public $maxCores;
  /**
   * @var int
   */
  public $memoryPerExecutorMb;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $quantileDataStatus;

  /**
   * @param self::APPLICATION_CONTEXT_INGESTION_STATUS_* $applicationContextIngestionStatus
   */
  public function setApplicationContextIngestionStatus($applicationContextIngestionStatus)
  {
    $this->applicationContextIngestionStatus = $applicationContextIngestionStatus;
  }
  /**
   * @return self::APPLICATION_CONTEXT_INGESTION_STATUS_*
   */
  public function getApplicationContextIngestionStatus()
  {
    return $this->applicationContextIngestionStatus;
  }
  /**
   * @param string $applicationId
   */
  public function setApplicationId($applicationId)
  {
    $this->applicationId = $applicationId;
  }
  /**
   * @return string
   */
  public function getApplicationId()
  {
    return $this->applicationId;
  }
  /**
   * @param ApplicationAttemptInfo[] $attempts
   */
  public function setAttempts($attempts)
  {
    $this->attempts = $attempts;
  }
  /**
   * @return ApplicationAttemptInfo[]
   */
  public function getAttempts()
  {
    return $this->attempts;
  }
  /**
   * @param int $coresGranted
   */
  public function setCoresGranted($coresGranted)
  {
    $this->coresGranted = $coresGranted;
  }
  /**
   * @return int
   */
  public function getCoresGranted()
  {
    return $this->coresGranted;
  }
  /**
   * @param int $coresPerExecutor
   */
  public function setCoresPerExecutor($coresPerExecutor)
  {
    $this->coresPerExecutor = $coresPerExecutor;
  }
  /**
   * @return int
   */
  public function getCoresPerExecutor()
  {
    return $this->coresPerExecutor;
  }
  /**
   * @param int $maxCores
   */
  public function setMaxCores($maxCores)
  {
    $this->maxCores = $maxCores;
  }
  /**
   * @return int
   */
  public function getMaxCores()
  {
    return $this->maxCores;
  }
  /**
   * @param int $memoryPerExecutorMb
   */
  public function setMemoryPerExecutorMb($memoryPerExecutorMb)
  {
    $this->memoryPerExecutorMb = $memoryPerExecutorMb;
  }
  /**
   * @return int
   */
  public function getMemoryPerExecutorMb()
  {
    return $this->memoryPerExecutorMb;
  }
  /**
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
   * @param self::QUANTILE_DATA_STATUS_* $quantileDataStatus
   */
  public function setQuantileDataStatus($quantileDataStatus)
  {
    $this->quantileDataStatus = $quantileDataStatus;
  }
  /**
   * @return self::QUANTILE_DATA_STATUS_*
   */
  public function getQuantileDataStatus()
  {
    return $this->quantileDataStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationInfo::class, 'Google_Service_Dataproc_ApplicationInfo');
