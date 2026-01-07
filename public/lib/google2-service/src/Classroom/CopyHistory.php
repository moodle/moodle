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

namespace Google\Service\Classroom;

class CopyHistory extends \Google\Model
{
  /**
   * Immutable. Identifier of the attachment.
   *
   * @var string
   */
  public $attachmentId;
  /**
   * Immutable. Identifier of the course.
   *
   * @var string
   */
  public $courseId;
  /**
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached.
   *
   * @var string
   */
  public $itemId;
  /**
   * Immutable. Deprecated, use `item_id` instead.
   *
   * @deprecated
   * @var string
   */
  public $postId;

  /**
   * Immutable. Identifier of the attachment.
   *
   * @param string $attachmentId
   */
  public function setAttachmentId($attachmentId)
  {
    $this->attachmentId = $attachmentId;
  }
  /**
   * @return string
   */
  public function getAttachmentId()
  {
    return $this->attachmentId;
  }
  /**
   * Immutable. Identifier of the course.
   *
   * @param string $courseId
   */
  public function setCourseId($courseId)
  {
    $this->courseId = $courseId;
  }
  /**
   * @return string
   */
  public function getCourseId()
  {
    return $this->courseId;
  }
  /**
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * Immutable. Deprecated, use `item_id` instead.
   *
   * @deprecated
   * @param string $postId
   */
  public function setPostId($postId)
  {
    $this->postId = $postId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPostId()
  {
    return $this->postId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CopyHistory::class, 'Google_Service_Classroom_CopyHistory');
