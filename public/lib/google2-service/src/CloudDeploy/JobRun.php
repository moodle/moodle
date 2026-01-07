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

class JobRun extends \Google\Model
{
  /**
   * The `JobRun` has an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `JobRun` is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The `JobRun` has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The `JobRun` has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The `JobRun` is terminating.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * The `JobRun` was terminated.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  protected $advanceChildRolloutJobRunType = AdvanceChildRolloutJobRun::class;
  protected $advanceChildRolloutJobRunDataType = '';
  protected $createChildRolloutJobRunType = CreateChildRolloutJobRun::class;
  protected $createChildRolloutJobRunDataType = '';
  /**
   * Output only. Time at which the `JobRun` was created.
   *
   * @var string
   */
  public $createTime;
  protected $deployJobRunType = DeployJobRun::class;
  protected $deployJobRunDataType = '';
  /**
   * Output only. Time at which the `JobRun` ended.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. ID of the `Rollout` job this `JobRun` corresponds to.
   *
   * @var string
   */
  public $jobId;
  /**
   * Output only. Name of the `JobRun`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{releases}/rollout
   * s/{rollouts}/jobRuns/{uuid}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. ID of the `Rollout` phase this `JobRun` belongs in.
   *
   * @var string
   */
  public $phaseId;
  protected $postdeployJobRunType = PostdeployJobRun::class;
  protected $postdeployJobRunDataType = '';
  protected $predeployJobRunType = PredeployJobRun::class;
  protected $predeployJobRunDataType = '';
  /**
   * Output only. Time at which the `JobRun` was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current state of the `JobRun`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Unique identifier of the `JobRun`.
   *
   * @var string
   */
  public $uid;
  protected $verifyJobRunType = VerifyJobRun::class;
  protected $verifyJobRunDataType = '';

  /**
   * Output only. Information specific to an advanceChildRollout `JobRun`
   *
   * @param AdvanceChildRolloutJobRun $advanceChildRolloutJobRun
   */
  public function setAdvanceChildRolloutJobRun(AdvanceChildRolloutJobRun $advanceChildRolloutJobRun)
  {
    $this->advanceChildRolloutJobRun = $advanceChildRolloutJobRun;
  }
  /**
   * @return AdvanceChildRolloutJobRun
   */
  public function getAdvanceChildRolloutJobRun()
  {
    return $this->advanceChildRolloutJobRun;
  }
  /**
   * Output only. Information specific to a createChildRollout `JobRun`.
   *
   * @param CreateChildRolloutJobRun $createChildRolloutJobRun
   */
  public function setCreateChildRolloutJobRun(CreateChildRolloutJobRun $createChildRolloutJobRun)
  {
    $this->createChildRolloutJobRun = $createChildRolloutJobRun;
  }
  /**
   * @return CreateChildRolloutJobRun
   */
  public function getCreateChildRolloutJobRun()
  {
    return $this->createChildRolloutJobRun;
  }
  /**
   * Output only. Time at which the `JobRun` was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Information specific to a deploy `JobRun`.
   *
   * @param DeployJobRun $deployJobRun
   */
  public function setDeployJobRun(DeployJobRun $deployJobRun)
  {
    $this->deployJobRun = $deployJobRun;
  }
  /**
   * @return DeployJobRun
   */
  public function getDeployJobRun()
  {
    return $this->deployJobRun;
  }
  /**
   * Output only. Time at which the `JobRun` ended.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. ID of the `Rollout` job this `JobRun` corresponds to.
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
   * Output only. Name of the `JobRun`. Format is `projects/{project}/locations/
   * {location}/deliveryPipelines/{deliveryPipeline}/releases/{releases}/rollout
   * s/{rollouts}/jobRuns/{uuid}`.
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
   * Output only. ID of the `Rollout` phase this `JobRun` belongs in.
   *
   * @param string $phaseId
   */
  public function setPhaseId($phaseId)
  {
    $this->phaseId = $phaseId;
  }
  /**
   * @return string
   */
  public function getPhaseId()
  {
    return $this->phaseId;
  }
  /**
   * Output only. Information specific to a postdeploy `JobRun`.
   *
   * @param PostdeployJobRun $postdeployJobRun
   */
  public function setPostdeployJobRun(PostdeployJobRun $postdeployJobRun)
  {
    $this->postdeployJobRun = $postdeployJobRun;
  }
  /**
   * @return PostdeployJobRun
   */
  public function getPostdeployJobRun()
  {
    return $this->postdeployJobRun;
  }
  /**
   * Output only. Information specific to a predeploy `JobRun`.
   *
   * @param PredeployJobRun $predeployJobRun
   */
  public function setPredeployJobRun(PredeployJobRun $predeployJobRun)
  {
    $this->predeployJobRun = $predeployJobRun;
  }
  /**
   * @return PredeployJobRun
   */
  public function getPredeployJobRun()
  {
    return $this->predeployJobRun;
  }
  /**
   * Output only. Time at which the `JobRun` was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The current state of the `JobRun`.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, SUCCEEDED, FAILED,
   * TERMINATING, TERMINATED
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
   * Output only. Unique identifier of the `JobRun`.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Information specific to a verify `JobRun`.
   *
   * @param VerifyJobRun $verifyJobRun
   */
  public function setVerifyJobRun(VerifyJobRun $verifyJobRun)
  {
    $this->verifyJobRun = $verifyJobRun;
  }
  /**
   * @return VerifyJobRun
   */
  public function getVerifyJobRun()
  {
    return $this->verifyJobRun;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobRun::class, 'Google_Service_CloudDeploy_JobRun');
