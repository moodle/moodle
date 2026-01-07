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

class JobStatus extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $errorResultType = ErrorProto::class;
  protected $errorResultDataType = '';
  protected $errorsType = ErrorProto::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Running state of the job. Valid states include 'PENDING',
   * 'RUNNING', and 'DONE'.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Final error result of the job. If present, indicates that the
   * job has completed and was unsuccessful.
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
   * Output only. The first errors encountered during the running of the job.
   * The final message includes the number of errors that caused the process to
   * stop. Errors here do not necessarily mean that the job has not completed or
   * was unsuccessful.
   *
   * @param ErrorProto[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ErrorProto[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Running state of the job. Valid states include 'PENDING',
   * 'RUNNING', and 'DONE'.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobStatus::class, 'Google_Service_Bigquery_JobStatus');
