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

namespace Google\Service\Dfareporting;

class DynamicProfileVersion extends \Google\Collection
{
  protected $collection_key = 'dynamicProfileFeedSettings';
  protected $dynamicProfileFeedSettingsType = DynamicProfileFeedSettings::class;
  protected $dynamicProfileFeedSettingsDataType = 'array';
  /**
   * Output only. Version ID of this dynamic profile version. This is a read-
   * only, auto-generated field. -1 for draft version, 0+ for published
   * versions.
   *
   * @var string
   */
  public $versionId;

  /**
   * Optional. Associated dynamic feeds and their settings (including dynamic
   * rules) for this dynamic profile version.
   *
   * @param DynamicProfileFeedSettings[] $dynamicProfileFeedSettings
   */
  public function setDynamicProfileFeedSettings($dynamicProfileFeedSettings)
  {
    $this->dynamicProfileFeedSettings = $dynamicProfileFeedSettings;
  }
  /**
   * @return DynamicProfileFeedSettings[]
   */
  public function getDynamicProfileFeedSettings()
  {
    return $this->dynamicProfileFeedSettings;
  }
  /**
   * Output only. Version ID of this dynamic profile version. This is a read-
   * only, auto-generated field. -1 for draft version, 0+ for published
   * versions.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicProfileVersion::class, 'Google_Service_Dfareporting_DynamicProfileVersion');
