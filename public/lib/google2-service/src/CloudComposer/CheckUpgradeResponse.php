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

namespace Google\Service\CloudComposer;

class CheckUpgradeResponse extends \Google\Collection
{
  /**
   * It is unknown whether build had conflicts or not.
   */
  public const CONTAINS_PYPI_MODULES_CONFLICT_CONFLICT_RESULT_UNSPECIFIED = 'CONFLICT_RESULT_UNSPECIFIED';
  /**
   * There were python packages conflicts.
   */
  public const CONTAINS_PYPI_MODULES_CONFLICT_CONFLICT = 'CONFLICT';
  /**
   * There were no python packages conflicts.
   */
  public const CONTAINS_PYPI_MODULES_CONFLICT_NO_CONFLICT = 'NO_CONFLICT';
  protected $collection_key = 'configConflicts';
  /**
   * Output only. Url for a docker build log of an upgraded image.
   *
   * @var string
   */
  public $buildLogUri;
  protected $configConflictsType = ConfigConflict::class;
  protected $configConflictsDataType = 'array';
  /**
   * Output only. Whether build has succeeded or failed on modules conflicts.
   *
   * @var string
   */
  public $containsPypiModulesConflict;
  /**
   * Composer image for which the build was happening.
   *
   * @var string
   */
  public $imageVersion;
  /**
   * Output only. Extract from a docker image build log containing information
   * about pypi modules conflicts.
   *
   * @var string
   */
  public $pypiConflictBuildLogExtract;
  /**
   * Pypi dependencies specified in the environment configuration, at the time
   * when the build was triggered.
   *
   * @var string[]
   */
  public $pypiDependencies;

  /**
   * Output only. Url for a docker build log of an upgraded image.
   *
   * @param string $buildLogUri
   */
  public function setBuildLogUri($buildLogUri)
  {
    $this->buildLogUri = $buildLogUri;
  }
  /**
   * @return string
   */
  public function getBuildLogUri()
  {
    return $this->buildLogUri;
  }
  /**
   * Output only. Contains information about environment configuration that is
   * incompatible with the new image version, except for pypi modules conflicts.
   *
   * @param ConfigConflict[] $configConflicts
   */
  public function setConfigConflicts($configConflicts)
  {
    $this->configConflicts = $configConflicts;
  }
  /**
   * @return ConfigConflict[]
   */
  public function getConfigConflicts()
  {
    return $this->configConflicts;
  }
  /**
   * Output only. Whether build has succeeded or failed on modules conflicts.
   *
   * Accepted values: CONFLICT_RESULT_UNSPECIFIED, CONFLICT, NO_CONFLICT
   *
   * @param self::CONTAINS_PYPI_MODULES_CONFLICT_* $containsPypiModulesConflict
   */
  public function setContainsPypiModulesConflict($containsPypiModulesConflict)
  {
    $this->containsPypiModulesConflict = $containsPypiModulesConflict;
  }
  /**
   * @return self::CONTAINS_PYPI_MODULES_CONFLICT_*
   */
  public function getContainsPypiModulesConflict()
  {
    return $this->containsPypiModulesConflict;
  }
  /**
   * Composer image for which the build was happening.
   *
   * @param string $imageVersion
   */
  public function setImageVersion($imageVersion)
  {
    $this->imageVersion = $imageVersion;
  }
  /**
   * @return string
   */
  public function getImageVersion()
  {
    return $this->imageVersion;
  }
  /**
   * Output only. Extract from a docker image build log containing information
   * about pypi modules conflicts.
   *
   * @param string $pypiConflictBuildLogExtract
   */
  public function setPypiConflictBuildLogExtract($pypiConflictBuildLogExtract)
  {
    $this->pypiConflictBuildLogExtract = $pypiConflictBuildLogExtract;
  }
  /**
   * @return string
   */
  public function getPypiConflictBuildLogExtract()
  {
    return $this->pypiConflictBuildLogExtract;
  }
  /**
   * Pypi dependencies specified in the environment configuration, at the time
   * when the build was triggered.
   *
   * @param string[] $pypiDependencies
   */
  public function setPypiDependencies($pypiDependencies)
  {
    $this->pypiDependencies = $pypiDependencies;
  }
  /**
   * @return string[]
   */
  public function getPypiDependencies()
  {
    return $this->pypiDependencies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckUpgradeResponse::class, 'Google_Service_CloudComposer_CheckUpgradeResponse');
