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

class AchievementUpdateRequest extends \Google\Model
{
  /**
   * Achievement is revealed.
   */
  public const UPDATE_TYPE_REVEAL = 'REVEAL';
  /**
   * Achievement is unlocked.
   */
  public const UPDATE_TYPE_UNLOCK = 'UNLOCK';
  /**
   * Achievement is incremented.
   */
  public const UPDATE_TYPE_INCREMENT = 'INCREMENT';
  /**
   * Achievement progress is set to at least the passed value.
   */
  public const UPDATE_TYPE_SET_STEPS_AT_LEAST = 'SET_STEPS_AT_LEAST';
  /**
   * The achievement this update is being applied to.
   *
   * @var string
   */
  public $achievementId;
  protected $incrementPayloadType = GamesAchievementIncrement::class;
  protected $incrementPayloadDataType = '';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUpdateRequest`.
   *
   * @var string
   */
  public $kind;
  protected $setStepsAtLeastPayloadType = GamesAchievementSetStepsAtLeast::class;
  protected $setStepsAtLeastPayloadDataType = '';
  /**
   * The type of update being applied.
   *
   * @var string
   */
  public $updateType;

  /**
   * The achievement this update is being applied to.
   *
   * @param string $achievementId
   */
  public function setAchievementId($achievementId)
  {
    $this->achievementId = $achievementId;
  }
  /**
   * @return string
   */
  public function getAchievementId()
  {
    return $this->achievementId;
  }
  /**
   * The payload if an update of type `INCREMENT` was requested for the
   * achievement.
   *
   * @param GamesAchievementIncrement $incrementPayload
   */
  public function setIncrementPayload(GamesAchievementIncrement $incrementPayload)
  {
    $this->incrementPayload = $incrementPayload;
  }
  /**
   * @return GamesAchievementIncrement
   */
  public function getIncrementPayload()
  {
    return $this->incrementPayload;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#achievementUpdateRequest`.
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
   * The payload if an update of type `SET_STEPS_AT_LEAST` was requested for the
   * achievement.
   *
   * @param GamesAchievementSetStepsAtLeast $setStepsAtLeastPayload
   */
  public function setSetStepsAtLeastPayload(GamesAchievementSetStepsAtLeast $setStepsAtLeastPayload)
  {
    $this->setStepsAtLeastPayload = $setStepsAtLeastPayload;
  }
  /**
   * @return GamesAchievementSetStepsAtLeast
   */
  public function getSetStepsAtLeastPayload()
  {
    return $this->setStepsAtLeastPayload;
  }
  /**
   * The type of update being applied.
   *
   * Accepted values: REVEAL, UNLOCK, INCREMENT, SET_STEPS_AT_LEAST
   *
   * @param self::UPDATE_TYPE_* $updateType
   */
  public function setUpdateType($updateType)
  {
    $this->updateType = $updateType;
  }
  /**
   * @return self::UPDATE_TYPE_*
   */
  public function getUpdateType()
  {
    return $this->updateType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementUpdateRequest::class, 'Google_Service_Games_AchievementUpdateRequest');
