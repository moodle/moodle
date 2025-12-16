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

namespace Google\Service\Dataform;

class FetchGitAheadBehindResponse extends \Google\Model
{
  /**
   * The number of commits in the remote branch that are not in the workspace.
   *
   * @var int
   */
  public $commitsAhead;
  /**
   * The number of commits in the workspace that are not in the remote branch.
   *
   * @var int
   */
  public $commitsBehind;

  /**
   * The number of commits in the remote branch that are not in the workspace.
   *
   * @param int $commitsAhead
   */
  public function setCommitsAhead($commitsAhead)
  {
    $this->commitsAhead = $commitsAhead;
  }
  /**
   * @return int
   */
  public function getCommitsAhead()
  {
    return $this->commitsAhead;
  }
  /**
   * The number of commits in the workspace that are not in the remote branch.
   *
   * @param int $commitsBehind
   */
  public function setCommitsBehind($commitsBehind)
  {
    $this->commitsBehind = $commitsBehind;
  }
  /**
   * @return int
   */
  public function getCommitsBehind()
  {
    return $this->commitsBehind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchGitAheadBehindResponse::class, 'Google_Service_Dataform_FetchGitAheadBehindResponse');
