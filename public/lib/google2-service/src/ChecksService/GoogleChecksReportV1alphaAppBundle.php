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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaAppBundle extends \Google\Model
{
  /**
   * Not specified.
   */
  public const RELEASE_TYPE_APP_BUNDLE_RELEASE_TYPE_UNSPECIFIED = 'APP_BUNDLE_RELEASE_TYPE_UNSPECIFIED';
  /**
   * Published production bundle.
   */
  public const RELEASE_TYPE_PUBLIC = 'PUBLIC';
  /**
   * Pre-release bundle.
   */
  public const RELEASE_TYPE_PRE_RELEASE = 'PRE_RELEASE';
  /**
   * Unique id of the bundle. For example: "com.google.Gmail".
   *
   * @var string
   */
  public $bundleId;
  /**
   * Git commit hash or changelist number associated with the release.
   *
   * @var string
   */
  public $codeReferenceId;
  /**
   * Identifies the type of release.
   *
   * @var string
   */
  public $releaseType;
  /**
   * The user-visible version of the bundle such as the Android `versionName` or
   * iOS `CFBundleShortVersionString`. For example: "7.21.1".
   *
   * @var string
   */
  public $version;
  /**
   * The version used throughout the operating system and store to identify the
   * build such as the Android `versionCode` or iOS `CFBundleVersion`.
   *
   * @var string
   */
  public $versionId;

  /**
   * Unique id of the bundle. For example: "com.google.Gmail".
   *
   * @param string $bundleId
   */
  public function setBundleId($bundleId)
  {
    $this->bundleId = $bundleId;
  }
  /**
   * @return string
   */
  public function getBundleId()
  {
    return $this->bundleId;
  }
  /**
   * Git commit hash or changelist number associated with the release.
   *
   * @param string $codeReferenceId
   */
  public function setCodeReferenceId($codeReferenceId)
  {
    $this->codeReferenceId = $codeReferenceId;
  }
  /**
   * @return string
   */
  public function getCodeReferenceId()
  {
    return $this->codeReferenceId;
  }
  /**
   * Identifies the type of release.
   *
   * Accepted values: APP_BUNDLE_RELEASE_TYPE_UNSPECIFIED, PUBLIC, PRE_RELEASE
   *
   * @param self::RELEASE_TYPE_* $releaseType
   */
  public function setReleaseType($releaseType)
  {
    $this->releaseType = $releaseType;
  }
  /**
   * @return self::RELEASE_TYPE_*
   */
  public function getReleaseType()
  {
    return $this->releaseType;
  }
  /**
   * The user-visible version of the bundle such as the Android `versionName` or
   * iOS `CFBundleShortVersionString`. For example: "7.21.1".
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
  /**
   * The version used throughout the operating system and store to identify the
   * build such as the Android `versionCode` or iOS `CFBundleVersion`.
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
class_alias(GoogleChecksReportV1alphaAppBundle::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaAppBundle');
