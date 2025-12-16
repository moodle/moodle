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

class OperatingSystem extends \Google\Model
{
  /**
   * DART ID of this operating system. This is the ID used for targeting.
   *
   * @var string
   */
  public $dartId;
  /**
   * Whether this operating system is for desktop.
   *
   * @var bool
   */
  public $desktop;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#operatingSystem".
   *
   * @var string
   */
  public $kind;
  /**
   * Whether this operating system is for mobile.
   *
   * @var bool
   */
  public $mobile;
  /**
   * Name of this operating system.
   *
   * @var string
   */
  public $name;

  /**
   * DART ID of this operating system. This is the ID used for targeting.
   *
   * @param string $dartId
   */
  public function setDartId($dartId)
  {
    $this->dartId = $dartId;
  }
  /**
   * @return string
   */
  public function getDartId()
  {
    return $this->dartId;
  }
  /**
   * Whether this operating system is for desktop.
   *
   * @param bool $desktop
   */
  public function setDesktop($desktop)
  {
    $this->desktop = $desktop;
  }
  /**
   * @return bool
   */
  public function getDesktop()
  {
    return $this->desktop;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#operatingSystem".
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
   * Whether this operating system is for mobile.
   *
   * @param bool $mobile
   */
  public function setMobile($mobile)
  {
    $this->mobile = $mobile;
  }
  /**
   * @return bool
   */
  public function getMobile()
  {
    return $this->mobile;
  }
  /**
   * Name of this operating system.
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
class_alias(OperatingSystem::class, 'Google_Service_Dfareporting_OperatingSystem');
