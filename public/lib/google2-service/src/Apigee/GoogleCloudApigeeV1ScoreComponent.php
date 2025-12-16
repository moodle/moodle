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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ScoreComponent extends \Google\Collection
{
  protected $collection_key = 'recommendations';
  /**
   * Time when score was calculated.
   *
   * @var string
   */
  public $calculateTime;
  /**
   * Time in the requested time period when data was last captured to compute
   * the score.
   *
   * @var string
   */
  public $dataCaptureTime;
  /**
   * List of paths for next components.
   *
   * @var string[]
   */
  public $drilldownPaths;
  protected $recommendationsType = GoogleCloudApigeeV1ScoreComponentRecommendation::class;
  protected $recommendationsDataType = 'array';
  /**
   * Score for the component.
   *
   * @var int
   */
  public $score;
  /**
   * Path of the component. Example:
   * /org@myorg/envgroup@myenvgroup/proxies/proxy@myproxy
   *
   * @var string
   */
  public $scorePath;

  /**
   * Time when score was calculated.
   *
   * @param string $calculateTime
   */
  public function setCalculateTime($calculateTime)
  {
    $this->calculateTime = $calculateTime;
  }
  /**
   * @return string
   */
  public function getCalculateTime()
  {
    return $this->calculateTime;
  }
  /**
   * Time in the requested time period when data was last captured to compute
   * the score.
   *
   * @param string $dataCaptureTime
   */
  public function setDataCaptureTime($dataCaptureTime)
  {
    $this->dataCaptureTime = $dataCaptureTime;
  }
  /**
   * @return string
   */
  public function getDataCaptureTime()
  {
    return $this->dataCaptureTime;
  }
  /**
   * List of paths for next components.
   *
   * @param string[] $drilldownPaths
   */
  public function setDrilldownPaths($drilldownPaths)
  {
    $this->drilldownPaths = $drilldownPaths;
  }
  /**
   * @return string[]
   */
  public function getDrilldownPaths()
  {
    return $this->drilldownPaths;
  }
  /**
   * List of recommendations to improve API security.
   *
   * @param GoogleCloudApigeeV1ScoreComponentRecommendation[] $recommendations
   */
  public function setRecommendations($recommendations)
  {
    $this->recommendations = $recommendations;
  }
  /**
   * @return GoogleCloudApigeeV1ScoreComponentRecommendation[]
   */
  public function getRecommendations()
  {
    return $this->recommendations;
  }
  /**
   * Score for the component.
   *
   * @param int $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return int
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * Path of the component. Example:
   * /org@myorg/envgroup@myenvgroup/proxies/proxy@myproxy
   *
   * @param string $scorePath
   */
  public function setScorePath($scorePath)
  {
    $this->scorePath = $scorePath;
  }
  /**
   * @return string
   */
  public function getScorePath()
  {
    return $this->scorePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ScoreComponent::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ScoreComponent');
