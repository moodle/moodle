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

class Blueprint extends \Google\Model
{
  /**
   * Output only. Type of the engine used to actuate the blueprint. e.g.
   * terraform, helm etc.
   *
   * @var string
   */
  public $engine;
  /**
   * Optional. Immutable. URI to a blueprint used by the Unit (required unless
   * unitKind or release is set).
   *
   * @var string
   */
  public $package;
  /**
   * Output only. Version metadata if present on the blueprint.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Type of the engine used to actuate the blueprint. e.g.
   * terraform, helm etc.
   *
   * @param string $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return string
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Optional. Immutable. URI to a blueprint used by the Unit (required unless
   * unitKind or release is set).
   *
   * @param string $package
   */
  public function setPackage($package)
  {
    $this->package = $package;
  }
  /**
   * @return string
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * Output only. Version metadata if present on the blueprint.
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
class_alias(Blueprint::class, 'Google_Service_SaaSServiceManagement_Blueprint');
