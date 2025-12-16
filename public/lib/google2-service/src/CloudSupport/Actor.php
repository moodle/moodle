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

namespace Google\Service\CloudSupport;

class Actor extends \Google\Model
{
  /**
   * The name to display for the actor. If not provided, it is inferred from
   * credentials supplied during case creation. When an email is provided, a
   * display name must also be provided. This will be obfuscated if the user is
   * a Google Support agent.
   *
   * @var string
   */
  public $displayName;
  /**
   * The email address of the actor. If not provided, it is inferred from the
   * credentials supplied during case creation. When a name is provided, an
   * email must also be provided. If the user is a Google Support agent, this is
   * obfuscated. This field is deprecated. Use `username` instead.
   *
   * @deprecated
   * @var string
   */
  public $email;
  /**
   * Output only. Whether the actor is a Google support actor.
   *
   * @var bool
   */
  public $googleSupport;
  /**
   * Output only. The username of the actor. It may look like an email or other
   * format provided by the identity provider. If not provided, it is inferred
   * from the credentials supplied. When a name is provided, a username must
   * also be provided. If the user is a Google Support agent, this will not be
   * set.
   *
   * @var string
   */
  public $username;

  /**
   * The name to display for the actor. If not provided, it is inferred from
   * credentials supplied during case creation. When an email is provided, a
   * display name must also be provided. This will be obfuscated if the user is
   * a Google Support agent.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The email address of the actor. If not provided, it is inferred from the
   * credentials supplied during case creation. When a name is provided, an
   * email must also be provided. If the user is a Google Support agent, this is
   * obfuscated. This field is deprecated. Use `username` instead.
   *
   * @deprecated
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Output only. Whether the actor is a Google support actor.
   *
   * @param bool $googleSupport
   */
  public function setGoogleSupport($googleSupport)
  {
    $this->googleSupport = $googleSupport;
  }
  /**
   * @return bool
   */
  public function getGoogleSupport()
  {
    return $this->googleSupport;
  }
  /**
   * Output only. The username of the actor. It may look like an email or other
   * format provided by the identity provider. If not provided, it is inferred
   * from the credentials supplied. When a name is provided, a username must
   * also be provided. If the user is a Google Support agent, this will not be
   * set.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Actor::class, 'Google_Service_CloudSupport_Actor');
