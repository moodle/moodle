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

class TaskSpec extends \Google\Collection
{
  protected $collection_key = 'volumes';
  protected $computeResourceType = ComputeResource::class;
  protected $computeResourceDataType = '';
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  /**
   * Deprecated: please use environment(non-plural) instead.
   *
   * @deprecated
   * @var string[]
   */
  public $environments;
  protected $lifecyclePoliciesType = LifecyclePolicy::class;
  protected $lifecyclePoliciesDataType = 'array';
  /**
   * Maximum number of retries on failures. The default, 0, which means never
   * retry. The valid value range is [0, 10].
   *
   * @var int
   */
  public $maxRetryCount;
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
  protected $runnablesType = Runnable::class;
  protected $runnablesDataType = 'array';
  protected $volumesType = Volume::class;
  protected $volumesDataType = 'array';

  /**
   * ComputeResource requirements.
   *
   * @param ComputeResource $computeResource
   */
  public function setComputeResource(ComputeResource $computeResource)
  {
    $this->computeResource = $computeResource;
  }
  /**
   * @return ComputeResource
   */
  public function getComputeResource()
  {
    return $this->computeResource;
  }
  /**
   * Environment variables to set before running the Task.
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Deprecated: please use environment(non-plural) instead.
   *
   * @deprecated
   * @param string[] $environments
   */
  public function setEnvironments($environments)
  {
    $this->environments = $environments;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getEnvironments()
  {
    return $this->environments;
  }
  /**
   * Lifecycle management schema when any task in a task group is failed.
   * Currently we only support one lifecycle policy. When the lifecycle policy
   * condition is met, the action in the policy will execute. If task execution
   * result does not meet with the defined lifecycle policy, we consider it as
   * the default policy. Default policy means if the exit code is 0, exit task.
   * If task ends with non-zero exit code, retry the task with max_retry_count.
   *
   * @param LifecyclePolicy[] $lifecyclePolicies
   */
  public function setLifecyclePolicies($lifecyclePolicies)
  {
    $this->lifecyclePolicies = $lifecyclePolicies;
  }
  /**
   * @return LifecyclePolicy[]
   */
  public function getLifecyclePolicies()
  {
    return $this->lifecyclePolicies;
  }
  /**
   * Maximum number of retries on failures. The default, 0, which means never
   * retry. The valid value range is [0, 10].
   *
   * @param int $maxRetryCount
   */
  public function setMaxRetryCount($maxRetryCount)
  {
    $this->maxRetryCount = $maxRetryCount;
  }
  /**
   * @return int
   */
  public function getMaxRetryCount()
  {
    return $this->maxRetryCount;
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
   * Required. The sequence of one or more runnables (executable scripts,
   * executable containers, and/or barriers) for each task in this task group to
   * run. Each task runs this list of runnables in order. For a task to succeed,
   * all of its script and container runnables each must meet at least one of
   * the following conditions: + The runnable exited with a zero status. + The
   * runnable didn't finish, but you enabled its `background` subfield. + The
   * runnable exited with a non-zero status, but you enabled its
   * `ignore_exit_status` subfield.
   *
   * @param Runnable[] $runnables
   */
  public function setRunnables($runnables)
  {
    $this->runnables = $runnables;
  }
  /**
   * @return Runnable[]
   */
  public function getRunnables()
  {
    return $this->runnables;
  }
  /**
   * Volumes to mount before running Tasks using this TaskSpec.
   *
   * @param Volume[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return Volume[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskSpec::class, 'Google_Service_Batch_TaskSpec');
