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

class CommitMetadata extends \Google\Model
{
  protected $authorType = CommitAuthor::class;
  protected $authorDataType = '';
  /**
   * Optional. The commit's message.
   *
   * @var string
   */
  public $commitMessage;

  /**
   * Required. The commit's author.
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
   * Optional. The commit's message.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitMetadata::class, 'Google_Service_Dataform_CommitMetadata');
