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

namespace Google\Service\SaaSServiceManagement;

class ReleaseRequirements extends \Google\Collection
{
  protected $collection_key = 'upgradeableFromReleases';
  /**
   * Optional. A list of releases from which a unit can be upgraded to this one
   * (optional). If left empty no constraints will be applied. When provided,
   * unit upgrade requests to this release will check and enforce this
   * constraint.
   *
   * @var string[]
   */
  public $upgradeableFromReleases;

  /**
   * Optional. A list of releases from which a unit can be upgraded to this one
   * (optional). If left empty no constraints will be applied. When provided,
   * unit upgrade requests to this release will check and enforce this
   * constraint.
   *
   * @param string[] $upgradeableFromReleases
   */
  public function setUpgradeableFromReleases($upgradeableFromReleases)
  {
    $this->upgradeableFromReleases = $upgradeableFromReleases;
  }
  /**
   * @return string[]
   */
  public function getUpgradeableFromReleases()
  {
    return $this->upgradeableFromReleases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReleaseRequirements::class, 'Google_Service_SaaSServiceManagement_ReleaseRequirements');
