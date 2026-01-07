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

class BackgroundJobLogEntry extends \Google\Model
{
  /**
   * The status is not specified. This state is used when job is not yet
   * finished.
   */
  public const COMPLETION_STATE_JOB_COMPLETION_STATE_UNSPECIFIED = 'JOB_COMPLETION_STATE_UNSPECIFIED';
  /**
   * Success.
   */
  public const COMPLETION_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Error.
   */
  public const COMPLETION_STATE_FAILED = 'FAILED';
  /**
   * Unspecified background job type.
   */
  public const JOB_TYPE_BACKGROUND_JOB_TYPE_UNSPECIFIED = 'BACKGROUND_JOB_TYPE_UNSPECIFIED';
  /**
   * Job to seed from the source database.
   */
  public const JOB_TYPE_BACKGROUND_JOB_TYPE_SOURCE_SEED = 'BACKGROUND_JOB_TYPE_SOURCE_SEED';
  /**
   * Job to convert the source database into a draft of the destination
   * database.
   */
  public const JOB_TYPE_BACKGROUND_JOB_TYPE_CONVERT = 'BACKGROUND_JOB_TYPE_CONVERT';
  /**
   * Job to apply the draft tree onto the destination.
   */
  public const JOB_TYPE_BACKGROUND_JOB_TYPE_APPLY_DESTINATION = 'BACKGROUND_JOB_TYPE_APPLY_DESTINATION';
  /**
   * Job to import and convert mapping rules from an external source such as an
   * ora2pg config file.
   */
  public const JOB_TYPE_BACKGROUND_JOB_TYPE_IMPORT_RULES_FILE = 'BACKGROUND_JOB_TYPE_IMPORT_RULES_FILE';
  protected $applyJobDetailsType = ApplyJobDetails::class;
  protected $applyJobDetailsDataType = '';
  /**
   * Output only. Job completion comment, such as how many entities were seeded,
   * how many warnings were found during conversion, and similar information.
   *
   * @var string
   */
  public $completionComment;
  /**
   * Output only. Job completion state, i.e. the final state after the job
   * completed.
   *
   * @var string
   */
  public $completionState;
  protected $convertJobDetailsType = ConvertJobDetails::class;
  protected $convertJobDetailsDataType = '';
  /**
   * The timestamp when the background job was finished.
   *
   * @var string
   */
  public $finishTime;
  /**
   * The background job log entry ID.
   *
   * @var string
   */
  public $id;
  protected $importRulesJobDetailsType = ImportRulesJobDetails::class;
  protected $importRulesJobDetailsDataType = '';
  /**
   * The type of job that was executed.
   *
   * @var string
   */
  public $jobType;
  /**
   * Output only. Whether the client requested the conversion workspace to be
   * committed after a successful completion of the job.
   *
   * @var bool
   */
  public $requestAutocommit;
  protected $seedJobDetailsType = SeedJobDetails::class;
  protected $seedJobDetailsDataType = '';
  /**
   * The timestamp when the background job was started.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. Apply job details.
   *
   * @param ApplyJobDetails $applyJobDetails
   */
  public function setApplyJobDetails(ApplyJobDetails $applyJobDetails)
  {
    $this->applyJobDetails = $applyJobDetails;
  }
  /**
   * @return ApplyJobDetails
   */
  public function getApplyJobDetails()
  {
    return $this->applyJobDetails;
  }
  /**
   * Output only. Job completion comment, such as how many entities were seeded,
   * how many warnings were found during conversion, and similar information.
   *
   * @param string $completionComment
   */
  public function setCompletionComment($completionComment)
  {
    $this->completionComment = $completionComment;
  }
  /**
   * @return string
   */
  public function getCompletionComment()
  {
    return $this->completionComment;
  }
  /**
   * Output only. Job completion state, i.e. the final state after the job
   * completed.
   *
   * Accepted values: JOB_COMPLETION_STATE_UNSPECIFIED, SUCCEEDED, FAILED
   *
   * @param self::COMPLETION_STATE_* $completionState
   */
  public function setCompletionState($completionState)
  {
    $this->completionState = $completionState;
  }
  /**
   * @return self::COMPLETION_STATE_*
   */
  public function getCompletionState()
  {
    return $this->completionState;
  }
  /**
   * Output only. Convert job details.
   *
   * @param ConvertJobDetails $convertJobDetails
   */
  public function setConvertJobDetails(ConvertJobDetails $convertJobDetails)
  {
    $this->convertJobDetails = $convertJobDetails;
  }
  /**
   * @return ConvertJobDetails
   */
  public function getConvertJobDetails()
  {
    return $this->convertJobDetails;
  }
  /**
   * The timestamp when the background job was finished.
   *
   * @param string $finishTime
   */
  public function setFinishTime($finishTime)
  {
    $this->finishTime = $finishTime;
  }
  /**
   * @return string
   */
  public function getFinishTime()
  {
    return $this->finishTime;
  }
  /**
   * The background job log entry ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Import rules job details.
   *
   * @param ImportRulesJobDetails $importRulesJobDetails
   */
  public function setImportRulesJobDetails(ImportRulesJobDetails $importRulesJobDetails)
  {
    $this->importRulesJobDetails = $importRulesJobDetails;
  }
  /**
   * @return ImportRulesJobDetails
   */
  public function getImportRulesJobDetails()
  {
    return $this->importRulesJobDetails;
  }
  /**
   * The type of job that was executed.
   *
   * Accepted values: BACKGROUND_JOB_TYPE_UNSPECIFIED,
   * BACKGROUND_JOB_TYPE_SOURCE_SEED, BACKGROUND_JOB_TYPE_CONVERT,
   * BACKGROUND_JOB_TYPE_APPLY_DESTINATION,
   * BACKGROUND_JOB_TYPE_IMPORT_RULES_FILE
   *
   * @param self::JOB_TYPE_* $jobType
   */
  public function setJobType($jobType)
  {
    $this->jobType = $jobType;
  }
  /**
   * @return self::JOB_TYPE_*
   */
  public function getJobType()
  {
    return $this->jobType;
  }
  /**
   * Output only. Whether the client requested the conversion workspace to be
   * committed after a successful completion of the job.
   *
   * @param bool $requestAutocommit
   */
  public function setRequestAutocommit($requestAutocommit)
  {
    $this->requestAutocommit = $requestAutocommit;
  }
  /**
   * @return bool
   */
  public function getRequestAutocommit()
  {
    return $this->requestAutocommit;
  }
  /**
   * Output only. Seed job details.
   *
   * @param SeedJobDetails $seedJobDetails
   */
  public function setSeedJobDetails(SeedJobDetails $seedJobDetails)
  {
    $this->seedJobDetails = $seedJobDetails;
  }
  /**
   * @return SeedJobDetails
   */
  public function getSeedJobDetails()
  {
    return $this->seedJobDetails;
  }
  /**
   * The timestamp when the background job was started.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackgroundJobLogEntry::class, 'Google_Service_DatabaseMigrationService_BackgroundJobLogEntry');
