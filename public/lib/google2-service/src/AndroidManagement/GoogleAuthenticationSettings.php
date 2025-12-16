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

namespace Google\Service\AndroidManagement;

class GoogleAuthenticationSettings extends \Google\Model
{
  /**
   * This value is not used.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_GOOGLE_AUTHENTICATION_REQUIRED_UNSPECIFIED = 'GOOGLE_AUTHENTICATION_REQUIRED_UNSPECIFIED';
  /**
   * Google authentication is not required.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_NOT_REQUIRED = 'NOT_REQUIRED';
  /**
   * User is required to be successfully authenticated by Google.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_REQUIRED = 'REQUIRED';
  /**
   * Output only. Whether users need to be authenticated by Google during the
   * enrollment process. IT admin can specify if Google authentication is
   * enabled for the enterprise for knowledge worker devices. This value can be
   * set only via the Google Admin Console. Google authentication can be used
   * with signin_url In the case where Google authentication is required and a
   * signin_url is specified, Google authentication will be launched before
   * signin_url.
   *
   * @var string
   */
  public $googleAuthenticationRequired;

  /**
   * Output only. Whether users need to be authenticated by Google during the
   * enrollment process. IT admin can specify if Google authentication is
   * enabled for the enterprise for knowledge worker devices. This value can be
   * set only via the Google Admin Console. Google authentication can be used
   * with signin_url In the case where Google authentication is required and a
   * signin_url is specified, Google authentication will be launched before
   * signin_url.
   *
   * Accepted values: GOOGLE_AUTHENTICATION_REQUIRED_UNSPECIFIED, NOT_REQUIRED,
   * REQUIRED
   *
   * @param self::GOOGLE_AUTHENTICATION_REQUIRED_* $googleAuthenticationRequired
   */
  public function setGoogleAuthenticationRequired($googleAuthenticationRequired)
  {
    $this->googleAuthenticationRequired = $googleAuthenticationRequired;
  }
  /**
   * @return self::GOOGLE_AUTHENTICATION_REQUIRED_*
   */
  public function getGoogleAuthenticationRequired()
  {
    return $this->googleAuthenticationRequired;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAuthenticationSettings::class, 'Google_Service_AndroidManagement_GoogleAuthenticationSettings');
