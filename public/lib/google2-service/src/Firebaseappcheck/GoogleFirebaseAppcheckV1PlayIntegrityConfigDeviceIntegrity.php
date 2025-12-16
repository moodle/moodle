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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity extends \Google\Model
{
  /**
   * Default value. Do not specify this value directly. When this default value
   * is detected in a configuration, the `NO_INTEGRITY` default level takes
   * effect.
   */
  public const MIN_DEVICE_RECOGNITION_LEVEL_DEVICE_RECOGNITION_LEVEL_UNSPECIFIED = 'DEVICE_RECOGNITION_LEVEL_UNSPECIFIED';
  /**
   * If this level is set, no explicit device integrity label requirements will
   * be checked. However, because Play Integrity's other features may perform
   * (and require) their own intrinsic device integrity checks, your
   * `app_integrity` and `account_details` settings may still cause some device
   * integrity checks to be performed.
   */
  public const MIN_DEVICE_RECOGNITION_LEVEL_NO_INTEGRITY = 'NO_INTEGRITY';
  /**
   * This level corresponds to the `MEETS_BASIC_INTEGRITY` [optional device
   * recognition label](https://developer.android.com/google/play/integrity/verd
   * icts#optional-device-labels). This value represents the most basic level of
   * device integrity, and is the minimum allowed in App Check's standard
   * implementation of Play Integrity. Warning: Because this is an optional
   * response, you **must** first explicitly [opt in your app in the Play Consol
   * e](https://developer.android.com/google/play/integrity/setup#optional) in
   * order to receive this label. Without this opt-in, **your app may break**
   * for any user whose device is eligible for `MEETS_BASIC_INTEGRITY` but not
   * `MEETS_DEVICE_INTEGRITY`. This API is **not** responsible for any such opt-
   * ins.
   */
  public const MIN_DEVICE_RECOGNITION_LEVEL_MEETS_BASIC_INTEGRITY = 'MEETS_BASIC_INTEGRITY';
  /**
   * This level corresponds to the `MEETS_DEVICE_INTEGRITY` [device recognition
   * verdict](https://developer.android.com/google/play/integrity/verdicts#devic
   * e-integrity-field). Any app integrated with Play Integrity will
   * automatically be eligible to receive this label without any additional
   * action from you. At this level, devices that have the
   * `MEETS_BASIC_INTEGRITY` label but **not** the `MEETS_DEVICE_INTEGRITY`
   * label will be rejected.
   */
  public const MIN_DEVICE_RECOGNITION_LEVEL_MEETS_DEVICE_INTEGRITY = 'MEETS_DEVICE_INTEGRITY';
  /**
   * This level corresponds to the `MEETS_STRONG_INTEGRITY` [optional device
   * recognition label](https://developer.android.com/google/play/integrity/verd
   * icts#optional-device-labels). This value represents the highest level of
   * device integrity. At this level, devices that have the
   * `MEETS_BASIC_INTEGRITY` or `MEETS_DEVICE_INTEGRITY` but **not** the
   * `MEETS_STRONG_INTEGRITY` label will be rejected. Warning: Because this is
   * an optional response, you **must** first explicitly [opt in your app in the
   * Play Console](https://developer.android.com/google/play/integrity/setup#opt
   * ional) in order to receive this label. Without this opt-in, **your app may
   * break** for any user whose device is eligible for `MEETS_STRONG_INTEGRITY`.
   * This API is **not** responsible for any such opt-ins.
   */
  public const MIN_DEVICE_RECOGNITION_LEVEL_MEETS_STRONG_INTEGRITY = 'MEETS_STRONG_INTEGRITY';
  /**
   * Specifies the minimum device integrity level in order for the device to be
   * considered valid. Any device with a device recognition verdict lower than
   * this level will be rejected. If this is unspecified, the default level is
   * `NO_INTEGRITY`.
   *
   * @var string
   */
  public $minDeviceRecognitionLevel;

  /**
   * Specifies the minimum device integrity level in order for the device to be
   * considered valid. Any device with a device recognition verdict lower than
   * this level will be rejected. If this is unspecified, the default level is
   * `NO_INTEGRITY`.
   *
   * Accepted values: DEVICE_RECOGNITION_LEVEL_UNSPECIFIED, NO_INTEGRITY,
   * MEETS_BASIC_INTEGRITY, MEETS_DEVICE_INTEGRITY, MEETS_STRONG_INTEGRITY
   *
   * @param self::MIN_DEVICE_RECOGNITION_LEVEL_* $minDeviceRecognitionLevel
   */
  public function setMinDeviceRecognitionLevel($minDeviceRecognitionLevel)
  {
    $this->minDeviceRecognitionLevel = $minDeviceRecognitionLevel;
  }
  /**
   * @return self::MIN_DEVICE_RECOGNITION_LEVEL_*
   */
  public function getMinDeviceRecognitionLevel()
  {
    return $this->minDeviceRecognitionLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1PlayIntegrityConfigDeviceIntegrity');
