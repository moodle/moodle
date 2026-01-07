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

class ChildRolloutJobs extends \Google\Collection
{
  protected $collection_key = 'createRolloutJobs';
  protected $advanceRolloutJobsType = Job::class;
  protected $advanceRolloutJobsDataType = 'array';
  protected $createRolloutJobsType = Job::class;
  protected $createRolloutJobsDataType = 'array';

  /**
   * Output only. List of AdvanceChildRolloutJobs
   *
   * @param Job[] $advanceRolloutJobs
   */
  public function setAdvanceRolloutJobs($advanceRolloutJobs)
  {
    $this->advanceRolloutJobs = $advanceRolloutJobs;
  }
  /**
   * @return Job[]
   */
  public function getAdvanceRolloutJobs()
  {
    return $this->advanceRolloutJobs;
  }
  /**
   * Output only. List of CreateChildRolloutJobs
   *
   * @param Job[] $createRolloutJobs
   */
  public function setCreateRolloutJobs($createRolloutJobs)
  {
    $this->createRolloutJobs = $createRolloutJobs;
  }
  /**
   * @return Job[]
   */
  public function getCreateRolloutJobs()
  {
    return $this->createRolloutJobs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChildRolloutJobs::class, 'Google_Service_CloudDeploy_ChildRolloutJobs');
