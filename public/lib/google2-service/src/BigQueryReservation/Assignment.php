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

namespace Google\Service\BigQueryReservation;

class Assignment extends \Google\Model
{
  /**
   * Invalid type. Requests with this value will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const JOB_TYPE_JOB_TYPE_UNSPECIFIED = 'JOB_TYPE_UNSPECIFIED';
  /**
   * Pipeline (load/export) jobs from the project will use the reservation.
   */
  public const JOB_TYPE_PIPELINE = 'PIPELINE';
  /**
   * Query jobs from the project will use the reservation.
   */
  public const JOB_TYPE_QUERY = 'QUERY';
  /**
   * BigQuery ML jobs that use services external to BigQuery for model training.
   * These jobs will not utilize idle slots from other reservations.
   */
  public const JOB_TYPE_ML_EXTERNAL = 'ML_EXTERNAL';
  /**
   * Background jobs that BigQuery runs for the customers in the background.
   */
  public const JOB_TYPE_BACKGROUND = 'BACKGROUND';
  /**
   * Continuous SQL jobs will use this reservation. Reservations with continuous
   * assignments cannot be mixed with non-continuous assignments.
   */
  public const JOB_TYPE_CONTINUOUS = 'CONTINUOUS';
  /**
   * Finer granularity background jobs for capturing changes in a source
   * database and streaming them into BigQuery. Reservations with this job type
   * take priority over a default BACKGROUND reservation assignment (if it
   * exists).
   */
  public const JOB_TYPE_BACKGROUND_CHANGE_DATA_CAPTURE = 'BACKGROUND_CHANGE_DATA_CAPTURE';
  /**
   * Finer granularity background jobs for refreshing cached metadata for
   * BigQuery tables. Reservations with this job type take priority over a
   * default BACKGROUND reservation assignment (if it exists).
   */
  public const JOB_TYPE_BACKGROUND_COLUMN_METADATA_INDEX = 'BACKGROUND_COLUMN_METADATA_INDEX';
  /**
   * Finer granularity background jobs for refreshing search indexes upon
   * BigQuery table columns. Reservations with this job type take priority over
   * a default BACKGROUND reservation assignment (if it exists).
   */
  public const JOB_TYPE_BACKGROUND_SEARCH_INDEX_REFRESH = 'BACKGROUND_SEARCH_INDEX_REFRESH';
  /**
   * Invalid state value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Queries from assignee will be executed as on-demand, if related assignment
   * is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Assignment is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Optional. The resource which will use the reservation. E.g.
   * `projects/myproject`, `folders/123`, or `organizations/456`.
   *
   * @var string
   */
  public $assignee;
  /**
   * Optional. This field controls if "Gemini in BigQuery"
   * (https://cloud.google.com/gemini/docs/bigquery/overview) features should be
   * enabled for this reservation assignment, which is not on by default.
   * "Gemini in BigQuery" has a distinct compliance posture from BigQuery. If
   * this field is set to true, the assignment job type is QUERY, and the parent
   * reservation edition is ENTERPRISE_PLUS, then the assignment will give the
   * grantee project/organization access to "Gemini in BigQuery" features.
   *
   * @var bool
   */
  public $enableGeminiInBigquery;
  /**
   * Optional. Which type of jobs will use the reservation.
   *
   * @var string
   */
  public $jobType;
  /**
   * Output only. Name of the resource. E.g.:
   * `projects/myproject/locations/US/reservations/team1-prod/assignments/123`.
   * The assignment_id must only contain lower case alphanumeric characters or
   * dashes and the max length is 64 characters.
   *
   * @var string
   */
  public $name;
  protected $schedulingPolicyType = SchedulingPolicy::class;
  protected $schedulingPolicyDataType = '';
  /**
   * Output only. State of the assignment.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. The resource which will use the reservation. E.g.
   * `projects/myproject`, `folders/123`, or `organizations/456`.
   *
   * @param string $assignee
   */
  public function setAssignee($assignee)
  {
    $this->assignee = $assignee;
  }
  /**
   * @return string
   */
  public function getAssignee()
  {
    return $this->assignee;
  }
  /**
   * Optional. This field controls if "Gemini in BigQuery"
   * (https://cloud.google.com/gemini/docs/bigquery/overview) features should be
   * enabled for this reservation assignment, which is not on by default.
   * "Gemini in BigQuery" has a distinct compliance posture from BigQuery. If
   * this field is set to true, the assignment job type is QUERY, and the parent
   * reservation edition is ENTERPRISE_PLUS, then the assignment will give the
   * grantee project/organization access to "Gemini in BigQuery" features.
   *
   * @param bool $enableGeminiInBigquery
   */
  public function setEnableGeminiInBigquery($enableGeminiInBigquery)
  {
    $this->enableGeminiInBigquery = $enableGeminiInBigquery;
  }
  /**
   * @return bool
   */
  public function getEnableGeminiInBigquery()
  {
    return $this->enableGeminiInBigquery;
  }
  /**
   * Optional. Which type of jobs will use the reservation.
   *
   * Accepted values: JOB_TYPE_UNSPECIFIED, PIPELINE, QUERY, ML_EXTERNAL,
   * BACKGROUND, CONTINUOUS, BACKGROUND_CHANGE_DATA_CAPTURE,
   * BACKGROUND_COLUMN_METADATA_INDEX, BACKGROUND_SEARCH_INDEX_REFRESH
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
   * Output only. Name of the resource. E.g.:
   * `projects/myproject/locations/US/reservations/team1-prod/assignments/123`.
   * The assignment_id must only contain lower case alphanumeric characters or
   * dashes and the max length is 64 characters.
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
   * Optional. The scheduling policy to use for jobs and queries of this
   * assignee when running under the associated reservation. The scheduling
   * policy controls how the reservation's resources are distributed. This
   * overrides the default scheduling policy specified on the reservation. This
   * feature is not yet generally available.
   *
   * @param SchedulingPolicy $schedulingPolicy
   */
  public function setSchedulingPolicy(SchedulingPolicy $schedulingPolicy)
  {
    $this->schedulingPolicy = $schedulingPolicy;
  }
  /**
   * @return SchedulingPolicy
   */
  public function getSchedulingPolicy()
  {
    return $this->schedulingPolicy;
  }
  /**
   * Output only. State of the assignment.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ACTIVE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Assignment::class, 'Google_Service_BigQueryReservation_Assignment');
