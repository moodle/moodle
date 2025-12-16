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

class CommitLogEntry extends \Google\Model
{
  protected $authorType = CommitAuthor::class;
  protected $authorDataType = '';
  /**
   * The commit message for this commit log entry.
   *
   * @var string
   */
  public $commitMessage;
  /**
   * The commit SHA for this commit log entry.
   *
   * @var string
   */
  public $commitSha;
  /**
   * Commit timestamp.
   *
   * @var string
   */
  public $commitTime;

  /**
   * The commit author for this commit log entry.
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
   * The commit message for this commit log entry.
   *
   * @param string $commitMessage
   */
  public function setCommitMessage($commitMessage)
  {
    $this->commitMessage = $commitMessage;
  }
  /**
   * @return string
   */
  public function getCommitMessage()
  {
    return $this->commitMessage;
  }
  /**
   * The commit SHA for this commit log entry.
   *
   * @param string $commitSha
   */
  public function setCommitSha($commitSha)
  {
    $this->commitSha = $commitSha;
  }
  /**
   * @return string
   */
  public function getCommitSha()
  {
    return $this->commitSha;
  }
  /**
   * Commit timestamp.
   *
   * @param string $commitTime
   */
  public function setCommitTime($commitTime)
  {
    $this->commitTime = $commitTime;
  }
  /**
   * @return string
   */
  public function getCommitTime()
  {
    return $this->commitTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitLogEntry::class, 'Google_Service_Dataform_CommitLogEntry');
