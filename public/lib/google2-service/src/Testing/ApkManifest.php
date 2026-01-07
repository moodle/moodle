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

namespace Google\Service\Testing;

class ApkManifest extends \Google\Collection
{
  protected $collection_key = 'usesPermissionTags';
  /**
   * User-readable name for the application.
   *
   * @var string
   */
  public $applicationLabel;
  protected $intentFiltersType = IntentFilter::class;
  protected $intentFiltersDataType = 'array';
  /**
   * Maximum API level on which the application is designed to run.
   *
   * @var int
   */
  public $maxSdkVersion;
  protected $metadataType = Metadata::class;
  protected $metadataDataType = 'array';
  /**
   * Minimum API level required for the application to run.
   *
   * @var int
   */
  public $minSdkVersion;
  /**
   * Full Java-style package name for this application, e.g. "com.example.foo".
   *
   * @var string
   */
  public $packageName;
  protected $servicesType = Service::class;
  protected $servicesDataType = 'array';
  /**
   * Specifies the API Level on which the application is designed to run.
   *
   * @var int
   */
  public $targetSdkVersion;
  protected $usesFeatureType = UsesFeature::class;
  protected $usesFeatureDataType = 'array';
  /**
   * @var string[]
   */
  public $usesPermission;
  protected $usesPermissionTagsType = UsesPermissionTag::class;
  protected $usesPermissionTagsDataType = 'array';
  /**
   * Version number used internally by the app.
   *
   * @var string
   */
  public $versionCode;
  /**
   * Version number shown to users.
   *
   * @var string
   */
  public $versionName;

  /**
   * User-readable name for the application.
   *
   * @param string $applicationLabel
   */
  public function setApplicationLabel($applicationLabel)
  {
    $this->applicationLabel = $applicationLabel;
  }
  /**
   * @return string
   */
  public function getApplicationLabel()
  {
    return $this->applicationLabel;
  }
  /**
   * @param IntentFilter[] $intentFilters
   */
  public function setIntentFilters($intentFilters)
  {
    $this->intentFilters = $intentFilters;
  }
  /**
   * @return IntentFilter[]
   */
  public function getIntentFilters()
  {
    return $this->intentFilters;
  }
  /**
   * Maximum API level on which the application is designed to run.
   *
   * @param int $maxSdkVersion
   */
  public function setMaxSdkVersion($maxSdkVersion)
  {
    $this->maxSdkVersion = $maxSdkVersion;
  }
  /**
   * @return int
   */
  public function getMaxSdkVersion()
  {
    return $this->maxSdkVersion;
  }
  /**
   * Meta-data tags defined in the manifest.
   *
   * @param Metadata[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Minimum API level required for the application to run.
   *
   * @param int $minSdkVersion
   */
  public function setMinSdkVersion($minSdkVersion)
  {
    $this->minSdkVersion = $minSdkVersion;
  }
  /**
   * @return int
   */
  public function getMinSdkVersion()
  {
    return $this->minSdkVersion;
  }
  /**
   * Full Java-style package name for this application, e.g. "com.example.foo".
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Services contained in the tag.
   *
   * @param Service[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return Service[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Specifies the API Level on which the application is designed to run.
   *
   * @param int $targetSdkVersion
   */
  public function setTargetSdkVersion($targetSdkVersion)
  {
    $this->targetSdkVersion = $targetSdkVersion;
  }
  /**
   * @return int
   */
  public function getTargetSdkVersion()
  {
    return $this->targetSdkVersion;
  }
  /**
   * Feature usage tags defined in the manifest.
   *
   * @param UsesFeature[] $usesFeature
   */
  public function setUsesFeature($usesFeature)
  {
    $this->usesFeature = $usesFeature;
  }
  /**
   * @return UsesFeature[]
   */
  public function getUsesFeature()
  {
    return $this->usesFeature;
  }
  /**
   * @param string[] $usesPermission
   */
  public function setUsesPermission($usesPermission)
  {
    $this->usesPermission = $usesPermission;
  }
  /**
   * @return string[]
   */
  public function getUsesPermission()
  {
    return $this->usesPermission;
  }
  /**
   * Permissions declared to be used by the application
   *
   * @param UsesPermissionTag[] $usesPermissionTags
   */
  public function setUsesPermissionTags($usesPermissionTags)
  {
    $this->usesPermissionTags = $usesPermissionTags;
  }
  /**
   * @return UsesPermissionTag[]
   */
  public function getUsesPermissionTags()
  {
    return $this->usesPermissionTags;
  }
  /**
   * Version number used internally by the app.
   *
   * @param string $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return string
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * Version number shown to users.
   *
   * @param string $versionName
   */
  public function setVersionName($versionName)
  {
    $this->versionName = $versionName;
  }
  /**
   * @return string
   */
  public function getVersionName()
  {
    return $this->versionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApkManifest::class, 'Google_Service_Testing_ApkManifest');
