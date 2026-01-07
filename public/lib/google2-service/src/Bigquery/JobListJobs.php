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

class JobListJobs extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "principalSubject" => "principal_subject",
        "userEmail" => "user_email",
  ];
  protected $configurationType = JobConfiguration::class;
  protected $configurationDataType = '';
  protected $errorResultType = ErrorProto::class;
  protected $errorResultDataType = '';
  /**
   * Unique opaque ID of the job.
   *
   * @var string
   */
  public $id;
  protected $jobReferenceType = JobReference::class;
  protected $jobReferenceDataType = '';
  /**
   * The resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * [Full-projection-only] String representation of identity of requesting
   * party. Populated for both first- and third-party identities. Only present
   * for APIs that support third-party identities.
   *
   * @var string
   */
  public $principalSubject;
  /**
   * Running state of the job. When the state is DONE, errorResult can be
   * checked to determine whether the job succeeded or failed.
   *
   * @var string
   */
  public $state;
  protected $statisticsType = JobStatistics::class;
  protected $statisticsDataType = '';
  protected $statusType = JobStatus::class;
  protected $statusDataType = '';
  /**
   * [Full-projection-only] Email address of the user who ran the job.
   *
   * @var string
   */
  public $userEmail;

  /**
   * Required. Describes the job configuration.
   *
   * @param JobConfiguration $configuration
   */
  public function setConfiguration(JobConfiguration $configuration)
  {
    $this->configuration = $configuration;
  }
  /**
   * @return JobConfiguration
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
  /**
   * A result object that will be present only if the job has failed.
   *
   * @param ErrorProto $errorResult
   */
  public function setErrorResult(ErrorProto $errorResult)
  {
    $this->errorResult = $errorResult;
  }
  /**
   * @return ErrorProto
   */
  public function getErrorResult()
  {
    return $this->errorResult;
  }
  /**
   * Unique opaque ID of the job.
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
   * Unique opaque ID of the job.
   *
   * @param JobReference $jobReference
   */
  public function setJobReference(JobReference $jobReference)
  {
    $this->jobReference = $jobReference;
  }
  /**
   * @return JobReference
   */
  public function getJobReference()
  {
    return $this->jobReference;
  }
  /**
   * The resource type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * [Full-projection-only] String representation of identity of requesting
   * party. Populated for both first- and third-party identities. Only present
   * for APIs that support third-party identities.
   *
   * @param string $principalSubject
   */
  public function setPrincipalSubject($principalSubject)
  {
    $this->principalSubject = $principalSubject;
  }
  /**
   * @return string
   */
  public function getPrincipalSubject()
  {
    return $this->principalSubject;
  }
  /**
   * Running state of the job. When the state is DONE, errorResult can be
   * checked to determine whether the job succeeded or failed.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Information about the job, including starting time and ending
   * time of the job.
   *
   * @param JobStatistics $statistics
   */
  public function setStatistics(JobStatistics $statistics)
  {
    $this->statistics = $statistics;
  }
  /**
   * @return JobStatistics
   */
  public function getStatistics()
  {
    return $this->statistics;
  }
  /**
   * [Full-projection-only] Describes the status of this job.
   *
   * @param JobStatus $status
   */
  public function setStatus(JobStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return JobStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * [Full-projection-only] Email address of the user who ran the job.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobListJobs::class, 'Google_Service_Bigquery_JobListJobs');
