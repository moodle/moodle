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

class MetagameConfig extends \Google\Collection
{
  protected $collection_key = 'playerLevels';
  /**
   * Current version of the metagame configuration data. When this data is
   * updated, the version number will be increased by one.
   *
   * @var int
   */
  public $currentVersion;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#metagameConfig`.
   *
   * @var string
   */
  public $kind;
  protected $playerLevelsType = PlayerLevel::class;
  protected $playerLevelsDataType = 'array';

  /**
   * Current version of the metagame configuration data. When this data is
   * updated, the version number will be increased by one.
   *
   * @param int $currentVersion
   */
  public function setCurrentVersion($currentVersion)
  {
    $this->currentVersion = $currentVersion;
  }
  /**
   * @return int
   */
  public function getCurrentVersion()
  {
    return $this->currentVersion;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#metagameConfig`.
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
   * The list of player levels.
   *
   * @param PlayerLevel[] $playerLevels
   */
  public function setPlayerLevels($playerLevels)
  {
    $this->playerLevels = $playerLevels;
  }
  /**
   * @return PlayerLevel[]
   */
  public function getPlayerLevels()
  {
    return $this->playerLevels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetagameConfig::class, 'Google_Service_Games_MetagameConfig');
