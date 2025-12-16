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

namespace Google\Service\SecureSourceManager;

class Code extends \Google\Model
{
  /**
   * Required. The comment body.
   *
   * @var string
   */
  public $body;
  /**
   * Output only. The effective commit sha this code comment is pointing to.
   *
   * @var string
   */
  public $effectiveCommitSha;
  /**
   * Output only. The root comment of the conversation, derived from the reply
   * field.
   *
   * @var string
   */
  public $effectiveRootComment;
  protected $positionType = Position::class;
  protected $positionDataType = '';
  /**
   * Optional. Input only. The PullRequestComment resource name that this
   * comment is replying to.
   *
   * @var string
   */
  public $reply;
  /**
   * Output only. Boolean indicator if the comment is resolved.
   *
   * @var bool
   */
  public $resolved;

  /**
   * Required. The comment body.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Output only. The effective commit sha this code comment is pointing to.
   *
   * @param string $effectiveCommitSha
   */
  public function setEffectiveCommitSha($effectiveCommitSha)
  {
    $this->effectiveCommitSha = $effectiveCommitSha;
  }
  /**
   * @return string
   */
  public function getEffectiveCommitSha()
  {
    return $this->effectiveCommitSha;
  }
  /**
   * Output only. The root comment of the conversation, derived from the reply
   * field.
   *
   * @param string $effectiveRootComment
   */
  public function setEffectiveRootComment($effectiveRootComment)
  {
    $this->effectiveRootComment = $effectiveRootComment;
  }
  /**
   * @return string
   */
  public function getEffectiveRootComment()
  {
    return $this->effectiveRootComment;
  }
  /**
   * Optional. The position of the comment.
   *
   * @param Position $position
   */
  public function setPosition(Position $position)
  {
    $this->position = $position;
  }
  /**
   * @return Position
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Optional. Input only. The PullRequestComment resource name that this
   * comment is replying to.
   *
   * @param string $reply
   */
  public function setReply($reply)
  {
    $this->reply = $reply;
  }
  /**
   * @return string
   */
  public function getReply()
  {
    return $this->reply;
  }
  /**
   * Output only. Boolean indicator if the comment is resolved.
   *
   * @param bool $resolved
   */
  public function setResolved($resolved)
  {
    $this->resolved = $resolved;
  }
  /**
   * @return bool
   */
  public function getResolved()
  {
    return $this->resolved;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Code::class, 'Google_Service_SecureSourceManager_Code');
