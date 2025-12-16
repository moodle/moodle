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

namespace Google\Service\Dataform;

class BigQueryAction extends \Google\Model
{
  /**
   * Output only. The ID of the BigQuery job that executed the SQL in
   * sql_script. Only set once the job has started to run.
   *
   * @var string
   */
  public $jobId;
  /**
   * Output only. The generated BigQuery SQL script that will be executed.
   *
   * @var string
   */
  public $sqlScript;

  /**
   * Output only. The ID of the BigQuery job that executed the SQL in
   * sql_script. Only set once the job has started to run.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * Output only. The generated BigQuery SQL script that will be executed.
   *
   * @param string $sqlScript
   */
  public function setSqlScript($sqlScript)
  {
    $this->sqlScript = $sqlScript;
  }
  /**
   * @return string
   */
  public function getSqlScript()
  {
    return $this->sqlScript;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryAction::class, 'Google_Service_Dataform_BigQueryAction');
