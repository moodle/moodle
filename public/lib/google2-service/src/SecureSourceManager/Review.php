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

class Review extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ACTION_TYPE_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * A general review comment.
   */
  public const ACTION_TYPE_COMMENT = 'COMMENT';
  /**
   * Change required from this review.
   */
  public const ACTION_TYPE_CHANGE_REQUESTED = 'CHANGE_REQUESTED';
  /**
   * Change approved from this review.
   */
  public const ACTION_TYPE_APPROVED = 'APPROVED';
  /**
   * Required. The review action type.
   *
   * @var string
   */
  public $actionType;
  /**
   * Optional. The comment body.
   *
   * @var string
   */
  public $body;
  /**
   * Output only. The effective commit sha this review is pointing to.
   *
   * @var string
   */
  public $effectiveCommitSha;

  /**
   * Required. The review action type.
   *
   * Accepted values: ACTION_TYPE_UNSPECIFIED, COMMENT, CHANGE_REQUESTED,
   * APPROVED
   *
   * @param self::ACTION_TYPE_* $actionType
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return self::ACTION_TYPE_*
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * Optional. The comment body.
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
   * Output only. The effective commit sha this review is pointing to.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Review::class, 'Google_Service_SecureSourceManager_Review');
