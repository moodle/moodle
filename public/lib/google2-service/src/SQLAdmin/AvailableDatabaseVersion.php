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

namespace Google\Service\SQLAdmin;

class AvailableDatabaseVersion extends \Google\Model
{
  /**
   * The database version's display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The version's major version name.
   *
   * @var string
   */
  public $majorVersion;
  /**
   * The database version name. For MySQL 8.0, this string provides the database
   * major and minor version.
   *
   * @var string
   */
  public $name;

  /**
   * The database version's display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The version's major version name.
   *
   * @param string $majorVersion
   */
  public function setMajorVersion($majorVersion)
  {
    $this->majorVersion = $majorVersion;
  }
  /**
   * @return string
   */
  public function getMajorVersion()
  {
    return $this->majorVersion;
  }
  /**
   * The database version name. For MySQL 8.0, this string provides the database
   * major and minor version.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AvailableDatabaseVersion::class, 'Google_Service_SQLAdmin_AvailableDatabaseVersion');
