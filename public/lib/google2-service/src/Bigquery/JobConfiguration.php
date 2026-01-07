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

class JobConfiguration extends \Google\Model
{
  protected $copyType = JobConfigurationTableCopy::class;
  protected $copyDataType = '';
  /**
   * Optional. If set, don't actually run this job. A valid query will return a
   * mostly empty response with some processing statistics, while an invalid
   * query will return the same error it would if it wasn't a dry run. Behavior
   * of non-query jobs is undefined.
   *
   * @var bool
   */
  public $dryRun;
  protected $extractType = JobConfigurationExtract::class;
  protected $extractDataType = '';
  /**
   * Optional. Job timeout in milliseconds relative to the job creation time. If
   * this time limit is exceeded, BigQuery attempts to stop the job, but might
   * not always succeed in canceling it before the job completes. For example, a
   * job that takes more than 60 seconds to complete has a better chance of
   * being stopped than a job that takes 10 seconds to complete.
   *
   * @var string
   */
  public $jobTimeoutMs;
  /**
   * Output only. The type of the job. Can be QUERY, LOAD, EXTRACT, COPY or
   * UNKNOWN.
   *
   * @var string
   */
  public $jobType;
  /**
   * The labels associated with this job. You can use these to organize and
   * group your jobs. Label keys and values can be no longer than 63 characters,
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. Label values are optional.
   * Label keys must start with a letter and each label in the list must have a
   * different key.
   *
   * @var string[]
   */
  public $labels;
  protected $loadType = JobConfigurationLoad::class;
  protected $loadDataType = '';
  /**
   * Optional. A target limit on the rate of slot consumption by this job. If
   * set to a value > 0, BigQuery will attempt to limit the rate of slot
   * consumption by this job to keep it below the configured limit, even if the
   * job is eligible for more slots based on fair scheduling. The unused slots
   * will be available for other jobs and queries to use. Note: This feature is
   * not yet generally available.
   *
   * @var int
   */
  public $maxSlots;
  protected $queryType = JobConfigurationQuery::class;
  protected $queryDataType = '';
  /**
   * Optional. The reservation that job would use. User can specify a
   * reservation to execute the job. If reservation is not set, reservation is
   * determined based on the rules defined by the reservation assignments. The
   * expected format is
   * `projects/{project}/locations/{location}/reservations/{reservation}`.
   *
   * @var string
   */
  public $reservation;

  /**
   * [Pick one] Copies a table.
   *
   * @param JobConfigurationTableCopy $copy
   */
  public function setCopy(JobConfigurationTableCopy $copy)
  {
    $this->copy = $copy;
  }
  /**
   * @return JobConfigurationTableCopy
   */
  public function getCopy()
  {
    return $this->copy;
  }
  /**
   * Optional. If set, don't actually run this job. A valid query will return a
   * mostly empty response with some processing statistics, while an invalid
   * query will return the same error it would if it wasn't a dry run. Behavior
   * of non-query jobs is undefined.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * [Pick one] Configures an extract job.
   *
   * @param JobConfigurationExtract $extract
   */
  public function setExtract(JobConfigurationExtract $extract)
  {
    $this->extract = $extract;
  }
  /**
   * @return JobConfigurationExtract
   */
  public function getExtract()
  {
    return $this->extract;
  }
  /**
   * Optional. Job timeout in milliseconds relative to the job creation time. If
   * this time limit is exceeded, BigQuery attempts to stop the job, but might
   * not always succeed in canceling it before the job completes. For example, a
   * job that takes more than 60 seconds to complete has a better chance of
   * being stopped than a job that takes 10 seconds to complete.
   *
   * @param string $jobTimeoutMs
   */
  public function setJobTimeoutMs($jobTimeoutMs)
  {
    $this->jobTimeoutMs = $jobTimeoutMs;
  }
  /**
   * @return string
   */
  public function getJobTimeoutMs()
  {
    return $this->jobTimeoutMs;
  }
  /**
   * Output only. The type of the job. Can be QUERY, LOAD, EXTRACT, COPY or
   * UNKNOWN.
   *
   * @param string $jobType
   */
  public function setJobType($jobType)
  {
    $this->jobType = $jobType;
  }
  /**
   * @return string
   */
  public function getJobType()
  {
    return $this->jobType;
  }
  /**
   * The labels associated with this job. You can use these to organize and
   * group your jobs. Label keys and values can be no longer than 63 characters,
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. Label values are optional.
   * Label keys must start with a letter and each label in the list must have a
   * different key.
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
   * [Pick one] Configures a load job.
   *
   * @param JobConfigurationLoad $load
   */
  public function setLoad(JobConfigurationLoad $load)
  {
    $this->load = $load;
  }
  /**
   * @return JobConfigurationLoad
   */
  public function getLoad()
  {
    return $this->load;
  }
  /**
   * Optional. A target limit on the rate of slot consumption by this job. If
   * set to a value > 0, BigQuery will attempt to limit the rate of slot
   * consumption by this job to keep it below the configured limit, even if the
   * job is eligible for more slots based on fair scheduling. The unused slots
   * will be available for other jobs and queries to use. Note: This feature is
   * not yet generally available.
   *
   * @param int $maxSlots
   */
  public function setMaxSlots($maxSlots)
  {
    $this->maxSlots = $maxSlots;
  }
  /**
   * @return int
   */
  public function getMaxSlots()
  {
    return $this->maxSlots;
  }
  /**
   * [Pick one] Configures a query job.
   *
   * @param JobConfigurationQuery $query
   */
  public function setQuery(JobConfigurationQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return JobConfigurationQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Optional. The reservation that job would use. User can specify a
   * reservation to execute the job. If reservation is not set, reservation is
   * determined based on the rules defined by the reservation assignments. The
   * expected format is
   * `projects/{project}/locations/{location}/reservations/{reservation}`.
   *
   * @param string $reservation
   */
  public function setReservation($reservation)
  {
    $this->reservation = $reservation;
  }
  /**
   * @return string
   */
  public function getReservation()
  {
    return $this->reservation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfiguration::class, 'Google_Service_Bigquery_JobConfiguration');
