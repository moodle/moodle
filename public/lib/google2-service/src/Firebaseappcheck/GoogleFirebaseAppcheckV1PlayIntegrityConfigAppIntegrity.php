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

class GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity extends \Google\Model
{
  /**
   * Specifies whether your running app is allowed to have the
   * `UNRECOGNIZED_VERSION` [app recognition verdict](https://developer.android.
   * com/google/play/integrity/verdicts#application-integrity-field). Note that
   * the app recognition verdict `PLAY_RECOGNIZED` is a strong, comprehensive
   * integrity signal that takes into account various other signals, including
   * conditional and optional device integrity responses that you have opted
   * into. If your app is published off-Play, this field should be set to `true`
   * to allow instances of your app installed from off-Play sources to function.
   * If set to `false`, only `PLAY_RECOGNIZED` verdicts are allowed, and both
   * `UNRECOGNIZED_VERSION` and `UNEVALUATED` will be rejected. If set to
   * `true`, any app recognition verdict is allowed. The default value is
   * `false`.
   *
   * @var bool
   */
  public $allowUnrecognizedVersion;

  /**
   * Specifies whether your running app is allowed to have the
   * `UNRECOGNIZED_VERSION` [app recognition verdict](https://developer.android.
   * com/google/play/integrity/verdicts#application-integrity-field). Note that
   * the app recognition verdict `PLAY_RECOGNIZED` is a strong, comprehensive
   * integrity signal that takes into account various other signals, including
   * conditional and optional device integrity responses that you have opted
   * into. If your app is published off-Play, this field should be set to `true`
   * to allow instances of your app installed from off-Play sources to function.
   * If set to `false`, only `PLAY_RECOGNIZED` verdicts are allowed, and both
   * `UNRECOGNIZED_VERSION` and `UNEVALUATED` will be rejected. If set to
   * `true`, any app recognition verdict is allowed. The default value is
   * `false`.
   *
   * @param bool $allowUnrecognizedVersion
   */
  public function setAllowUnrecognizedVersion($allowUnrecognizedVersion)
  {
    $this->allowUnrecognizedVersion = $allowUnrecognizedVersion;
  }
  /**
   * @return bool
   */
  public function getAllowUnrecognizedVersion()
  {
    return $this->allowUnrecognizedVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1PlayIntegrityConfigAppIntegrity');
