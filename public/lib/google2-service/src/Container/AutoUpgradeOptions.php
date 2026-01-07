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

namespace Google\Service\Container;

class AutoUpgradeOptions extends \Google\Model
{
  /**
   * Output only. This field is set when upgrades are about to commence with the
   * approximate start time for the upgrades, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $autoUpgradeStartTime;
  /**
   * Output only. This field is set when upgrades are about to commence with the
   * description of the upgrade.
   *
   * @var string
   */
  public $description;

  /**
   * Output only. This field is set when upgrades are about to commence with the
   * approximate start time for the upgrades, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @param string $autoUpgradeStartTime
   */
  public function setAutoUpgradeStartTime($autoUpgradeStartTime)
  {
    $this->autoUpgradeStartTime = $autoUpgradeStartTime;
  }
  /**
   * @return string
   */
  public function getAutoUpgradeStartTime()
  {
    return $this->autoUpgradeStartTime;
  }
  /**
   * Output only. This field is set when upgrades are about to commence with the
   * description of the upgrade.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoUpgradeOptions::class, 'Google_Service_Container_AutoUpgradeOptions');
