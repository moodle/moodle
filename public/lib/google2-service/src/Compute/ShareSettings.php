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

namespace Google\Service\Compute;

class ShareSettings extends \Google\Model
{
  /**
   * Default value.
   */
  public const SHARE_TYPE_LOCAL = 'LOCAL';
  /**
   * Shared-reservation is open to entire Organization
   */
  public const SHARE_TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Default value. This value is unused.
   */
  public const SHARE_TYPE_SHARE_TYPE_UNSPECIFIED = 'SHARE_TYPE_UNSPECIFIED';
  /**
   * Shared-reservation is open to specific projects
   */
  public const SHARE_TYPE_SPECIFIC_PROJECTS = 'SPECIFIC_PROJECTS';
  protected $projectMapType = ShareSettingsProjectConfig::class;
  protected $projectMapDataType = 'map';
  /**
   * Type of sharing for this shared-reservation
   *
   * @var string
   */
  public $shareType;

  /**
   * A map of project id and project config. This is only valid when
   * share_type's value is SPECIFIC_PROJECTS.
   *
   * @param ShareSettingsProjectConfig[] $projectMap
   */
  public function setProjectMap($projectMap)
  {
    $this->projectMap = $projectMap;
  }
  /**
   * @return ShareSettingsProjectConfig[]
   */
  public function getProjectMap()
  {
    return $this->projectMap;
  }
  /**
   * Type of sharing for this shared-reservation
   *
   * Accepted values: LOCAL, ORGANIZATION, SHARE_TYPE_UNSPECIFIED,
   * SPECIFIC_PROJECTS
   *
   * @param self::SHARE_TYPE_* $shareType
   */
  public function setShareType($shareType)
  {
    $this->shareType = $shareType;
  }
  /**
   * @return self::SHARE_TYPE_*
   */
  public function getShareType()
  {
    return $this->shareType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShareSettings::class, 'Google_Service_Compute_ShareSettings');
