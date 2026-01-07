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

namespace Google\Service\HangoutsChat;

class UserMentionMetadata extends \Google\Model
{
  /**
   * Default value for the enum. Don't use.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Add user to space.
   */
  public const TYPE_ADD = 'ADD';
  /**
   * Mention user in space.
   */
  public const TYPE_MENTION = 'MENTION';
  /**
   * The type of user mention.
   *
   * @var string
   */
  public $type;
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * The type of user mention.
   *
   * Accepted values: TYPE_UNSPECIFIED, ADD, MENTION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The user mentioned.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserMentionMetadata::class, 'Google_Service_HangoutsChat_UserMentionMetadata');
