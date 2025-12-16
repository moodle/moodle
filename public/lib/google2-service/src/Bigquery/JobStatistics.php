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

namespace Google\Service\Bigquery;

class JobStatistics extends \Google\Collection
{
  /**
   * Default value, which will be treated as ENTERPRISE.
   */
  public const EDITION_RESERVATION_EDITION_UNSPECIFIED = 'RESERVATION_EDITION_UNSPECIFIED';
  /**
   * Standard edition.
   */
  public const EDITION_STANDARD = 'STANDARD';
  /**
   * Enterprise edition.
   */
  public const EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * Enterprise Plus edition.
   */
  public const EDITION_ENTERPRISE_PLUS = 'ENTERPRISE_PLUS';
  protected $collection_key = 'reservationUsage';
  protected $internal_gapi_mappings = [
        "reservationId" => "reservation_id",
  ];
  /**
   * Output only. [TrustedTester] Job progress (0.0 -> 1.0) for LOAD and EXTRACT
   * jobs.
   *
   * @var 
   */
  public $completionRatio;
  protected $copyType = JobStatistics5::class;
  protected $copyDataType = '';
  /**
   * Output only. Creation time of this job, in milliseconds since the epoch.
   * This field will be present on all jobs.
   *
   * @var string
   */
  public $creationTime;
  protected $dataMaskingStatisticsType = DataMaskingStatistics::class;
  protected $dataMaskingStatisticsDataType = '';
  /**
   * Output only. Name of edition corresponding to the reservation for this job
   * at the time of this update.
   *
   * @var string
   */
  public $edition;
  /**
   * Output only. End time of this job, in milliseconds since the epoch. This
   * field will be present whenever a job is in the DONE state.
   *
   * @var string
   */
  public $endTime;
  protected $extractType = JobStatistics4::class;
  protected $extractDataType = '';
  /**
   * Output only. The duration in milliseconds of the execution of the final
   * attempt of this job, as BigQuery may internally re-attempt to execute the
   * job.
   *
   * @var string
   */
  public $finalExecutionDurationMs;
  protected $loadType = JobStatistics3::class;
  protected $loadDataType = '';
  /**
   * Output only. Number of child jobs executed.
   *
   * @var string
   */
  public $numChildJobs;
  /**
   * Output only. If this is a child job, specifies the job ID of the parent.
   *
   * @var string
   */
  public $parentJobId;
  protected $queryType = JobStatistics2::class;
  protected $queryDataType = '';
  /**
   * Output only. Quotas which delayed this job's start time.
   *
   * @var string[]
   */
  public $quotaDeferments;
  /**
   * Output only. The reservation group path of the reservation assigned to this
   * job. This field has a limit of 10 nested reservation groups. This is to
   * maintain consistency between reservatins info schema and jobs info schema.
   * The first reservation group is the root reservation group and the last is
   * the leaf or lowest level reservation group.
   *
   * @var string[]
   */
  public $reservationGroupPath;
  protected $reservationUsageType = JobStatisticsReservationUsage::class;
  protected $reservationUsageDataType = 'array';
  /**
   * Output only. Name of the primary reservation assigned to this job. Note
   * that this could be different than reservations reported in the reservation
   * usage field if parent reservations were used to execute this job.
   *
   * @var string
   */
  public $reservationId;
  protected $rowLevelSecurityStatisticsType = RowLevelSecurityStatistics::class;
  protected $rowLevelSecurityStatisticsDataType = '';
  protected $scriptStatisticsType = ScriptStatistics::class;
  protected $scriptStatisticsDataType = '';
  protected $sessionInfoType = SessionInfo::class;
  protected $sessionInfoDataType = '';
  /**
   * Output only. Start time of this job, in milliseconds since the epoch. This
   * field will be present when the job transitions from the PENDING state to
   * either RUNNING or DONE.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Total bytes processed for the job.
   *
   * @var string
   */
  public $totalBytesProcessed;
  /**
   * Output only. Slot-milliseconds for the job.
   *
   * @var string
   */
  public $totalSlotMs;
  protected $transactionInfoType = TransactionInfo::class;
  protected $transactionInfoDataType = '';

