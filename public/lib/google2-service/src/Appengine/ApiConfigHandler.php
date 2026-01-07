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

namespace Google\Service\Appengine;

class ApiConfigHandler extends \Google\Model
{
  /**
   * Not specified. AUTH_FAIL_ACTION_REDIRECT is assumed.
   */
  public const AUTH_FAIL_ACTION_AUTH_FAIL_ACTION_UNSPECIFIED = 'AUTH_FAIL_ACTION_UNSPECIFIED';
  /**
   * Redirects user to "accounts.google.com". The user is redirected back to the
   * application URL after signing in or creating an account.
   */
  public const AUTH_FAIL_ACTION_AUTH_FAIL_ACTION_REDIRECT = 'AUTH_FAIL_ACTION_REDIRECT';
  /**
   * Rejects request with a 401 HTTP status code and an error message.
   */
  public const AUTH_FAIL_ACTION_AUTH_FAIL_ACTION_UNAUTHORIZED = 'AUTH_FAIL_ACTION_UNAUTHORIZED';
  /**
   * Not specified. LOGIN_OPTIONAL is assumed.
   */
  public const LOGIN_LOGIN_UNSPECIFIED = 'LOGIN_UNSPECIFIED';
  /**
   * Does not require that the user is signed in.
   */
  public const LOGIN_LOGIN_OPTIONAL = 'LOGIN_OPTIONAL';
  /**
   * If the user is not signed in, the auth_fail_action is taken. In addition,
   * if the user is not an administrator for the application, they are given an
   * error message regardless of auth_fail_action. If the user is an
   * administrator, the handler proceeds.
   */
  public const LOGIN_LOGIN_ADMIN = 'LOGIN_ADMIN';
  /**
   * If the user has signed in, the handler proceeds normally. Otherwise, the
   * auth_fail_action is taken.
   */
  public const LOGIN_LOGIN_REQUIRED = 'LOGIN_REQUIRED';
  /**
   * Not specified.
   */
  public const SECURITY_LEVEL_SECURE_UNSPECIFIED = 'SECURE_UNSPECIFIED';
  /**
   * Both HTTP and HTTPS requests with URLs that match the handler succeed
   * without redirects. The application can examine the request to determine
   * which protocol was used, and respond accordingly.
   */
  public const SECURITY_LEVEL_SECURE_DEFAULT = 'SECURE_DEFAULT';
  /**
   * Requests for a URL that match this handler that use HTTPS are automatically
   * redirected to the HTTP equivalent URL.
   */
  public const SECURITY_LEVEL_SECURE_NEVER = 'SECURE_NEVER';
  /**
   * Both HTTP and HTTPS requests with URLs that match the handler succeed
   * without redirects. The application can examine the request to determine
   * which protocol was used and respond accordingly.
   */
  public const SECURITY_LEVEL_SECURE_OPTIONAL = 'SECURE_OPTIONAL';
  /**
   * Requests for a URL that match this handler that do not use HTTPS are
   * automatically redirected to the HTTPS URL with the same path. Query
   * parameters are reserved for the redirect.
   */
  public const SECURITY_LEVEL_SECURE_ALWAYS = 'SECURE_ALWAYS';
  /**
   * Action to take when users access resources that require authentication.
   * Defaults to redirect.
   *
   * @var string
   */
  public $authFailAction;
  /**
   * Level of login required to access this resource. Defaults to optional.
   *
   * @var string
   */
  public $login;
  /**
   * Path to the script from the application root directory.
   *
   * @var string
   */
  public $script;
  /**
   * Security (HTTPS) enforcement for this URL.
   *
   * @var string
   */
  public $securityLevel;
  /**
   * URL to serve the endpoint at.
   *
   * @var string
   */
  public $url;

  /**
   * Action to take when users access resources that require authentication.
   * Defaults to redirect.
   *
   * Accepted values: AUTH_FAIL_ACTION_UNSPECIFIED, AUTH_FAIL_ACTION_REDIRECT,
   * AUTH_FAIL_ACTION_UNAUTHORIZED
   *
   * @param self::AUTH_FAIL_ACTION_* $authFailAction
   */
  public function setAuthFailAction($authFailAction)
  {
    $this->authFailAction = $authFailAction;
  }
  /**
   * @return self::AUTH_FAIL_ACTION_*
   */
  public function getAuthFailAction()
  {
    return $this->authFailAction;
  }
  /**
   * Level of login required to access this resource. Defaults to optional.
   *
   * Accepted values: LOGIN_UNSPECIFIED, LOGIN_OPTIONAL, LOGIN_ADMIN,
   * LOGIN_REQUIRED
   *
   * @param self::LOGIN_* $login
   */
  public function setLogin($login)
  {
    $this->login = $login;
  }
  /**
   * @return self::LOGIN_*
   */
  public function getLogin()
  {
    return $this->login;
  }
  /**
   * Path to the script from the application root directory.
   *
   * @param string $script
   */
  public function setScript($script)
  {
    $this->script = $script;
  }
  /**
   * @return string
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * Security (HTTPS) enforcement for this URL.
   *
   * Accepted values: SECURE_UNSPECIFIED, SECURE_DEFAULT, SECURE_NEVER,
   * SECURE_OPTIONAL, SECURE_ALWAYS
   *
   * @param self::SECURITY_LEVEL_* $securityLevel
   */
  public function setSecurityLevel($securityLevel)
  {
    $this->securityLevel = $securityLevel;
  }
  /**
   * @return self::SECURITY_LEVEL_*
   */
  public function getSecurityLevel()
  {
    return $this->securityLevel;
  }
  /**
   * URL to serve the endpoint at.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApiConfigHandler::class, 'Google_Service_Appengine_ApiConfigHandler');
