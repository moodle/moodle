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

class Reaction extends \Google\Model
{
  protected $emojiType = Emoji::class;
  protected $emojiDataType = '';
  /**
   * Identifier. The resource name of the reaction. Format:
   * `spaces/{space}/messages/{message}/reactions/{reaction}`
   *
   * @var string
   */
  public $name;
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * Required. The emoji used in the reaction.
   *
   * @param Emoji $emoji
   */
  public function setEmoji(Emoji $emoji)
  {
    $this->emoji = $emoji;
  }
  /**
   * @return Emoji
   */
  public function getEmoji()
  {
    return $this->emoji;
  }
  /**
   * Identifier. The resource name of the reaction. Format:
   * `spaces/{space}/messages/{message}/reactions/{reaction}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The user who created the reaction.
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
class_alias(Reaction::class, 'Google_Service_HangoutsChat_Reaction');
