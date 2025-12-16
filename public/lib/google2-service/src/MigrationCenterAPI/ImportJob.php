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

namespace Google\Service\MigrationCenterAPI;

class ImportJob extends \Google\Model
{
  /**
   * Default value.
   */
  public const STATE_IMPORT_JOB_STATE_UNSPECIFIED = 'IMPORT_JOB_STATE_UNSPECIFIED';
  /**
   * The import job is pending.
   */
  public const STATE_IMPORT_JOB_STATE_PENDING = 'IMPORT_JOB_STATE_PENDING';
  /**
   * The processing of the import job is ongoing.
   */
  public const STATE_IMPORT_JOB_STATE_RUNNING = 'IMPORT_JOB_STATE_RUNNING';
  /**
   * The import job processing has completed.
   */
  public const STATE_IMPORT_JOB_STATE_COMPLETED = 'IMPORT_JOB_STATE_COMPLETED';
  /**
   * The import job failed to be processed.
   */
  public const STATE_IMPORT_JOB_STATE_FAILED = 'IMPORT_JOB_STATE_FAILED';
  /**
   * The import job is being validated.
   */
  public const STATE_IMPORT_JOB_STATE_VALIDATING = 'IMPORT_JOB_STATE_VALIDATING';
  /**
   * The import job contains blocking errors.
   */
  public const STATE_IMPORT_JOB_STATE_FAILED_VALIDATION = 'IMPORT_JOB_STATE_FAILED_VALIDATION';
  /**
   * The validation of the job completed with no blocking errors.
   */
  public const STATE_IMPORT_JOB_STATE_READY = 'IMPORT_JOB_STATE_READY';
  /**
   * Required. Reference to a source.
   *
   * @var string
   */
  public $assetSource;
  /**
   * Output only. The timestamp when the import job was completed.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The timestamp when the import job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-friendly display name. Maximum length is 256 characters.
   *
   * @var string
   */
  public $displayName;
  protected $executionReportType = ExecutionReport::class;
  protected $executionReportDataType = '';
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The full name of the import job.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the import job.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp when the import job was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $validationReportType = ValidationReport::class;
  protected $validationReportDataType = '';

  /**
   * Required. Reference to a source.
   *
   * @param string $assetSource
   */
  public function setAssetSource($assetSource)
  {
    $this->assetSource = $assetSource;
  }
  /**
   * @return string
   */
  public function getAssetSource()
  {
    return $this->assetSource;
  }
  /**
   * Output only. The timestamp when the import job was completed.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. The timestamp when the import job was created.
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
   * Optional. User-friendly display name. Maximum length is 256 characters.
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
   * Output only. The report with the results of running the import job.
   *
   * @param ExecutionReport $executionReport
   */
  public function setExecutionReport(ExecutionReport $executionReport)
  {
    $this->executionReport = $executionReport;
  }
  /**
   * @return ExecutionReport
   */
  public function getExecutionReport()
  {
    return $this->executionReport;
  }
  /**
   * Labels as key value pairs.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The full name of the import job.
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
   * Output only. The state of the import job.
   *
   * Accepted values: IMPORT_JOB_STATE_UNSPECIFIED, IMPORT_JOB_STATE_PENDING,
   * IMPORT_JOB_STATE_RUNNING, IMPORT_JOB_STATE_COMPLETED,
   * IMPORT_JOB_STATE_FAILED, IMPORT_JOB_STATE_VALIDATING,
   * IMPORT_JOB_STATE_FAILED_VALIDATION, IMPORT_JOB_STATE_READY
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
   * Output only. The timestamp when the import job was last updated.
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
  /**
   * Output only. The report with the validation results of the import job.
   *
   * @param ValidationReport $validationReport
   */
  public function setValidationReport(ValidationReport $validationReport)
  {
    $this->validationReport = $validationReport;
  }
  /**
   * @return ValidationReport
   */
  public function getValidationReport()
  {
    return $this->validationReport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportJob::class, 'Google_Service_MigrationCenterAPI_ImportJob');
