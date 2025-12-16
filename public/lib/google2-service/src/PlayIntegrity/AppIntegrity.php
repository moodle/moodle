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

namespace Google\Service\PlayIntegrity;

class AppIntegrity extends \Google\Collection
{
  /**
   * Play does not have sufficient information to evaluate app integrity
   */
  public const APP_RECOGNITION_VERDICT_UNKNOWN = 'UNKNOWN';
  /**
   * The app and certificate match the versions distributed by Play.
   */
  public const APP_RECOGNITION_VERDICT_PLAY_RECOGNIZED = 'PLAY_RECOGNIZED';
  /**
   * The certificate or package name does not match Google Play records.
   */
  public const APP_RECOGNITION_VERDICT_UNRECOGNIZED_VERSION = 'UNRECOGNIZED_VERSION';
  /**
   * Application integrity was not evaluated since a necessary requirement was
   * missed. For example DeviceIntegrity did not meet the minimum bar.
   */
  public const APP_RECOGNITION_VERDICT_UNEVALUATED = 'UNEVALUATED';
  protected $collection_key = 'certificateSha256Digest';
  /**
   * Required. Details about the app recognition verdict
   *
   * @var string
   */
  public $appRecognitionVerdict;
  /**
   * The SHA256 hash of the requesting app's signing certificates (base64 web-
   * safe encoded). Set iff app_recognition_verdict != UNEVALUATED.
   *
   * @var string[]
   */
  public $certificateSha256Digest;
  /**
   * Package name of the application under attestation. Set iff
   * app_recognition_verdict != UNEVALUATED.
   *
   * @var string
   */
  public $packageName;
  /**
   * Version code of the application. Set iff app_recognition_verdict !=
   * UNEVALUATED.
   *
   * @var string
   */
  public $versionCode;

  /**
   * Required. Details about the app recognition verdict
   *
   * Accepted values: UNKNOWN, PLAY_RECOGNIZED, UNRECOGNIZED_VERSION,
   * UNEVALUATED
   *
   * @param self::APP_RECOGNITION_VERDICT_* $appRecognitionVerdict
   */
  public function setAppRecognitionVerdict($appRecognitionVerdict)
  {
    $this->appRecognitionVerdict = $appRecognitionVerdict;
  }
  /**
   * @return self::APP_RECOGNITION_VERDICT_*
   */
  public function getAppRecognitionVerdict()
  {
    return $this->appRecognitionVerdict;
  }
  /**
   * The SHA256 hash of the requesting app's signing certificates (base64 web-
   * safe encoded). Set iff app_recognition_verdict != UNEVALUATED.
   *
   * @param string[] $certificateSha256Digest
   */
  public function setCertificateSha256Digest($certificateSha256Digest)
  {
    $this->certificateSha256Digest = $certificateSha256Digest;
  }
  /**
   * @return string[]
   */
  public function getCertificateSha256Digest()
  {
    return $this->certificateSha256Digest;
  }
  /**
   * Package name of the application under attestation. Set iff
   * app_recognition_verdict != UNEVALUATED.
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
   * Version code of the application. Set iff app_recognition_verdict !=
   * UNEVALUATED.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppIntegrity::class, 'Google_Service_PlayIntegrity_AppIntegrity');
