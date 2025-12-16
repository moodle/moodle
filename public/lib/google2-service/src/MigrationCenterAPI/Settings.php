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

namespace Google\Service\MigrationCenterAPI;

class Settings extends \Google\Model
{
  /**
   * Disable Cloud Logging for the Migration Center API. Users are billed for
   * the logs.
   *
   * @var bool
   */
  public $disableCloudLogging;
  /**
   * Output only. The name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * The preference set used by default for a project.
   *
   * @var string
   */
  public $preferenceSet;

  /**
   * Disable Cloud Logging for the Migration Center API. Users are billed for
   * the logs.
   *
   * @param bool $disableCloudLogging
   */
  public function setDisableCloudLogging($disableCloudLogging)
  {
    $this->disableCloudLogging = $disableCloudLogging;
  }
  /**
   * @return bool
   */
  public function getDisableCloudLogging()
  {
    return $this->disableCloudLogging;
  }
  /**
   * Output only. The name of the resource.
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
   * The preference set used by default for a project.
   *
   * @param string $preferenceSet
   */
  public function setPreferenceSet($preferenceSet)
  {
    $this->preferenceSet = $preferenceSet;
  }
  /**
   * @return string
   */
  public function getPreferenceSet()
  {
    return $this->preferenceSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Settings::class, 'Google_Service_MigrationCenterAPI_Settings');
