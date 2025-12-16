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

namespace Google\Service\Dfareporting;

class OperatingSystemVersion extends \Google\Model
{
  /**
   * ID of this operating system version.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#operatingSystemVersion".
   *
   * @var string
   */
  public $kind;
  /**
   * Major version (leftmost number) of this operating system version.
   *
   * @var string
   */
  public $majorVersion;
  /**
   * Minor version (number after the first dot) of this operating system
   * version.
   *
   * @var string
   */
  public $minorVersion;
  /**
   * Name of this operating system version.
   *
   * @var string
   */
  public $name;
  protected $operatingSystemType = OperatingSystem::class;
  protected $operatingSystemDataType = '';

  /**
   * ID of this operating system version.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#operatingSystemVersion".
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
   * Major version (leftmost number) of this operating system version.
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
   * Minor version (number after the first dot) of this operating system
   * version.
   *
   * @param string $minorVersion
   */
  public function setMinorVersion($minorVersion)
  {
    $this->minorVersion = $minorVersion;
  }
  /**
   * @return string
   */
  public function getMinorVersion()
  {
    return $this->minorVersion;
  }
  /**
   * Name of this operating system version.
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
   * Operating system of this operating system version.
   *
   * @param OperatingSystem $operatingSystem
   */
  public function setOperatingSystem(OperatingSystem $operatingSystem)
  {
    $this->operatingSystem = $operatingSystem;
  }
  /**
   * @return OperatingSystem
   */
  public function getOperatingSystem()
  {
    return $this->operatingSystem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperatingSystemVersion::class, 'Google_Service_Dfareporting_OperatingSystemVersion');
