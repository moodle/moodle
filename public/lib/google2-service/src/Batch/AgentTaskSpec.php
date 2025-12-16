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

namespace Google\Service\Batch;

class AgentTaskSpec extends \Google\Collection
{
  protected $collection_key = 'runnables';
  protected $environmentType = AgentEnvironment::class;
  protected $environmentDataType = '';
  protected $loggingOptionType = AgentTaskLoggingOption::class;
  protected $loggingOptionDataType = '';
  /**
   * Maximum duration the task should run before being automatically retried (if
   * enabled) or automatically failed. Format the value of this field as a time
   * limit in seconds followed by `s`—for example, `3600s` for 1 hour. The field
   * accepts any value between 0 and the maximum listed for the `Duration` field
   * type at https://protobuf.dev/reference/protobuf/google.protobuf/#duration;
   * however, the actual maximum run time for a job will be limited to the
   * maximum run time for a job listed at
   * https://cloud.google.com/batch/quotas#max-job-duration.
   *
   * @var string
   */
  public $maxRunDuration;
  protected $runnablesType = AgentTaskRunnable::class;
  protected $runnablesDataType = 'array';
  protected $userAccountType = AgentTaskUserAccount::class;
  protected $userAccountDataType = '';

  /**
   * Environment variables to set before running the Task.
   *
   * @param AgentEnvironment $environment
   */
  public function setEnvironment(AgentEnvironment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return AgentEnvironment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Logging option for the task.
   *
   * @param AgentTaskLoggingOption $loggingOption
   */
  public function setLoggingOption(AgentTaskLoggingOption $loggingOption)
  {
    $this->loggingOption = $loggingOption;
  }
  /**
   * @return AgentTaskLoggingOption
   */
  public function getLoggingOption()
  {
    return $this->loggingOption;
  }
  /**
   * Maximum duration the task should run before being automatically retried (if
   * enabled) or automatically failed. Format the value of this field as a time
   * limit in seconds followed by `s`—for example, `3600s` for 1 hour. The field
   * accepts any value between 0 and the maximum listed for the `Duration` field
   * type at https://protobuf.dev/reference/protobuf/google.protobuf/#duration;
   * however, the actual maximum run time for a job will be limited to the
   * maximum run time for a job listed at
   * https://cloud.google.com/batch/quotas#max-job-duration.
   *
   * @param string $maxRunDuration
   */
  public function setMaxRunDuration($maxRunDuration)
  {
    $this->maxRunDuration = $maxRunDuration;
  }
  /**
   * @return string
   */
  public function getMaxRunDuration()
  {
    return $this->maxRunDuration;
  }
  /**
   * AgentTaskRunnable is runanbles that will be executed on the agent.
   *
   * @param AgentTaskRunnable[] $runnables
   */
  public function setRunnables($runnables)
  {
    $this->runnables = $runnables;
  }
  /**
   * @return AgentTaskRunnable[]
   */
  public function getRunnables()
  {
    return $this->runnables;
  }
  /**
   * User account on the VM to run the runnables in the agentTaskSpec. If not
   * set, the runnable will be run under root user.
   *
   * @param AgentTaskUserAccount $userAccount
   */
  public function setUserAccount(AgentTaskUserAccount $userAccount)
  {
    $this->userAccount = $userAccount;
  }
  /**
   * @return AgentTaskUserAccount
   */
  public function getUserAccount()
  {
    return $this->userAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentTaskSpec::class, 'Google_Service_Batch_AgentTaskSpec');
