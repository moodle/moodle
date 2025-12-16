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

namespace Google\Service\Testing;

class AndroidVersion extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * The API level for this Android version. Examples: 18, 19.
   *
   * @var int
   */
  public $apiLevel;
  /**
   * The code name for this Android version. Examples: "JellyBean", "KitKat".
   *
   * @var string
   */
  public $codeName;
  protected $distributionType = Distribution::class;
  protected $distributionDataType = '';
  /**
   * An opaque id for this Android version. Use this id to invoke the
   * TestExecutionService.
   *
   * @var string
   */
  public $id;
  protected $releaseDateType = Date::class;
  protected $releaseDateDataType = '';
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @var string[]
   */
  public $tags;
  /**
   * A string representing this version of the Android OS. Examples: "4.3",
   * "4.4".
   *
   * @var string
   */
  public $versionString;

  /**
   * The API level for this Android version. Examples: 18, 19.
   *
   * @param int $apiLevel
   */
  public function setApiLevel($apiLevel)
  {
    $this->apiLevel = $apiLevel;
  }
  /**
   * @return int
   */
  public function getApiLevel()
  {
    return $this->apiLevel;
  }
  /**
   * The code name for this Android version. Examples: "JellyBean", "KitKat".
   *
   * @param string $codeName
   */
  public function setCodeName($codeName)
  {
    $this->codeName = $codeName;
  }
  /**
   * @return string
   */
  public function getCodeName()
  {
    return $this->codeName;
  }
  /**
   * Market share for this version.
   *
   * @param Distribution $distribution
   */
  public function setDistribution(Distribution $distribution)
  {
    $this->distribution = $distribution;
  }
  /**
   * @return Distribution
   */
  public function getDistribution()
  {
    return $this->distribution;
  }
  /**
   * An opaque id for this Android version. Use this id to invoke the
   * TestExecutionService.
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
   * The date this Android version became available in the market.
   *
   * @param Date $releaseDate
   */
  public function setReleaseDate(Date $releaseDate)
  {
    $this->releaseDate = $releaseDate;
  }
  /**
   * @return Date
   */
  public function getReleaseDate()
  {
    return $this->releaseDate;
  }
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * A string representing this version of the Android OS. Examples: "4.3",
   * "4.4".
   *
   * @param string $versionString
   */
  public function setVersionString($versionString)
  {
    $this->versionString = $versionString;
  }
  /**
   * @return string
   */
  public function getVersionString()
  {
    return $this->versionString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidVersion::class, 'Google_Service_Testing_AndroidVersion');
