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

namespace Google\Service\CloudDeploy;

class Job extends \Google\Model
{
  /**
   * The Job has an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Job is waiting for an earlier Phase(s) or Job(s) to complete.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The Job is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The Job is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The Job succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Job failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The Job was aborted.
   */
  public const STATE_ABORTED = 'ABORTED';
  /**
   * The Job was skipped.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * The Job was ignored.
   */
  public const STATE_IGNORED = 'IGNORED';
  protected $advanceChildRolloutJobType = AdvanceChildRolloutJob::class;
  protected $advanceChildRolloutJobDataType = '';
  protected $createChildRolloutJobType = CreateChildRolloutJob::class;
  protected $createChildRolloutJobDataType = '';
  protected $deployJobType = DeployJob::class;
  protected $deployJobDataType = '';
  /**
   * Output only. The ID of the Job.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The name of the `JobRun` responsible for the most recent
   * invocation of this Job.
   *
   * @var string
   */
  public $jobRun;
  protected $postdeployJobType = PostdeployJob::class;
  protected $postdeployJobDataType = '';
  protected $predeployJobType = PredeployJob::class;
  protected $predeployJobDataType = '';
  /**
   * Output only. Additional information on why the Job was skipped, if
   * available.
   *
   * @var string
   */
  public $skipMessage;
  /**
   * Output only. The current state of the Job.
   *
   * @var string
   */
  public $state;
  protected $verifyJobType = VerifyJob::class;
  protected $verifyJobDataType = '';

  /**
   * Output only. An advanceChildRollout Job.
   *
   * @param AdvanceChildRolloutJob $advanceChildRolloutJob
   */
  public function setAdvanceChildRolloutJob(AdvanceChildRolloutJob $advanceChildRolloutJob)
  {
    $this->advanceChildRolloutJob = $advanceChildRolloutJob;
  }
  /**
   * @return AdvanceChildRolloutJob
   */
  public function getAdvanceChildRolloutJob()
  {
    return $this->advanceChildRolloutJob;
  }
  /**
   * Output only. A createChildRollout Job.
   *
   * @param CreateChildRolloutJob $createChildRolloutJob
   */
  public function setCreateChildRolloutJob(CreateChildRolloutJob $createChildRolloutJob)
  {
    $this->createChildRolloutJob = $createChildRolloutJob;
  }
  /**
   * @return CreateChildRolloutJob
   */
  public function getCreateChildRolloutJob()
  {
    return $this->createChildRolloutJob;
  }
  /**
   * Output only. A deploy Job.
   *
   * @param DeployJob $deployJob
   */
  public function setDeployJob(DeployJob $deployJob)
  {
    $this->deployJob = $deployJob;
  }
  /**
   * @return DeployJob
   */
  public function getDeployJob()
  {
    return $this->deployJob;
  }
  /**
   * Output only. The ID of the Job.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The name of the `JobRun` responsible for the most recent
   * invocation of this Job.
   *
   * @param string $jobRun
   */
  public function setJobRun($jobRun)
  {
    $this->jobRun = $jobRun;
  }
  /**
   * @return string
   */
  public function getJobRun()
  {
    return $this->jobRun;
  }
  /**
   * Output only. A postdeploy Job.
   *
   * @param PostdeployJob $postdeployJob
   */
  public function setPostdeployJob(PostdeployJob $postdeployJob)
  {
    $this->postdeployJob = $postdeployJob;
  }
  /**
   * @return PostdeployJob
   */
  public function getPostdeployJob()
  {
    return $this->postdeployJob;
  }
  /**
   * Output only. A predeploy Job.
   *
   * @param PredeployJob $predeployJob
   */
  public function setPredeployJob(PredeployJob $predeployJob)
  {
    $this->predeployJob = $predeployJob;
  }
  /**
   * @return PredeployJob
   */
  public function getPredeployJob()
  {
    return $this->predeployJob;
  }
  /**
   * Output only. Additional information on why the Job was skipped, if
   * available.
   *
   * @param string $skipMessage
   */
  public function setSkipMessage($skipMessage)
  {
    $this->skipMessage = $skipMessage;
  }
  /**
   * @return string
   */
  public function getSkipMessage()
  {
    return $this->skipMessage;
  }
  /**
   * Output only. The current state of the Job.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, DISABLED, IN_PROGRESS,
   * SUCCEEDED, FAILED, ABORTED, SKIPPED, IGNORED
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
  /**
   * Output only. A verify Job.
   *
   * @param VerifyJob $verifyJob
   */
  public function setVerifyJob(VerifyJob $verifyJob)
  {
    $this->verifyJob = $verifyJob;
  }
  /**
   * @return VerifyJob
   */
  public function getVerifyJob()
  {
    return $this->verifyJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_CloudDeploy_Job');
