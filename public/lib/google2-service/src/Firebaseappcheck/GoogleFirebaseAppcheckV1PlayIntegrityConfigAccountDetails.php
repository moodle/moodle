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

class GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails extends \Google\Model
{
  /**
   * Specifies whether the caller must have received the [`LICENSED` verdict](ht
   * tps://developer.android.com/google/play/integrity/verdicts#account-details-
   * field). For additional details about scenarios where your users will
   * receive this `LICENSED` label, see [the default responses
   * table](https://developer.android.com/google/play/integrity/setup#default).
   * If set to `true`, apps without the `LICENSED` app licensing verdict will be
   * rejected. If set to `false`, any app licensing verdict is allowed. The
   * default value is `false`.
   *
   * @var bool
   */
  public $requireLicensed;

  /**
   * Specifies whether the caller must have received the [`LICENSED` verdict](ht
   * tps://developer.android.com/google/play/integrity/verdicts#account-details-
   * field). For additional details about scenarios where your users will
   * receive this `LICENSED` label, see [the default responses
   * table](https://developer.android.com/google/play/integrity/setup#default).
   * If set to `true`, apps without the `LICENSED` app licensing verdict will be
   * rejected. If set to `false`, any app licensing verdict is allowed. The
   * default value is `false`.
   *
   * @param bool $requireLicensed
   */
  public function setRequireLicensed($requireLicensed)
  {
    $this->requireLicensed = $requireLicensed;
  }
  /**
   * @return bool
   */
  public function getRequireLicensed()
  {
    return $this->requireLicensed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1PlayIntegrityConfigAccountDetails');
