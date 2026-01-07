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

class DriveReference extends \Google\Model
{
  /**
   * The resource name of the shared drive. The format is
   * `COLLECTION_ID/DRIVE_ID`. Clients should not assume a specific collection
   * ID for this resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The title of the shared drive.
   *
   * @var string
   */
  public $title;

  /**
   * The resource name of the shared drive. The format is
   * `COLLECTION_ID/DRIVE_ID`. Clients should not assume a specific collection
   * ID for this resource name.
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
   * The title of the shared drive.
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
class_alias(DriveReference::class, 'Google_Service_DriveActivity_DriveReference');
