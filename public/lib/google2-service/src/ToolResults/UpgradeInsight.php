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

namespace Google\Service\ToolResults;

class UpgradeInsight extends \Google\Model
{
  /**
   * The name of the package to be upgraded.
   *
   * @var string
   */
  public $packageName;
  /**
   * The suggested version to upgrade to. Optional: In case we are not sure
   * which version solves this problem
   *
   * @var string
   */
  public $upgradeToVersion;

  /**
   * The name of the package to be upgraded.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * The suggested version to upgrade to. Optional: In case we are not sure
   * which version solves this problem
   *
   * @param string $upgradeToVersion
   */
  public function setUpgradeToVersion($upgradeToVersion)
  {
    $this->upgradeToVersion = $upgradeToVersion;
  }
  /**
   * @return string
   */
  public function getUpgradeToVersion()
  {
    return $this->upgradeToVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeInsight::class, 'Google_Service_ToolResults_UpgradeInsight');
