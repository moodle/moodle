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

namespace Google\Service\DriveActivity;

class FileComment extends \Google\Model
{
  /**
   * The comment in the discussion thread. This identifier is an opaque string
   * compatible with the Drive API; see
   * https://developers.google.com/workspace/drive/v3/reference/comments/get
   *
   * @var string
   */
  public $legacyCommentId;
  /**
   * The discussion thread to which the comment was added. This identifier is an
   * opaque string compatible with the Drive API and references the first
   * comment in a discussion; see
   * https://developers.google.com/workspace/drive/v3/reference/comments/get
   *
   * @var string
   */
  public $legacyDiscussionId;
  /**
   * The link to the discussion thread containing this comment, for example,
   * `https://docs.google.com/DOCUMENT_ID/edit?disco=THREAD_ID`.
   *
   * @var string
   */
  public $linkToDiscussion;
  protected $parentType = DriveItem::class;
  protected $parentDataType = '';

  /**
   * The comment in the discussion thread. This identifier is an opaque string
   * compatible with the Drive API; see
   * https://developers.google.com/workspace/drive/v3/reference/comments/get
   *
   * @param string $legacyCommentId
   */
  public function setLegacyCommentId($legacyCommentId)
  {
    $this->legacyCommentId = $legacyCommentId;
  }
  /**
   * @return string
   */
  public function getLegacyCommentId()
  {
    return $this->legacyCommentId;
  }
  /**
   * The discussion thread to which the comment was added. This identifier is an
   * opaque string compatible with the Drive API and references the first
   * comment in a discussion; see
   * https://developers.google.com/workspace/drive/v3/reference/comments/get
   *
   * @param string $legacyDiscussionId
   */
  public function setLegacyDiscussionId($legacyDiscussionId)
  {
    $this->legacyDiscussionId = $legacyDiscussionId;
  }
  /**
   * @return string
   */
  public function getLegacyDiscussionId()
  {
    return $this->legacyDiscussionId;
  }
  /**
   * The link to the discussion thread containing this comment, for example,
   * `https://docs.google.com/DOCUMENT_ID/edit?disco=THREAD_ID`.
   *
   * @param string $linkToDiscussion
   */
  public function setLinkToDiscussion($linkToDiscussion)
  {
    $this->linkToDiscussion = $linkToDiscussion;
  }
  /**
   * @return string
   */
  public function getLinkToDiscussion()
  {
    return $this->linkToDiscussion;
  }
  /**
   * The Drive item containing this comment.
   *
   * @param DriveItem $parent
   */
  public function setParent(DriveItem $parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return DriveItem
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileComment::class, 'Google_Service_DriveActivity_FileComment');
