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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DlpJob extends \Google\Collection
{
  /**
   * Unused.
   */
  public const STATE_JOB_STATE_UNSPECIFIED = 'JOB_STATE_UNSPECIFIED';
  /**
   * The job has not yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The job is currently running. Once a job has finished it will transition to
   * FAILED or DONE.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job is no longer running.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The job was canceled before it could be completed.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * The job had an error and did not complete.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job is currently accepting findings via hybridInspect. A hybrid job in
   * ACTIVE state may continue to have findings added to it through the calling
   * of hybridInspect. After the job has finished no more calls to hybridInspect
   * may be made. ACTIVE jobs can transition to DONE.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Defaults to INSPECT_JOB.
   */
  public const TYPE_DLP_JOB_TYPE_UNSPECIFIED = 'DLP_JOB_TYPE_UNSPECIFIED';
  /**
   * The job inspected Google Cloud for sensitive data.
   */
  public const TYPE_INSPECT_JOB = 'INSPECT_JOB';
  /**
   * The job executed a Risk Analysis computation.
   */
  public const TYPE_RISK_ANALYSIS_JOB = 'RISK_ANALYSIS_JOB';
  protected $collection_key = 'errors';
  protected $actionDetailsType = GooglePrivacyDlpV2ActionDetails::class;
  protected $actionDetailsDataType = 'array';
  /**
   * Time when the job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Time when the job finished.
   *
   * @var string
   */
  public $endTime;
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  protected $inspectDetailsType = GooglePrivacyDlpV2InspectDataSourceDetails::class;
  protected $inspectDetailsDataType = '';
  /**
   * If created by a job trigger, the resource name of the trigger that
   * instantiated the job.
   *
   * @var string
   */
  public $jobTriggerName;
  /**
   * Time when the job was last modified by the system.
   *
   * @var string
   */
  public $lastModified;
  /**
   * The server-assigned name.
   *
   * @var string
   */
  public $name;
  protected $riskDetailsType = GooglePrivacyDlpV2AnalyzeDataSourceRiskDetails::class;
  protected $riskDetailsDataType = '';
  /**
   * Time when the job started.
   *
   * @var string
   */
  public $startTime;
  /**
   * State of a job.
   *
   * @var string
   */
  public $state;
  /**
   * The type of job.
   *
   * @var string
   */
  public $type;

  /**
   * Events that should occur after the job has completed.
   *
   * @param GooglePrivacyDlpV2ActionDetails[] $actionDetails
   */
  public function setActionDetails($actionDetails)
  {
    $this->actionDetails = $actionDetails;
  }
  /**
   * @return GooglePrivacyDlpV2ActionDetails[]
   */
  public function getActionDetails()
  {
    return $this->actionDetails;
  }
  /**
   * Time when the job was created.
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
   * Time when the job finished.
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
   * A stream of errors encountered running the job.
   *
   * @param GooglePrivacyDlpV2Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GooglePrivacyDlpV2Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Results from inspecting a data source.
   *
   * @param GooglePrivacyDlpV2InspectDataSourceDetails $inspectDetails
   */
  public function setInspectDetails(GooglePrivacyDlpV2InspectDataSourceDetails $inspectDetails)
  {
    $this->inspectDetails = $inspectDetails;
  }
  /**
   * @return GooglePrivacyDlpV2InspectDataSourceDetails
   */
  public function getInspectDetails()
  {
    return $this->inspectDetails;
  }
  /**
   * If created by a job trigger, the resource name of the trigger that
   * instantiated the job.
   *
   * @param string $jobTriggerName
   */
  public function setJobTriggerName($jobTriggerName)
  {
    $this->jobTriggerName = $jobTriggerName;
  }
  /**
   * @return string
   */
  public function getJobTriggerName()
  {
    return $this->jobTriggerName;
  }
  /**
   * Time when the job was last modified by the system.
   *
   * @param string $lastModified
   */
  public function setLastModified($lastModified)
  {
    $this->lastModified = $lastModified;
  }
  /**
   * @return string
   */
  public function getLastModified()
  {
    return $this->lastModified;
  }
  /**
   * The server-assigned name.
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
   * Results from analyzing risk of a data source.
   *
   * @param GooglePrivacyDlpV2AnalyzeDataSourceRiskDetails $riskDetails
   */
  public function setRiskDetails(GooglePrivacyDlpV2AnalyzeDataSourceRiskDetails $riskDetails)
  {
    $this->riskDetails = $riskDetails;
  }
  /**
   * @return GooglePrivacyDlpV2AnalyzeDataSourceRiskDetails
   */
  public function getRiskDetails()
  {
    return $this->riskDetails;
  }
  /**
   * Time when the job started.
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
   * State of a job.
   *
   * Accepted values: JOB_STATE_UNSPECIFIED, PENDING, RUNNING, DONE, CANCELED,
   * FAILED, ACTIVE
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
   * The type of job.
   *
   * Accepted values: DLP_JOB_TYPE_UNSPECIFIED, INSPECT_JOB, RISK_ANALYSIS_JOB
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DlpJob::class, 'Google_Service_DLP_GooglePrivacyDlpV2DlpJob');
