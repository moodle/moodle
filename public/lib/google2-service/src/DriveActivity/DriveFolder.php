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

class DriveFolder extends \Google\Model
{
  /**
   * The folder type is unknown.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The folder is the root of a user's MyDrive.
   */
  public const TYPE_MY_DRIVE_ROOT = 'MY_DRIVE_ROOT';
  /**
   * The folder is the root of a shared drive.
   */
  public const TYPE_SHARED_DRIVE_ROOT = 'SHARED_DRIVE_ROOT';
  /**
   * The folder is a standard, non-root, folder.
   */
  public const TYPE_STANDARD_FOLDER = 'STANDARD_FOLDER';
  /**
   * The type of Drive folder.
   *
   * @var string
   */
  public $type;

  /**
   * The type of Drive folder.
   *
   * Accepted values: TYPE_UNSPECIFIED, MY_DRIVE_ROOT, SHARED_DRIVE_ROOT,
   * STANDARD_FOLDER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFolder::class, 'Google_Service_DriveActivity_DriveFolder');
