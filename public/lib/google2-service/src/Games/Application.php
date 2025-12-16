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

class Application extends \Google\Collection
{
  protected $collection_key = 'instances';
  protected $internal_gapi_mappings = [
        "achievementCount" => "achievement_count",
        "leaderboardCount" => "leaderboard_count",
  ];
  /**
   * The number of achievements visible to the currently authenticated player.
   *
   * @var int
   */
  public $achievementCount;
  protected $assetsType = ImageAsset::class;
  protected $assetsDataType = 'array';
  /**
   * The author of the application.
   *
   * @var string
   */
  public $author;
  protected $categoryType = ApplicationCategory::class;
  protected $categoryDataType = '';
  /**
   * The description of the application.
   *
   * @var string
   */
  public $description;
  /**
   * A list of features that have been enabled for the application.
   *
   * @var string[]
   */
  public $enabledFeatures;
  /**
   * The ID of the application.
   *
   * @var string
   */
  public $id;
  protected $instancesType = Instance::class;
  protected $instancesDataType = 'array';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#application`.
   *
   * @var string
   */
  public $kind;
  /**
   * The last updated timestamp of the application.
   *
   * @var string
   */
  public $lastUpdatedTimestamp;
  /**
   * The number of leaderboards visible to the currently authenticated player.
   *
   * @var int
   */
  public $leaderboardCount;
  /**
   * The name of the application.
   *
   * @var string
   */
  public $name;
  /**
   * A hint to the client UI for what color to use as an app-themed color. The
   * color is given as an RGB triplet (e.g. "E0E0E0").
   *
   * @var string
   */
  public $themeColor;

  /**
   * The number of achievements visible to the currently authenticated player.
   *
   * @param int $achievementCount
   */
  public function setAchievementCount($achievementCount)
  {
    $this->achievementCount = $achievementCount;
  }
  /**
   * @return int
   */
  public function getAchievementCount()
  {
    return $this->achievementCount;
  }
  /**
   * The assets of the application.
   *
   * @param ImageAsset[] $assets
   */
  public function setAssets($assets)
  {
    $this->assets = $assets;
  }
  /**
   * @return ImageAsset[]
   */
  public function getAssets()
  {
    return $this->assets;
  }
  /**
   * The author of the application.
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }
  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * The category of the application.
   *
   * @param ApplicationCategory $category
   */
  public function setCategory(ApplicationCategory $category)
  {
    $this->category = $category;
  }
  /**
   * @return ApplicationCategory
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The description of the application.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A list of features that have been enabled for the application.
   *
   * @param string[] $enabledFeatures
   */
  public function setEnabledFeatures($enabledFeatures)
  {
    $this->enabledFeatures = $enabledFeatures;
  }
  /**
   * @return string[]
   */
  public function getEnabledFeatures()
  {
    return $this->enabledFeatures;
  }
  /**
   * The ID of the application.
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
   * The instances of the application.
   *
   * @param Instance[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return Instance[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#application`.
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
   * The last updated timestamp of the application.
   *
   * @param string $lastUpdatedTimestamp
   */
  public function setLastUpdatedTimestamp($lastUpdatedTimestamp)
  {
    $this->lastUpdatedTimestamp = $lastUpdatedTimestamp;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimestamp()
  {
    return $this->lastUpdatedTimestamp;
  }
  /**
   * The number of leaderboards visible to the currently authenticated player.
   *
   * @param int $leaderboardCount
   */
  public function setLeaderboardCount($leaderboardCount)
  {
    $this->leaderboardCount = $leaderboardCount;
  }
  /**
   * @return int
   */
  public function getLeaderboardCount()
  {
    return $this->leaderboardCount;
  }
  /**
   * The name of the application.
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
   * A hint to the client UI for what color to use as an app-themed color. The
   * color is given as an RGB triplet (e.g. "E0E0E0").
   *
   * @param string $themeColor
   */
  public function setThemeColor($themeColor)
  {
    $this->themeColor = $themeColor;
  }
  /**
   * @return string
   */
  public function getThemeColor()
  {
    return $this->themeColor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_Games_Application');
