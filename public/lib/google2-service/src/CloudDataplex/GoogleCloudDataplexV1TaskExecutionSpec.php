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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TaskExecutionSpec extends \Google\Model
{
  /**
   * Optional. The arguments to pass to the task. The args can use placeholders
   * of the format ${placeholder} as part of key/value string. These will be
   * interpolated before passing the args to the driver. Currently supported
   * placeholders: - ${task_id} - ${job_time} To pass positional args, set the
   * key as TASK_ARGS. The value should be a comma-separated string of all the
   * positional arguments. To use a delimiter other than comma, refer to
   * https://cloud.google.com/sdk/gcloud/reference/topic/escaping. In case of
   * other keys being present in the args, then TASK_ARGS will be passed as the
   * last argument.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. The Cloud KMS key to use for encryption, of the form:
   * projects/{project_number}/locations/{location_id}/keyRings/{key-ring-
   * name}/cryptoKeys/{key-name}.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. The maximum duration after which the job execution is expired.
   *
   * @var string
   */
  public $maxJobExecutionLifetime;
  /**
   * Optional. The project in which jobs are run. By default, the project
   * containing the Lake is used. If a project is provided, the
   * ExecutionSpec.service_account must belong to this project.
   *
   * @var string
   */
  public $project;
  /**
   * Required. Service account to use to execute a task. If not provided, the
   * default Compute service account for the project is used.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. The arguments to pass to the task. The args can use placeholders
   * of the format ${placeholder} as part of key/value string. These will be
   * interpolated before passing the args to the driver. Currently supported
   * placeholders: - ${task_id} - ${job_time} To pass positional args, set the
   * key as TASK_ARGS. The value should be a comma-separated string of all the
   * positional arguments. To use a delimiter other than comma, refer to
   * https://cloud.google.com/sdk/gcloud/reference/topic/escaping. In case of
   * other keys being present in the args, then TASK_ARGS will be passed as the
   * last argument.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. The Cloud KMS key to use for encryption, of the form:
   * projects/{project_number}/locations/{location_id}/keyRings/{key-ring-
   * name}/cryptoKeys/{key-name}.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. The maximum duration after which the job execution is expired.
   *
   * @param string $maxJobExecutionLifetime
   */
  public function setMaxJobExecutionLifetime($maxJobExecutionLifetime)
  {
    $this->maxJobExecutionLifetime = $maxJobExecutionLifetime;
  }
  /**
   * @return string
   */
  public function getMaxJobExecutionLifetime()
  {
    return $this->maxJobExecutionLifetime;
  }
  /**
   * Optional. The project in which jobs are run. By default, the project
   * containing the Lake is used. If a project is provided, the
   * ExecutionSpec.service_account must belong to this project.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Required. Service account to use to execute a task. If not provided, the
   * default Compute service account for the project is used.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskExecutionSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskExecutionSpec');
