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

class SharedDriveFile extends \Google\Model
{
  /**
   * No sharing mode specified. This should never be returned.
   */
  public const SHARE_MODE_UNKNOWN_SHARE_MODE = 'UNKNOWN_SHARE_MODE';
  /**
   * Students can view the shared file.
   */
  public const SHARE_MODE_VIEW = 'VIEW';
  /**
   * Students can edit the shared file.
   */
  public const SHARE_MODE_EDIT = 'EDIT';
  /**
   * Students have a personal copy of the shared file.
   */
  public const SHARE_MODE_STUDENT_COPY = 'STUDENT_COPY';
  protected $driveFileType = DriveFile::class;
  protected $driveFileDataType = '';
  /**
   * Mechanism by which students access the Drive item.
   *
   * @var string
   */
  public $shareMode;

  /**
   * Drive file details.
   *
   * @param DriveFile $driveFile
   */
  public function setDriveFile(DriveFile $driveFile)
  {
    $this->driveFile = $driveFile;
  }
  /**
   * @return DriveFile
   */
  public function getDriveFile()
  {
    return $this->driveFile;
  }
  /**
   * Mechanism by which students access the Drive item.
   *
   * Accepted values: UNKNOWN_SHARE_MODE, VIEW, EDIT, STUDENT_COPY
   *
   * @param self::SHARE_MODE_* $shareMode
   */
  public function setShareMode($shareMode)
  {
    $this->shareMode = $shareMode;
  }
  /**
   * @return self::SHARE_MODE_*
   */
  public function getShareMode()
  {
    return $this->shareMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SharedDriveFile::class, 'Google_Service_Classroom_SharedDriveFile');
