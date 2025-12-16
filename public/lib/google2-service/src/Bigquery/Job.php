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

class Job extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "principalSubject" => "principal_subject",
        "userEmail" => "user_email",
  ];
  protected $configurationType = JobConfiguration::class;
  protected $configurationDataType = '';
  /**
   * Output only. A hash of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Opaque ID field of the job.
   *
   * @var string
   */
  public $id;
  protected $jobCreationReasonType = JobCreationReason::class;
  protected $jobCreationReasonDataType = '';
  protected $jobReferenceType = JobReference::class;
  protected $jobReferenceDataType = '';
  /**
   * Output only. The type of the resource.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Full-projection-only] String representation of identity of
   * requesting party. Populated for both first- and third-party identities.
   * Only present for APIs that support third-party identities.
   *
   * @var string
   */
  public $principalSubject;
  /**
   * Output only. A URL that can be used to access the resource again.
   *
   * @var string
   */
  public $selfLink;
  protected $statisticsType = JobStatistics::class;
  protected $statisticsDataType = '';
  protected $statusType = JobStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. Email address of the user who ran the job.
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
   * Output only. A hash of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Opaque ID field of the job.
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
   * Output only. The reason why a Job was created.
   *
   * @param JobCreationReason $jobCreationReason
   */
  public function setJobCreationReason(JobCreationReason $jobCreationReason)
  {
    $this->jobCreationReason = $jobCreationReason;
  }
  /**
   * @return JobCreationReason
   */
  public function getJobCreationReason()
  {
    return $this->jobCreationReason;
  }
  /**
   * Optional. Reference describing the unique-per-user name of the job.
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
   * Output only. The type of the resource.
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
   * Output only. [Full-projection-only] String representation of identity of
   * requesting party. Populated for both first- and third-party identities.
   * Only present for APIs that support third-party identities.
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
   * Output only. A URL that can be used to access the resource again.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
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
   * Output only. The status of this job. Examine this value when polling an
   * asynchronous job to see if the job is complete.
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
   * Output only. Email address of the user who ran the job.
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
class_alias(Job::class, 'Google_Service_Bigquery_Job');
