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

namespace Google\Service\GKEOnPrem;

class UpgradeDependency extends \Google\Model
{
  /**
   * Current version of the dependency e.g. 1.15.0.
   *
   * @var string
   */
  public $currentVersion;
  /**
   * Membership names are formatted as `projects//locations//memberships/`.
   *
   * @var string
   */
  public $membership;
  /**
   * Resource name of the dependency.
   *
   * @var string
   */
  public $resourceName;
  /**
   * Target version of the dependency e.g. 1.16.1. This is the version the
   * dependency needs to be upgraded to before a resource can be upgraded.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * Current version of the dependency e.g. 1.15.0.
   *
   * @param string $currentVersion
   */
  public function setCurrentVersion($currentVersion)
  {
    $this->currentVersion = $currentVersion;
  }
  /**
   * @return string
   */
  public function getCurrentVersion()
  {
    return $this->currentVersion;
  }
  /**
   * Membership names are formatted as `projects//locations//memberships/`.
   *
   * @param string $membership
   */
  public function setMembership($membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return string
   */
  public function getMembership()
  {
    return $this->membership;
  }
  /**
   * Resource name of the dependency.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Target version of the dependency e.g. 1.16.1. This is the version the
   * dependency needs to be upgraded to before a resource can be upgraded.
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeDependency::class, 'Google_Service_GKEOnPrem_UpgradeDependency');
