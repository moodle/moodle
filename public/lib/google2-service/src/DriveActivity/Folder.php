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

class Folder extends \Google\Model
{
  /**
   * This item is deprecated; please see `DriveFolder.Type` instead.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * This item is deprecated; please see `DriveFolder.Type` instead.
   */
  public const TYPE_MY_DRIVE_ROOT = 'MY_DRIVE_ROOT';
  /**
   * This item is deprecated; please see `DriveFolder.Type` instead.
   */
  public const TYPE_TEAM_DRIVE_ROOT = 'TEAM_DRIVE_ROOT';
  /**
   * This item is deprecated; please see `DriveFolder.Type` instead.
   */
  public const TYPE_STANDARD_FOLDER = 'STANDARD_FOLDER';
  /**
   * This field is deprecated; please see `DriveFolder.type` instead.
   *
   * @var string
   */
  public $type;

  /**
   * This field is deprecated; please see `DriveFolder.type` instead.
   *
   * Accepted values: TYPE_UNSPECIFIED, MY_DRIVE_ROOT, TEAM_DRIVE_ROOT,
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
class_alias(Folder::class, 'Google_Service_DriveActivity_Folder');
