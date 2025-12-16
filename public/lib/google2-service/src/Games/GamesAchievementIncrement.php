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

class GamesAchievementIncrement extends \Google\Model
{
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#GamesAchievementIncrement`.
   *
   * @var string
   */
  public $kind;
  /**
   * The requestId associated with an increment to an achievement.
   *
   * @var string
   */
  public $requestId;
  /**
   * The number of steps to be incremented.
   *
   * @var int
   */
  public $steps;

  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#GamesAchievementIncrement`.
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
   * The requestId associated with an increment to an achievement.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * The number of steps to be incremented.
   *
   * @param int $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return int
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GamesAchievementIncrement::class, 'Google_Service_Games_GamesAchievementIncrement');
