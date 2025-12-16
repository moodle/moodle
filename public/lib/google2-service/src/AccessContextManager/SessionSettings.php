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

namespace Google\Service\AccessContextManager;

class SessionSettings extends \Google\Model
{
  /**
   * If method is undefined in the API, LOGIN will be used by default.
   */
  public const SESSION_REAUTH_METHOD_SESSION_REAUTH_METHOD_UNSPECIFIED = 'SESSION_REAUTH_METHOD_UNSPECIFIED';
  /**
   * The user will be prompted to perform regular login. Users who are enrolled
   * for two-step verification and haven't chosen "Remember this computer" will
   * be prompted for their second factor.
   */
  public const SESSION_REAUTH_METHOD_LOGIN = 'LOGIN';
  /**
   * The user will be prompted to authenticate using their security key. If no
   * security key has been configured, then authentication will fallback to
   * LOGIN.
   */
  public const SESSION_REAUTH_METHOD_SECURITY_KEY = 'SECURITY_KEY';
  /**
   * The user will be prompted for their password.
   */
  public const SESSION_REAUTH_METHOD_PASSWORD = 'PASSWORD';
  /**
   * Optional. How long a user is allowed to take between actions before a new
   * access token must be issued. Only set for Google Cloud apps.
   *
   * @var string
   */
  public $maxInactivity;
  /**
   * Optional. The session length. Setting this field to zero is equal to
   * disabling session. Also can set infinite session by flipping the enabled
   * bit to false below. If use_oidc_max_age is true, for OIDC apps, the session
   * length will be the minimum of this field and OIDC max_age param.
   *
   * @var string
   */
  public $sessionLength;
  /**
   * Optional. This field enables or disables Google Cloud session length. When
   * false, all fields set above will be disregarded and the session length is
   * basically infinite.
   *
   * @var bool
   */
  public $sessionLengthEnabled;
  /**
   * Optional. Session method when user's Google Cloud session is up.
   *
   * @var string
   */
  public $sessionReauthMethod;
  /**
   * Optional. Only useful for OIDC apps. When false, the OIDC max_age param, if
   * passed in the authentication request will be ignored. When true, the re-
   * auth period will be the minimum of the session_length field and the max_age
   * OIDC param.
   *
   * @var bool
   */
  public $useOidcMaxAge;

  /**
   * Optional. How long a user is allowed to take between actions before a new
   * access token must be issued. Only set for Google Cloud apps.
   *
   * @param string $maxInactivity
   */
  public function setMaxInactivity($maxInactivity)
  {
    $this->maxInactivity = $maxInactivity;
  }
  /**
   * @return string
   */
  public function getMaxInactivity()
  {
    return $this->maxInactivity;
  }
  /**
   * Optional. The session length. Setting this field to zero is equal to
   * disabling session. Also can set infinite session by flipping the enabled
   * bit to false below. If use_oidc_max_age is true, for OIDC apps, the session
   * length will be the minimum of this field and OIDC max_age param.
   *
   * @param string $sessionLength
   */
  public function setSessionLength($sessionLength)
  {
    $this->sessionLength = $sessionLength;
  }
  /**
   * @return string
   */
  public function getSessionLength()
  {
    return $this->sessionLength;
  }
  /**
   * Optional. This field enables or disables Google Cloud session length. When
   * false, all fields set above will be disregarded and the session length is
   * basically infinite.
   *
   * @param bool $sessionLengthEnabled
   */
  public function setSessionLengthEnabled($sessionLengthEnabled)
  {
    $this->sessionLengthEnabled = $sessionLengthEnabled;
  }
  /**
   * @return bool
   */
  public function getSessionLengthEnabled()
  {
    return $this->sessionLengthEnabled;
  }
  /**
   * Optional. Session method when user's Google Cloud session is up.
   *
   * Accepted values: SESSION_REAUTH_METHOD_UNSPECIFIED, LOGIN, SECURITY_KEY,
   * PASSWORD
   *
   * @param self::SESSION_REAUTH_METHOD_* $sessionReauthMethod
   */
  public function setSessionReauthMethod($sessionReauthMethod)
  {
    $this->sessionReauthMethod = $sessionReauthMethod;
  }
  /**
   * @return self::SESSION_REAUTH_METHOD_*
   */
  public function getSessionReauthMethod()
  {
    return $this->sessionReauthMethod;
  }
  /**
   * Optional. Only useful for OIDC apps. When false, the OIDC max_age param, if
   * passed in the authentication request will be ignored. When true, the re-
   * auth period will be the minimum of the session_length field and the max_age
   * OIDC param.
   *
   * @param bool $useOidcMaxAge
   */
  public function setUseOidcMaxAge($useOidcMaxAge)
  {
    $this->useOidcMaxAge = $useOidcMaxAge;
  }
  /**
   * @return bool
   */
  public function getUseOidcMaxAge()
  {
    return $this->useOidcMaxAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionSettings::class, 'Google_Service_AccessContextManager_SessionSettings');
