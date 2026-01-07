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

namespace Google\Service\ToolResults;

class ScreenshotCluster extends \Google\Collection
{
  protected $collection_key = 'screens';
  /**
   * A string that describes the activity of every screen in the cluster.
   *
   * @var string
   */
  public $activity;
  /**
   * A unique identifier for the cluster. @OutputOnly
   *
   * @var string
   */
  public $clusterId;
  protected $keyScreenType = Screen::class;
  protected $keyScreenDataType = '';
  protected $screensType = Screen::class;
  protected $screensDataType = 'array';

  /**
   * A string that describes the activity of every screen in the cluster.
   *
   * @param string $activity
   */
  public function setActivity($activity)
  {
    $this->activity = $activity;
  }
  /**
   * @return string
   */
  public function getActivity()
  {
    return $this->activity;
  }
  /**
   * A unique identifier for the cluster. @OutputOnly
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * A singular screen that represents the cluster as a whole. This screen will
   * act as the "cover" of the entire cluster. When users look at the clusters,
   * only the key screen from each cluster will be shown. Which screen is the
   * key screen is determined by the ClusteringAlgorithm
   *
   * @param Screen $keyScreen
   */
  public function setKeyScreen(Screen $keyScreen)
  {
    $this->keyScreen = $keyScreen;
  }
  /**
   * @return Screen
   */
  public function getKeyScreen()
  {
    return $this->keyScreen;
  }
  /**
   * Full list of screens.
   *
   * @param Screen[] $screens
   */
  public function setScreens($screens)
  {
    $this->screens = $screens;
  }
  /**
   * @return Screen[]
   */
  public function getScreens()
  {
    return $this->screens;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScreenshotCluster::class, 'Google_Service_ToolResults_ScreenshotCluster');
