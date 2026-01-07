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

class PullGitCommitsRequest extends \Google\Model
{
  protected $authorType = CommitAuthor::class;
  protected $authorDataType = '';
  /**
   * Optional. The name of the branch in the Git remote from which to pull
   * commits. If left unset, the repository's default branch name will be used.
   *
   * @var string
   */
  public $remoteBranch;

  /**
   * Required. The author of any merge commit which may be created as a result
   * of merging fetched Git commits into this workspace.
   *
   * @param CommitAuthor $author
   */
  public function setAuthor(CommitAuthor $author)
  {
    $this->author = $author;
  }
  /**
   * @return CommitAuthor
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Optional. The name of the branch in the Git remote from which to pull
   * commits. If left unset, the repository's default branch name will be used.
   *
   * @param string $remoteBranch
   */
  public function setRemoteBranch($remoteBranch)
  {
    $this->remoteBranch = $remoteBranch;
  }
  /**
   * @return string
   */
  public function getRemoteBranch()
  {
    return $this->remoteBranch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PullGitCommitsRequest::class, 'Google_Service_Dataform_PullGitCommitsRequest');
