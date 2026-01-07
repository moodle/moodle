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

class JobCreationReason extends \Google\Model
{
  /**
   * Reason is not specified.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Job creation was requested.
   */
  public const CODE_REQUESTED = 'REQUESTED';
  /**
   * The query request ran beyond a system defined timeout specified by the
   * [timeoutMs field in the QueryRequest](https://cloud.google.com/bigquery/doc
   * s/reference/rest/v2/jobs/query#queryrequest). As a result it was considered
   * a long running operation for which a job was created.
   */
  public const CODE_LONG_RUNNING = 'LONG_RUNNING';
  /**
   * The results from the query cannot fit in the response.
   */
  public const CODE_LARGE_RESULTS = 'LARGE_RESULTS';
  /**
   * BigQuery has determined that the query needs to be executed as a Job.
   */
  public const CODE_OTHER = 'OTHER';
  /**
   * Output only. Specifies the high level reason why a Job was created.
   *
   * @var string
   */
  public $code;

  /**
   * Output only. Specifies the high level reason why a Job was created.
   *
   * Accepted values: CODE_UNSPECIFIED, REQUESTED, LONG_RUNNING, LARGE_RESULTS,
   * OTHER
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobCreationReason::class, 'Google_Service_Bigquery_JobCreationReason');
