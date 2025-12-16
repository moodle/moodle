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

namespace Google\Service\Backupdr;

class BackupGcpResource extends \Google\Model
{
  /**
   * Name of the Google Cloud resource.
   *
   * @var string
   */
  public $gcpResourcename;
  /**
   * Location of the resource: //"global"/"unspecified".
   *
   * @var string
   */
  public $location;
  /**
   * Type of the resource. Use the Unified Resource Type, eg.
   * compute.googleapis.com/Instance.
   *
   * @var string
   */
  public $type;

  /**
   * Name of the Google Cloud resource.
   *
   * @param string $gcpResourcename
   */
  public function setGcpResourcename($gcpResourcename)
  {
    $this->gcpResourcename = $gcpResourcename;
  }
  /**
   * @return string
   */
  public function getGcpResourcename()
  {
    return $this->gcpResourcename;
  }
  /**
   * Location of the resource: //"global"/"unspecified".
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Type of the resource. Use the Unified Resource Type, eg.
   * compute.googleapis.com/Instance.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupGcpResource::class, 'Google_Service_Backupdr_BackupGcpResource');
