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

namespace Google\Service\GamesConfiguration;

class AchievementConfiguration extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const ACHIEVEMENT_TYPE_ACHIEVEMENT_TYPE_UNSPECIFIED = 'ACHIEVEMENT_TYPE_UNSPECIFIED';
  /**
   * Achievement is either locked or unlocked.
   */
  public const ACHIEVEMENT_TYPE_STANDARD = 'STANDARD';
  /**
   * Achievement is incremental.
   */
  public const ACHIEVEMENT_TYPE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Default value. This value is unused.
   */
  public const INITIAL_STATE_INITIAL_STATE_UNSPECIFIED = 'INITIAL_STATE_UNSPECIFIED';
  /**
   * Achievement is hidden.
   */
  public const INITIAL_STATE_HIDDEN = 'HIDDEN';
  /**
   * Achievement is revealed.
   */
  public const INITIAL_STATE_REVEALED = 'REVEALED';
  /**
   * The type of the achievement.
   *
   * @var string
   */
  public $achievementType;
  protected $draftType = AchievementConfigurationDetail::class;
  protected $draftDataType = '';
  /**
   * The ID of the achievement.
   *
   * @var string
   */
  public $id;
  /**
   * The initial state of the achievement.
   *
   * @var string
   */
  public $initialState;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#achievementConfiguration`.
   *
   * @var string
   */
  public $kind;
  protected $publishedType = AchievementConfigurationDetail::class;
  protected $publishedDataType = '';
  /**
   * Steps to unlock. Only applicable to incremental achievements.
   *
   * @var int
   */
  public $stepsToUnlock;
  /**
   * The token for this resource.
   *
   * @var string
   */
  public $token;

  /**
   * The type of the achievement.
   *
   * Accepted values: ACHIEVEMENT_TYPE_UNSPECIFIED, STANDARD, INCREMENTAL
   *
   * @param self::ACHIEVEMENT_TYPE_* $achievementType
   */
  public function setAchievementType($achievementType)
  {
    $this->achievementType = $achievementType;
  }
  /**
   * @return self::ACHIEVEMENT_TYPE_*
   */
  public function getAchievementType()
  {
    return $this->achievementType;
  }
  /**
   * The draft data of the achievement.
   *
   * @param AchievementConfigurationDetail $draft
   */
  public function setDraft(AchievementConfigurationDetail $draft)
  {
    $this->draft = $draft;
  }
  /**
   * @return AchievementConfigurationDetail
   */
  public function getDraft()
  {
    return $this->draft;
  }
  /**
   * The ID of the achievement.
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
   * The initial state of the achievement.
   *
   * Accepted values: INITIAL_STATE_UNSPECIFIED, HIDDEN, REVEALED
   *
   * @param self::INITIAL_STATE_* $initialState
   */
  public function setInitialState($initialState)
  {
    $this->initialState = $initialState;
  }
  /**
   * @return self::INITIAL_STATE_*
   */
  public function getInitialState()
  {
    return $this->initialState;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `gamesConfiguration#achievementConfiguration`.
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
   * The read-only published data of the achievement.
   *
   * @param AchievementConfigurationDetail $published
   */
  public function setPublished(AchievementConfigurationDetail $published)
  {
    $this->published = $published;
  }
  /**
   * @return AchievementConfigurationDetail
   */
  public function getPublished()
  {
    return $this->published;
  }
  /**
   * Steps to unlock. Only applicable to incremental achievements.
   *
   * @param int $stepsToUnlock
   */
  public function setStepsToUnlock($stepsToUnlock)
  {
    $this->stepsToUnlock = $stepsToUnlock;
  }
  /**
   * @return int
   */
  public function getStepsToUnlock()
  {
    return $this->stepsToUnlock;
  }
  /**
   * The token for this resource.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AchievementConfiguration::class, 'Google_Service_GamesConfiguration_AchievementConfiguration');
