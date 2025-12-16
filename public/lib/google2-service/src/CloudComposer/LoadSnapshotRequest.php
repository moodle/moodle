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

class LoadSnapshotRequest extends \Google\Model
{
  /**
   * Whether or not to skip setting Airflow overrides when loading the
   * environment's state.
   *
   * @var bool
   */
  public $skipAirflowOverridesSetting;
  /**
   * Whether or not to skip setting environment variables when loading the
   * environment's state.
   *
   * @var bool
   */
  public $skipEnvironmentVariablesSetting;
  /**
   * Whether or not to skip copying Cloud Storage data when loading the
   * environment's state.
   *
   * @var bool
   */
  public $skipGcsDataCopying;
  /**
   * Whether or not to skip installing Pypi packages when loading the
   * environment's state.
   *
   * @var bool
   */
  public $skipPypiPackagesInstallation;
  /**
   * A Cloud Storage path to a snapshot to load, e.g.: "gs://my-
   * bucket/snapshots/project_location_environment_timestamp".
   *
   * @var string
   */
  public $snapshotPath;

  /**
   * Whether or not to skip setting Airflow overrides when loading the
   * environment's state.
   *
   * @param bool $skipAirflowOverridesSetting
   */
  public function setSkipAirflowOverridesSetting($skipAirflowOverridesSetting)
  {
    $this->skipAirflowOverridesSetting = $skipAirflowOverridesSetting;
  }
  /**
   * @return bool
   */
  public function getSkipAirflowOverridesSetting()
  {
    return $this->skipAirflowOverridesSetting;
  }
  /**
   * Whether or not to skip setting environment variables when loading the
   * environment's state.
   *
   * @param bool $skipEnvironmentVariablesSetting
   */
  public function setSkipEnvironmentVariablesSetting($skipEnvironmentVariablesSetting)
  {
    $this->skipEnvironmentVariablesSetting = $skipEnvironmentVariablesSetting;
  }
  /**
   * @return bool
   */
  public function getSkipEnvironmentVariablesSetting()
  {
    return $this->skipEnvironmentVariablesSetting;
  }
  /**
   * Whether or not to skip copying Cloud Storage data when loading the
   * environment's state.
   *
   * @param bool $skipGcsDataCopying
   */
  public function setSkipGcsDataCopying($skipGcsDataCopying)
  {
    $this->skipGcsDataCopying = $skipGcsDataCopying;
  }
  /**
   * @return bool
   */
  public function getSkipGcsDataCopying()
  {
    return $this->skipGcsDataCopying;
  }
  /**
   * Whether or not to skip installing Pypi packages when loading the
   * environment's state.
   *
   * @param bool $skipPypiPackagesInstallation
   */
  public function setSkipPypiPackagesInstallation($skipPypiPackagesInstallation)
  {
    $this->skipPypiPackagesInstallation = $skipPypiPackagesInstallation;
  }
  /**
   * @return bool
   */
  public function getSkipPypiPackagesInstallation()
  {
    return $this->skipPypiPackagesInstallation;
  }
  /**
   * A Cloud Storage path to a snapshot to load, e.g.: "gs://my-
   * bucket/snapshots/project_location_environment_timestamp".
   *
   * @param string $snapshotPath
   */
  public function setSnapshotPath($snapshotPath)
  {
    $this->snapshotPath = $snapshotPath;
  }
  /**
   * @return string
   */
  public function getSnapshotPath()
  {
    return $this->snapshotPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadSnapshotRequest::class, 'Google_Service_CloudComposer_LoadSnapshotRequest');
