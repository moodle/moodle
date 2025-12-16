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

class TeamDriveReference extends \Google\Model
{
  /**
   * This field is deprecated; please see `DriveReference.name` instead.
   *
   * @var string
   */
  public $name;
  /**
   * This field is deprecated; please see `DriveReference.title` instead.
   *
   * @var string
   */
  public $title;

  /**
   * This field is deprecated; please see `DriveReference.name` instead.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * This field is deprecated; please see `DriveReference.title` instead.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TeamDriveReference::class, 'Google_Service_DriveActivity_TeamDriveReference');
