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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2GetDefaultBranchResponse extends \Google\Model
{
  /**
   * Full resource name of the branch id currently set as default branch.
   *
   * @var string
   */
  public $branch;
  /**
   * This corresponds to SetDefaultBranchRequest.note field, when this branch
   * was set as default.
   *
   * @var string
   */
  public $note;
  /**
   * The time when this branch is set to default.
   *
   * @var string
   */
  public $setTime;

  /**
   * Full resource name of the branch id currently set as default branch.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * This corresponds to SetDefaultBranchRequest.note field, when this branch
   * was set as default.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * The time when this branch is set to default.
   *
   * @param string $setTime
   */
  public function setSetTime($setTime)
  {
    $this->setTime = $setTime;
  }
  /**
   * @return string
   */
  public function getSetTime()
  {
    return $this->setTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2GetDefaultBranchResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2GetDefaultBranchResponse');
