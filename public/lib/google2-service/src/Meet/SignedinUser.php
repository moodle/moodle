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

namespace Google\Service\Meet;

class SignedinUser extends \Google\Model
{
  /**
   * Output only. For a personal device, it's the user's first name and last
   * name. For a robot account, it's the administrator-specified device name.
   * For example, "Altostrat Room".
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Unique ID for the user. Interoperable with Admin SDK API and
   * People API. Format: `users/{user}`
   *
   * @var string
   */
  public $user;

  /**
   * Output only. For a personal device, it's the user's first name and last
   * name. For a robot account, it's the administrator-specified device name.
   * For example, "Altostrat Room".
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
   * Output only. Unique ID for the user. Interoperable with Admin SDK API and
   * People API. Format: `users/{user}`
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignedinUser::class, 'Google_Service_Meet_SignedinUser');
