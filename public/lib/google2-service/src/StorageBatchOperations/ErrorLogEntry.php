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

namespace Google\Service\StorageBatchOperations;

class ErrorLogEntry extends \Google\Collection
{
  protected $collection_key = 'errorDetails';
  /**
   * Optional. Output only. At most 5 error log entries are recorded for a given
   * error code for a job.
   *
   * @var string[]
   */
  public $errorDetails;
  /**
   * Required. Output only. Object URL. e.g. gs://my_bucket/object.txt
   *
   * @var string
   */
  public $objectUri;

  /**
   * Optional. Output only. At most 5 error log entries are recorded for a given
   * error code for a job.
   *
   * @param string[] $errorDetails
   */
  public function setErrorDetails($errorDetails)
  {
    $this->errorDetails = $errorDetails;
  }
  /**
   * @return string[]
   */
  public function getErrorDetails()
  {
    return $this->errorDetails;
  }
  /**
   * Required. Output only. Object URL. e.g. gs://my_bucket/object.txt
   *
   * @param string $objectUri
   */
  public function setObjectUri($objectUri)
  {
    $this->objectUri = $objectUri;
  }
  /**
   * @return string
   */
  public function getObjectUri()
  {
    return $this->objectUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorLogEntry::class, 'Google_Service_StorageBatchOperations_ErrorLogEntry');
