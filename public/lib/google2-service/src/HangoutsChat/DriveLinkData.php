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

namespace Google\Service\HangoutsChat;

class DriveLinkData extends \Google\Model
{
  protected $driveDataRefType = DriveDataRef::class;
  protected $driveDataRefDataType = '';
  /**
   * The mime type of the linked Google Drive resource.
   *
   * @var string
   */
  public $mimeType;

  /**
   * A [DriveDataRef](https://developers.google.com/workspace/chat/api/reference
   * /rest/v1/spaces.messages.attachments#drivedataref) which references a
   * Google Drive file.
   *
   * @param DriveDataRef $driveDataRef
   */
  public function setDriveDataRef(DriveDataRef $driveDataRef)
  {
    $this->driveDataRef = $driveDataRef;
  }
  /**
   * @return DriveDataRef
   */
  public function getDriveDataRef()
  {
    return $this->driveDataRef;
  }
  /**
   * The mime type of the linked Google Drive resource.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveLinkData::class, 'Google_Service_HangoutsChat_DriveLinkData');
