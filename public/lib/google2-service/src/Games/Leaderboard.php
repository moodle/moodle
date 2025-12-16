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

namespace Google\Service\Games;

class Leaderboard extends \Google\Model
{
  /**
   * Larger values are better; scores are sorted in descending order
   */
  public const ORDER_LARGER_IS_BETTER = 'LARGER_IS_BETTER';
  /**
   * Smaller values are better; scores are sorted in ascending order
   */
  public const ORDER_SMALLER_IS_BETTER = 'SMALLER_IS_BETTER';
  /**
   * The icon for the leaderboard.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * The leaderboard ID.
   *
   * @var string
   */
  public $id;
  /**
   * Indicates whether the icon image being returned is a default image, or is
   * game-provided.
   *
   * @var bool
   */
  public $isIconUrlDefault;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboard`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the leaderboard.
   *
   * @var string
   */
  public $name;
  /**
   * How scores are ordered.
   *
   * @var string
   */
  public $order;

  /**
   * The icon for the leaderboard.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * The leaderboard ID.
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
   * Indicates whether the icon image being returned is a default image, or is
   * game-provided.
   *
   * @param bool $isIconUrlDefault
   */
  public function setIsIconUrlDefault($isIconUrlDefault)
  {
    $this->isIconUrlDefault = $isIconUrlDefault;
  }
  /**
   * @return bool
   */
  public function getIsIconUrlDefault()
  {
    return $this->isIconUrlDefault;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#leaderboard`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of the leaderboard.
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
   * How scores are ordered.
   *
   * Accepted values: LARGER_IS_BETTER, SMALLER_IS_BETTER
   *
   * @param self::ORDER_* $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return self::ORDER_*
   */
  public function getOrder()
  {
    return $this->order;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Leaderboard::class, 'Google_Service_Games_Leaderboard');
