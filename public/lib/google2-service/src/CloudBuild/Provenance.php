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

namespace Google\Service\CloudBuild;

class Provenance extends \Google\Model
{
  /**
   * Default to disabled (before AA regionalization), optimistic after
   */
  public const ENABLED_ENABLED_UNSPECIFIED = 'ENABLED_UNSPECIFIED';
  /**
   * Provenance failures would fail the run
   */
  public const ENABLED_REQUIRED = 'REQUIRED';
  /**
   * GCB will attempt to push to artifact analaysis and build state would not be
   * impacted by the push failures.
   */
  public const ENABLED_OPTIMISTIC = 'OPTIMISTIC';
  /**
   * Disable the provenance push entirely.
   */
  public const ENABLED_DISABLED = 'DISABLED';
  /**
   * The PipelineRun/TaskRun/Workflow will be rejected. Update this comment to
   * push to the same region as the run in Artifact Analysis when it's
   * regionalized.
   */
  public const REGION_REGION_UNSPECIFIED = 'REGION_UNSPECIFIED';
  /**
   * Push provenance to Artifact Analysis in global region.
   */
  public const REGION_GLOBAL = 'GLOBAL';
  /**
   * Default PREFER_ARTIFACT_PROJECT.
   */
  public const STORAGE_STORAGE_UNSPECIFIED = 'STORAGE_UNSPECIFIED';
  /**
   * GCB will attempt to push provenance to the artifact project. If it is not
   * available, fallback to build project.
   */
  public const STORAGE_PREFER_ARTIFACT_PROJECT = 'PREFER_ARTIFACT_PROJECT';
  /**
   * Only push to artifact project.
   */
  public const STORAGE_ARTIFACT_PROJECT_ONLY = 'ARTIFACT_PROJECT_ONLY';
  /**
   * Only push to build project.
   */
  public const STORAGE_BUILD_PROJECT_ONLY = 'BUILD_PROJECT_ONLY';
  /**
   * Optional. Provenance push mode.
   *
   * @var string
   */
  public $enabled;
  /**
   * Optional. Provenance region.
   *
   * @var string
   */
  public $region;
  /**
   * Optional. Where provenance is stored.
   *
   * @var string
   */
  public $storage;

  /**
   * Optional. Provenance push mode.
   *
   * Accepted values: ENABLED_UNSPECIFIED, REQUIRED, OPTIMISTIC, DISABLED
   *
   * @param self::ENABLED_* $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return self::ENABLED_*
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. Provenance region.
   *
   * Accepted values: REGION_UNSPECIFIED, GLOBAL
   *
   * @param self::REGION_* $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return self::REGION_*
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Optional. Where provenance is stored.
   *
   * Accepted values: STORAGE_UNSPECIFIED, PREFER_ARTIFACT_PROJECT,
   * ARTIFACT_PROJECT_ONLY, BUILD_PROJECT_ONLY
   *
   * @param self::STORAGE_* $storage
   */
  public function setStorage($storage)
  {
    $this->storage = $storage;
  }
  /**
   * @return self::STORAGE_*
   */
  public function getStorage()
  {
    return $this->storage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Provenance::class, 'Google_Service_CloudBuild_Provenance');
