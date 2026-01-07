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

namespace Google\Service\GKEHub;

class ClusterUpgradeGKEUpgrade extends \Google\Model
{
  /**
   * Name of the upgrade, e.g., "k8s_control_plane".
   *
   * @var string
   */
  public $name;
  /**
   * Version of the upgrade, e.g., "1.22.1-gke.100".
   *
   * @var string
   */
  public $version;

  /**
   * Name of the upgrade, e.g., "k8s_control_plane".
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
   * Version of the upgrade, e.g., "1.22.1-gke.100".
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeGKEUpgrade::class, 'Google_Service_GKEHub_ClusterUpgradeGKEUpgrade');
