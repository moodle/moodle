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

namespace Google\Service\Drive;

class DriveFileShortcutDetails extends \Google\Model
{
  /**
   * The ID of the file that this shortcut points to. Can only be set on
   * `files.create` requests.
   *
   * @var string
   */
  public $targetId;
  /**
   * Output only. The MIME type of the file that this shortcut points to. The
   * value of this field is a snapshot of the target's MIME type, captured when
   * the shortcut is created.
   *
   * @var string
   */
  public $targetMimeType;
  /**
   * Output only. The `resourceKey` for the target file.
   *
   * @var string
   */
  public $targetResourceKey;

  /**
   * The ID of the file that this shortcut points to. Can only be set on
   * `files.create` requests.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * Output only. The MIME type of the file that this shortcut points to. The
   * value of this field is a snapshot of the target's MIME type, captured when
   * the shortcut is created.
   *
   * @param string $targetMimeType
   */
  public function setTargetMimeType($targetMimeType)
  {
    $this->targetMimeType = $targetMimeType;
  }
  /**
   * @return string
   */
  public function getTargetMimeType()
  {
    return $this->targetMimeType;
  }
  /**
   * Output only. The `resourceKey` for the target file.
   *
   * @param string $targetResourceKey
   */
  public function setTargetResourceKey($targetResourceKey)
  {
    $this->targetResourceKey = $targetResourceKey;
  }
  /**
   * @return string
   */
  public function getTargetResourceKey()
  {
    return $this->targetResourceKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFileShortcutDetails::class, 'Google_Service_Drive_DriveFileShortcutDetails');
