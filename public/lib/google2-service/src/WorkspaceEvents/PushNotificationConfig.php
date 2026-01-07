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

namespace Google\Service\WorkspaceEvents;

class PushNotificationConfig extends \Google\Model
{
  protected $authenticationType = AuthenticationInfo::class;
  protected $authenticationDataType = '';
  /**
   * A unique identifier (e.g. UUID) for this push notification.
   *
   * @var string
   */
  public $id;
  /**
   * Token unique for this task/session
   *
   * @var string
   */
  public $token;
  /**
   * Url to send the notification too
   *
   * @var string
   */
  public $url;

  /**
   * Information about the authentication to sent with the notification
   *
   * @param AuthenticationInfo $authentication
   */
  public function setAuthentication(AuthenticationInfo $authentication)
  {
    $this->authentication = $authentication;
  }
  /**
   * @return AuthenticationInfo
   */
  public function getAuthentication()
  {
    return $this->authentication;
  }
  /**
   * A unique identifier (e.g. UUID) for this push notification.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Token unique for this task/session
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * Url to send the notification too
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
class_alias(PushNotificationConfig::class, 'Google_Service_WorkspaceEvents_PushNotificationConfig');
