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

namespace Google\Service\Dataflow;

class WorkerMessageCode extends \Google\Model
{
  /**
   * The code is a string intended for consumption by a machine that identifies
   * the type of message being sent. Examples: 1. "HARNESS_STARTED" might be
   * used to indicate the worker harness has started. 2. "GCS_DOWNLOAD_ERROR"
   * might be used to indicate an error downloading a Cloud Storage file as part
   * of the boot process of one of the worker containers. This is a string and
   * not an enum to make it easy to add new codes without waiting for an API
   * change.
   *
   * @var string
   */
  public $code;
  /**
   * Parameters contains specific information about the code. This is a struct
   * to allow parameters of different types. Examples: 1. For a
   * "HARNESS_STARTED" message parameters might provide the name of the worker
   * and additional data like timing information. 2. For a "GCS_DOWNLOAD_ERROR"
   * parameters might contain fields listing the Cloud Storage objects being
   * downloaded and fields containing errors. In general complex data structures
   * should be avoided. If a worker needs to send a specific and complicated
   * data structure then please consider defining a new proto and adding it to
   * the data oneof in WorkerMessageResponse. Conventions: Parameters should
   * only be used for information that isn't typically passed as a label.
   * hostname and other worker identifiers should almost always be passed as
   * labels since they will be included on most messages.
   *
   * @var array[]
   */
  public $parameters;

  /**
   * The code is a string intended for consumption by a machine that identifies
   * the type of message being sent. Examples: 1. "HARNESS_STARTED" might be
   * used to indicate the worker harness has started. 2. "GCS_DOWNLOAD_ERROR"
   * might be used to indicate an error downloading a Cloud Storage file as part
   * of the boot process of one of the worker containers. This is a string and
   * not an enum to make it easy to add new codes without waiting for an API
   * change.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Parameters contains specific information about the code. This is a struct
   * to allow parameters of different types. Examples: 1. For a
   * "HARNESS_STARTED" message parameters might provide the name of the worker
   * and additional data like timing information. 2. For a "GCS_DOWNLOAD_ERROR"
   * parameters might contain fields listing the Cloud Storage objects being
   * downloaded and fields containing errors. In general complex data structures
   * should be avoided. If a worker needs to send a specific and complicated
   * data structure then please consider defining a new proto and adding it to
   * the data oneof in WorkerMessageResponse. Conventions: Parameters should
   * only be used for information that isn't typically passed as a label.
   * hostname and other worker identifiers should almost always be passed as
   * labels since they will be included on most messages.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerMessageCode::class, 'Google_Service_Dataflow_WorkerMessageCode');
