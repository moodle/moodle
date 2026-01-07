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

namespace Google\Service\AndroidPublisher;

class ExternallyHostedApk extends \Google\Collection
{
  protected $collection_key = 'usesPermissions';
  /**
   * The application label.
   *
   * @var string
   */
  public $applicationLabel;
  /**
   * A certificate (or array of certificates if a certificate-chain is used)
   * used to sign this APK, represented as a base64 encoded byte array.
   *
   * @var string[]
   */
  public $certificateBase64s;
  /**
   * The URL at which the APK is hosted. This must be an https URL.
   *
   * @var string
   */
  public $externallyHostedUrl;
  /**
   * The sha1 checksum of this APK, represented as a base64 encoded byte array.
   *
   * @var string
   */
  public $fileSha1Base64;
  /**
   * The sha256 checksum of this APK, represented as a base64 encoded byte
   * array.
   *
   * @var string
   */
  public $fileSha256Base64;
  /**
   * The file size in bytes of this APK.
   *
   * @var string
   */
  public $fileSize;
  /**
   * The icon image from the APK, as a base64 encoded byte array.
   *
   * @var string
   */
  public $iconBase64;
  /**
   * The maximum SDK supported by this APK (optional).
   *
   * @var int
   */
  public $maximumSdk;
  /**
   * The minimum SDK targeted by this APK.
   *
   * @var int
   */
  public $minimumSdk;
  /**
   * The native code environments supported by this APK (optional).
   *
   * @var string[]
   */
  public $nativeCodes;
  /**
   * The package name.
   *
   * @var string
   */
  public $packageName;
  /**
   * The features required by this APK (optional).
   *
   * @var string[]
   */
  public $usesFeatures;
  protected $usesPermissionsType = UsesPermission::class;
  protected $usesPermissionsDataType = 'array';
  /**
   * The version code of this APK.
   *
   * @var int
   */
  public $versionCode;
  /**
   * The version name of this APK.
   *
   * @var string
   */
  public $versionName;

  /**
   * The application label.
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
   * A certificate (or array of certificates if a certificate-chain is used)
   * used to sign this APK, represented as a base64 encoded byte array.
   *
   * @param string[] $certificateBase64s
   */
  public function setCertificateBase64s($certificateBase64s)
  {
    $this->certificateBase64s = $certificateBase64s;
  }
  /**
   * @return string[]
   */
  public function getCertificateBase64s()
  {
    return $this->certificateBase64s;
  }
  /**
   * The URL at which the APK is hosted. This must be an https URL.
   *
   * @param string $externallyHostedUrl
   */
  public function setExternallyHostedUrl($externallyHostedUrl)
  {
    $this->externallyHostedUrl = $externallyHostedUrl;
  }
  /**
   * @return string
   */
  public function getExternallyHostedUrl()
  {
    return $this->externallyHostedUrl;
  }
  /**
   * The sha1 checksum of this APK, represented as a base64 encoded byte array.
   *
   * @param string $fileSha1Base64
   */
  public function setFileSha1Base64($fileSha1Base64)
  {
    $this->fileSha1Base64 = $fileSha1Base64;
  }
  /**
   * @return string
   */
  public function getFileSha1Base64()
  {
    return $this->fileSha1Base64;
  }
  /**
   * The sha256 checksum of this APK, represented as a base64 encoded byte
   * array.
   *
   * @param string $fileSha256Base64
   */
  public function setFileSha256Base64($fileSha256Base64)
  {
    $this->fileSha256Base64 = $fileSha256Base64;
  }
  /**
   * @return string
   */
  public function getFileSha256Base64()
  {
    return $this->fileSha256Base64;
  }
  /**
   * The file size in bytes of this APK.
   *
   * @param string $fileSize
   */
  public function setFileSize($fileSize)
  {
    $this->fileSize = $fileSize;
  }
  /**
   * @return string
   */
  public function getFileSize()
  {
    return $this->fileSize;
  }
  /**
   * The icon image from the APK, as a base64 encoded byte array.
   *
   * @param string $iconBase64
   */
  public function setIconBase64($iconBase64)
  {
    $this->iconBase64 = $iconBase64;
  }
  /**
   * @return string
   */
  public function getIconBase64()
  {
    return $this->iconBase64;
  }
  /**
   * The maximum SDK supported by this APK (optional).
   *
   * @param int $maximumSdk
   */
  public function setMaximumSdk($maximumSdk)
  {
    $this->maximumSdk = $maximumSdk;
  }
  /**
   * @return int
   */
  public function getMaximumSdk()
  {
    return $this->maximumSdk;
  }
  /**
   * The minimum SDK targeted by this APK.
   *
   * @param int $minimumSdk
   */
  public function setMinimumSdk($minimumSdk)
  {
    $this->minimumSdk = $minimumSdk;
  }
  /**
   * @return int
   */
  public function getMinimumSdk()
  {
    return $this->minimumSdk;
  }
  /**
   * The native code environments supported by this APK (optional).
   *
   * @param string[] $nativeCodes
   */
  public function setNativeCodes($nativeCodes)
  {
    $this->nativeCodes = $nativeCodes;
  }
  /**
   * @return string[]
   */
  public function getNativeCodes()
  {
    return $this->nativeCodes;
  }
  /**
   * The package name.
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
   * The features required by this APK (optional).
   *
   * @param string[] $usesFeatures
   */
  public function setUsesFeatures($usesFeatures)
  {
    $this->usesFeatures = $usesFeatures;
  }
  /**
   * @return string[]
   */
  public function getUsesFeatures()
  {
    return $this->usesFeatures;
  }
  /**
   * The permissions requested by this APK.
   *
   * @param UsesPermission[] $usesPermissions
   */
  public function setUsesPermissions($usesPermissions)
  {
    $this->usesPermissions = $usesPermissions;
  }
  /**
   * @return UsesPermission[]
   */
  public function getUsesPermissions()
  {
    return $this->usesPermissions;
  }
  /**
   * The version code of this APK.
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * The version name of this APK.
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
class_alias(ExternallyHostedApk::class, 'Google_Service_AndroidPublisher_ExternallyHostedApk');
