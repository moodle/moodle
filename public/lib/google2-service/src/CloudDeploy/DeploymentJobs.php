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

class DeploymentJobs extends \Google\Model
{
  protected $deployJobType = Job::class;
  protected $deployJobDataType = '';
  protected $postdeployJobType = Job::class;
  protected $postdeployJobDataType = '';
  protected $predeployJobType = Job::class;
  protected $predeployJobDataType = '';
  protected $verifyJobType = Job::class;
  protected $verifyJobDataType = '';

  /**
   * Output only. The deploy Job. This is the deploy job in the phase.
   *
   * @param Job $deployJob
   */
  public function setDeployJob(Job $deployJob)
  {
    $this->deployJob = $deployJob;
  }
  /**
   * @return Job
   */
  public function getDeployJob()
  {
    return $this->deployJob;
  }
  /**
   * Output only. The postdeploy Job, which is the last job on the phase.
   *
   * @param Job $postdeployJob
   */
  public function setPostdeployJob(Job $postdeployJob)
  {
    $this->postdeployJob = $postdeployJob;
  }
  /**
   * @return Job
   */
  public function getPostdeployJob()
  {
    return $this->postdeployJob;
  }
  /**
   * Output only. The predeploy Job, which is the first job on the phase.
   *
   * @param Job $predeployJob
   */
  public function setPredeployJob(Job $predeployJob)
  {
    $this->predeployJob = $predeployJob;
  }
  /**
   * @return Job
   */
  public function getPredeployJob()
  {
    return $this->predeployJob;
  }
  /**
   * Output only. The verify Job. Runs after a deploy if the deploy succeeds.
   *
   * @param Job $verifyJob
   */
  public function setVerifyJob(Job $verifyJob)
  {
    $this->verifyJob = $verifyJob;
  }
  /**
   * @return Job
   */
  public function getVerifyJob()
  {
    return $this->verifyJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentJobs::class, 'Google_Service_CloudDeploy_DeploymentJobs');
