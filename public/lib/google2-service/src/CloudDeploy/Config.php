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

namespace Google\Service\CloudDeploy;

class Config extends \Google\Collection
{
  protected $collection_key = 'supportedVersions';
  /**
   * Default Skaffold version that is assigned when a Release is created without
   * specifying a Skaffold version.
   *
   * @var string
   */
  public $defaultSkaffoldVersion;
  protected $defaultToolVersionsType = ToolVersions::class;
  protected $defaultToolVersionsDataType = '';
  /**
   * Name of the configuration.
   *
   * @var string
   */
  public $name;
  protected $supportedVersionsType = SkaffoldVersion::class;
  protected $supportedVersionsDataType = 'array';

  /**
   * Default Skaffold version that is assigned when a Release is created without
   * specifying a Skaffold version.
   *
   * @param string $defaultSkaffoldVersion
   */
  public function setDefaultSkaffoldVersion($defaultSkaffoldVersion)
  {
    $this->defaultSkaffoldVersion = $defaultSkaffoldVersion;
  }
  /**
   * @return string
   */
  public function getDefaultSkaffoldVersion()
  {
    return $this->defaultSkaffoldVersion;
  }
  /**
   * Output only. Default tool versions. These tool versions are assigned when a
   * Release is created without specifying tool versions.
   *
   * @param ToolVersions $defaultToolVersions
   */
  public function setDefaultToolVersions(ToolVersions $defaultToolVersions)
  {
    $this->defaultToolVersions = $defaultToolVersions;
  }
  /**
   * @return ToolVersions
   */
  public function getDefaultToolVersions()
  {
    return $this->defaultToolVersions;
  }
  /**
   * Name of the configuration.
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
   * All supported versions of Skaffold.
   *
   * @param SkaffoldVersion[] $supportedVersions
   */
  public function setSupportedVersions($supportedVersions)
  {
    $this->supportedVersions = $supportedVersions;
  }
  /**
   * @return SkaffoldVersion[]
   */
  public function getSupportedVersions()
  {
    return $this->supportedVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Config::class, 'Google_Service_CloudDeploy_Config');