  public function setCompletionRatio($completionRatio)
  {
    $this->completionRatio = $completionRatio;
  }
  public function getCompletionRatio()
  {
    return $this->completionRatio;
  }
  /**
   * Output only. Statistics for a copy job.
   *
   * @param JobStatistics5 $copy
   */
  public function setCopy(JobStatistics5 $copy)
  {
    $this->copy = $copy;
  }
  /**
   * @return JobStatistics5
   */
  public function getCopy()
  {
    return $this->copy;
  }
  /**
   * Output only. Creation time of this job, in milliseconds since the epoch.
   * This field will be present on all jobs.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. Statistics for data-masking. Present only for query and
   * extract jobs.
   *
   * @param DataMaskingStatistics $dataMaskingStatistics
   */
  public function setDataMaskingStatistics(DataMaskingStatistics $dataMaskingStatistics)
  {
    $this->dataMaskingStatistics = $dataMaskingStatistics;
  }
  /**
   * @return DataMaskingStatistics
   */
  public function getDataMaskingStatistics()
  {
    return $this->dataMaskingStatistics;
  }
  /**
   * Output only. Name of edition corresponding to the reservation for this job
   * at the time of this update.
   *
   * Accepted values: RESERVATION_EDITION_UNSPECIFIED, STANDARD, ENTERPRISE,
   * ENTERPRISE_PLUS
   *
   * @param self::EDITION_* $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return self::EDITION_*
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Output only. End time of this job, in milliseconds since the epoch. This
   * field will be present whenever a job is in the DONE state.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Statistics for an extract job.
   *
   * @param JobStatistics4 $extract
   */
  public function setExtract(JobStatistics4 $extract)
  {
    $this->extract = $extract;
  }
  /**
   * @return JobStatistics4
   */
  public function getExtract()
  {
    return $this->extract;
  }
  /**
   * Output only. The duration in milliseconds of the execution of the final
   * attempt of this job, as BigQuery may internally re-attempt to execute the
   * job.
   *
   * @param string $finalExecutionDurationMs
   */
  public function setFinalExecutionDurationMs($finalExecutionDurationMs)
  {
    $this->finalExecutionDurationMs = $finalExecutionDurationMs;
  }
  /**
   * @return string
   */
  public function getFinalExecutionDurationMs()
  {
    return $this->finalExecutionDurationMs;
  }
  /**
   * Output only. Statistics for a load job.
   *
   * @param JobStatistics3 $load
   */
  public function setLoad(JobStatistics3 $load)
  {
    $this->load = $load;
  }
  /**
   * @return JobStatistics3
   */
  public function getLoad()
  {
    return $this->load;
  }
  /**
   * Output only. Number of child jobs executed.
   *
   * @param string $numChildJobs
   */
  public function setNumChildJobs($numChildJobs)
  {
    $this->numChildJobs = $numChildJobs;
  }
  /**
   * @return string
   */
  public function getNumChildJobs()
  {
    return $this->numChildJobs;
  }
  /**
   * Output only. If this is a child job, specifies the job ID of the parent.
   *
   * @param string $parentJobId
   */
  public function setParentJobId($parentJobId)
  {
    $this->parentJobId = $parentJobId;
  }
  /**
   * @return string
   */
  public function getParentJobId()
  {
    return $this->parentJobId;
  }
  /**
   * Output only. Statistics for a query job.
   *
   * @param JobStatistics2 $query
   */
  public function setQuery(JobStatistics2 $query)
  {
    $this->query = $query;
  }
  /**
   * @return JobStatistics2
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Output only. Quotas which delayed this job's start time.
   *
   * @param string[] $quotaDeferments
   */
  public function setQuotaDeferments($quotaDeferments)
  {
    $this->quotaDeferments = $quotaDeferments;
  }
  /**
   * @return string[]
   */
  public function getQuotaDeferments()
  {
    return $this->quotaDeferments;
  }
  /**
   * Output only. The reservation group path of the reservation assigned to this
   * job. This field has a limit of 10 nested reservation groups. This is to
   * maintain consistency between reservatins info schema and jobs info schema.
   * The first reservation group is the root reservation group and the last is
   * the leaf or lowest level reservation group.
   *
   * @param string[] $reservationGroupPath
   */
  public function setReservationGroupPath($reservationGroupPath)
  {
    $this->reservationGroupPath = $reservationGroupPath;
  }
  /**
   * @return string[]
   */
  public function getReservationGroupPath()
  {
    return $this->reservationGroupPath;
  }
  /**
   * Output only. Job resource usage breakdown by reservation. This field
   * reported misleading information and will no longer be populated.
   *
   * @deprecated
   * @param JobStatisticsReservationUsage[] $reservationUsage
   */
  public function setReservationUsage($reservationUsage)
  {
    $this->reservationUsage = $reservationUsage;
  }
  /**
   * @deprecated
   * @return JobStatisticsReservationUsage[]
   */
  public function getReservationUsage()
  {
    return $this->reservationUsage;
  }
  /**
   * Output only. Name of the primary reservation assigned to this job. Note
   * that this could be different than reservations reported in the reservation
   * usage field if parent reservations were used to execute this job.
   *
   * @param string $reservationId
   */
  public function setReservationId($reservationId)
  {
    $this->reservationId = $reservationId;
  }
  /**
   * @return string
   */
  public function getReservationId()
  {
    return $this->reservationId;
  }
  /**
   * Output only. Statistics for row-level security. Present only for query and
   * extract jobs.
   *
   * @param RowLevelSecurityStatistics $rowLevelSecurityStatistics
   */
  public function setRowLevelSecurityStatistics(RowLevelSecurityStatistics $rowLevelSecurityStatistics)
  {
    $this->rowLevelSecurityStatistics = $rowLevelSecurityStatistics;
  }
  /**
   * @return RowLevelSecurityStatistics
   */
  public function getRowLevelSecurityStatistics()
  {
    return $this->rowLevelSecurityStatistics;
  }
  /**
   * Output only. If this a child job of a script, specifies information about
   * the context of this job within the script.
   *
   * @param ScriptStatistics $scriptStatistics
   */
  public function setScriptStatistics(ScriptStatistics $scriptStatistics)
  {
    $this->scriptStatistics = $scriptStatistics;
  }
  /**
   * @return ScriptStatistics
   */
  public function getScriptStatistics()
  {
    return $this->scriptStatistics;
  }
  /**
   * Output only. Information of the session if this job is part of one.
   *
   * @param SessionInfo $sessionInfo
   */
  public function setSessionInfo(SessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return SessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
  }
  /**
   * Output only. Start time of this job, in milliseconds since the epoch. This
   * field will be present when the job transitions from the PENDING state to
   * either RUNNING or DONE.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Total bytes processed for the job.
   *
   * @param string $totalBytesProcessed
   */
  public function setTotalBytesProcessed($totalBytesProcessed)
  {
    $this->totalBytesProcessed = $totalBytesProcessed;
  }
  /**
   * @return string
   */
  public function getTotalBytesProcessed()
  {
    return $this->totalBytesProcessed;
  }
  /**
   * Output only. Slot-milliseconds for the job.
   *
   * @param string $totalSlotMs
   */
  public function setTotalSlotMs($totalSlotMs)
  {
    $this->totalSlotMs = $totalSlotMs;
  }
  /**
   * @return string
   */
  public function getTotalSlotMs()
  {
    return $this->totalSlotMs;
  }
  /**
   * Output only. [Alpha] Information of the multi-statement transaction if this
   * job is part of one. This property is only expected on a child job or a job
   * that is in a session. A script parent job is not part of the transaction
   * started in the script.
   *
   * @param TransactionInfo $transactionInfo
   */
  public function setTransactionInfo(TransactionInfo $transactionInfo)
  {
    $this->transactionInfo = $transactionInfo;
  }
  /**
   * @return TransactionInfo
   */
  public function getTransactionInfo()
  {
    return $this->transactionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobStatistics::class, 'Google_Service_Bigquery_JobStatistics');
