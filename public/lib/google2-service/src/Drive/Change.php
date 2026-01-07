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

class Change extends \Google\Model
{
  /**
   * The type of the change. Possible values are `file` and `drive`.
   *
   * @var string
   */
  public $changeType;
  protected $driveType = Drive::class;
  protected $driveDataType = '';
  /**
   * The ID of the shared drive associated with this change.
   *
   * @var string
   */
  public $driveId;
  protected $fileType = DriveFile::class;
  protected $fileDataType = '';
  /**
   * The ID of the file which has changed.
   *
   * @var string
   */
  public $fileId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#change"`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether the file or shared drive has been removed from this list of
   * changes, for example by deletion or loss of access.
   *
   * @var bool
   */
  public $removed;
  protected $teamDriveType = TeamDrive::class;
  protected $teamDriveDataType = '';
  /**
   * Deprecated: Use `driveId` instead.
   *
   * @deprecated
   * @var string
   */
  public $teamDriveId;
  /**
   * The time of this change (RFC 3339 date-time).
   *
   * @var string
   */
  public $time;
  /**
   * Deprecated: Use `changeType` instead.
   *
   * @deprecated
   * @var string
   */
  public $type;

  /**
   * The type of the change. Possible values are `file` and `drive`.
   *
   * @param string $changeType
   */
  public function setChangeType($changeType)
  {
    $this->changeType = $changeType;
  }
  /**
   * @return string
   */
  public function getChangeType()
  {
    return $this->changeType;
  }
  /**
   * The updated state of the shared drive. Present if the changeType is drive,
   * the user is still a member of the shared drive, and the shared drive has
   * not been deleted.
   *
   * @param Drive $drive
   */
  public function setDrive(Drive $drive)
  {
    $this->drive = $drive;
  }
  /**
   * @return Drive
   */
  public function getDrive()
  {
    return $this->drive;
  }
  /**
   * The ID of the shared drive associated with this change.
   *
   * @param string $driveId
   */
  public function setDriveId($driveId)
  {
    $this->driveId = $driveId;
  }
  /**
   * @return string
   */
  public function getDriveId()
  {
    return $this->driveId;
  }
  /**
   * The updated state of the file. Present if the type is file and the file has
   * not been removed from this list of changes.
   *
   * @param DriveFile $file
   */
  public function setFile(DriveFile $file)
  {
    $this->file = $file;
  }
  /**
   * @return DriveFile
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * The ID of the file which has changed.
   *
   * @param string $fileId
   */
  public function setFileId($fileId)
  {
    $this->fileId = $fileId;
  }
  /**
   * @return string
   */
  public function getFileId()
  {
    return $this->fileId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#change"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Whether the file or shared drive has been removed from this list of
   * changes, for example by deletion or loss of access.
   *
   * @param bool $removed
   */
  public function setRemoved($removed)
  {
    $this->removed = $removed;
  }
  /**
   * @return bool
   */
  public function getRemoved()
  {
    return $this->removed;
  }
  /**
   * Deprecated: Use `drive` instead.
   *
   * @deprecated
   * @param TeamDrive $teamDrive
   */
  public function setTeamDrive(TeamDrive $teamDrive)
  {
    $this->teamDrive = $teamDrive;
  }
  /**
   * @deprecated
   * @return TeamDrive
   */
  public function getTeamDrive()
  {
    return $this->teamDrive;
  }
  /**
   * Deprecated: Use `driveId` instead.
   *
   * @deprecated
   * @param string $teamDriveId
   */
  public function setTeamDriveId($teamDriveId)
  {
    $this->teamDriveId = $teamDriveId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTeamDriveId()
  {
    return $this->teamDriveId;
  }
  /**
   * The time of this change (RFC 3339 date-time).
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
  /**
   * Deprecated: Use `changeType` instead.
   *
   * @deprecated
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Change::class, 'Google_Service_Drive_Change');
