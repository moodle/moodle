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

namespace Google\Service\Dataflow;

class SdkVersion extends \Google\Collection
{
  /**
   * Cloud Dataflow is unaware of this version.
   */
  public const SDK_SUPPORT_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * This is a known version of an SDK, and is supported.
   */
  public const SDK_SUPPORT_STATUS_SUPPORTED = 'SUPPORTED';
  /**
   * A newer version of the SDK family exists, and an update is recommended.
   */
  public const SDK_SUPPORT_STATUS_STALE = 'STALE';
  /**
   * This version of the SDK is deprecated and will eventually be unsupported.
   */
  public const SDK_SUPPORT_STATUS_DEPRECATED = 'DEPRECATED';
  /**
   * Support for this SDK version has ended and it should no longer be used.
   */
  public const SDK_SUPPORT_STATUS_UNSUPPORTED = 'UNSUPPORTED';
  protected $collection_key = 'bugs';
  protected $bugsType = SdkBug::class;
  protected $bugsDataType = 'array';
  /**
   * The support status for this SDK version.
   *
   * @var string
   */
  public $sdkSupportStatus;
  /**
   * The version of the SDK used to run the job.
   *
   * @var string
   */
  public $version;
  /**
   * A readable string describing the version of the SDK.
   *
   * @var string
   */
  public $versionDisplayName;

  /**
   * Output only. Known bugs found in this SDK version.
   *
   * @param SdkBug[] $bugs
   */
  public function setBugs($bugs)
  {
    $this->bugs = $bugs;
  }
  /**
   * @return SdkBug[]
   */
  public function getBugs()
  {
    return $this->bugs;
  }
  /**
   * The support status for this SDK version.
   *
   * Accepted values: UNKNOWN, SUPPORTED, STALE, DEPRECATED, UNSUPPORTED
   *
   * @param self::SDK_SUPPORT_STATUS_* $sdkSupportStatus
   */
  public function setSdkSupportStatus($sdkSupportStatus)
  {
    $this->sdkSupportStatus = $sdkSupportStatus;
  }
  /**
   * @return self::SDK_SUPPORT_STATUS_*
   */
  public function getSdkSupportStatus()
  {
    return $this->sdkSupportStatus;
  }
  /**
   * The version of the SDK used to run the job.
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
   * A readable string describing the version of the SDK.
   *
   * @param string $versionDisplayName
   */
  public function setVersionDisplayName($versionDisplayName)
  {
    $this->versionDisplayName = $versionDisplayName;
  }
  /**
   * @return string
   */
  public function getVersionDisplayName()
  {
    return $this->versionDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SdkVersion::class, 'Google_Service_Dataflow_SdkVersion');
