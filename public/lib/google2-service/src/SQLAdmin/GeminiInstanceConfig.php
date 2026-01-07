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

namespace Google\Service\SQLAdmin;

class GeminiInstanceConfig extends \Google\Model
{
  /**
   * Output only. Whether the active query is enabled.
   *
   * @var bool
   */
  public $activeQueryEnabled;
  /**
   * Output only. Whether Gemini is enabled.
   *
   * @var bool
   */
  public $entitled;
  /**
   * Output only. Whether the flag recommender is enabled.
   *
   * @var bool
   */
  public $flagRecommenderEnabled;
  /**
   * Output only. Whether the vacuum management is enabled.
   *
   * @var bool
   */
  public $googleVacuumMgmtEnabled;
  /**
   * Output only. Whether the index advisor is enabled.
   *
   * @var bool
   */
  public $indexAdvisorEnabled;
  /**
   * Output only. Whether canceling the out-of-memory (OOM) session is enabled.
   *
   * @var bool
   */
  public $oomSessionCancelEnabled;

  /**
   * Output only. Whether the active query is enabled.
   *
   * @param bool $activeQueryEnabled
   */
  public function setActiveQueryEnabled($activeQueryEnabled)
  {
    $this->activeQueryEnabled = $activeQueryEnabled;
  }
  /**
   * @return bool
   */
  public function getActiveQueryEnabled()
  {
    return $this->activeQueryEnabled;
  }
  /**
   * Output only. Whether Gemini is enabled.
   *
   * @param bool $entitled
   */
  public function setEntitled($entitled)
  {
    $this->entitled = $entitled;
  }
  /**
   * @return bool
   */
  public function getEntitled()
  {
    return $this->entitled;
  }
  /**
   * Output only. Whether the flag recommender is enabled.
   *
   * @param bool $flagRecommenderEnabled
   */
  public function setFlagRecommenderEnabled($flagRecommenderEnabled)
  {
    $this->flagRecommenderEnabled = $flagRecommenderEnabled;
  }
  /**
   * @return bool
   */
  public function getFlagRecommenderEnabled()
  {
    return $this->flagRecommenderEnabled;
  }
  /**
   * Output only. Whether the vacuum management is enabled.
   *
   * @param bool $googleVacuumMgmtEnabled
   */
  public function setGoogleVacuumMgmtEnabled($googleVacuumMgmtEnabled)
  {
    $this->googleVacuumMgmtEnabled = $googleVacuumMgmtEnabled;
  }
  /**
   * @return bool
   */
  public function getGoogleVacuumMgmtEnabled()
  {
    return $this->googleVacuumMgmtEnabled;
  }
  /**
   * Output only. Whether the index advisor is enabled.
   *
   * @param bool $indexAdvisorEnabled
   */
  public function setIndexAdvisorEnabled($indexAdvisorEnabled)
  {
    $this->indexAdvisorEnabled = $indexAdvisorEnabled;
  }
  /**
   * @return bool
   */
  public function getIndexAdvisorEnabled()
  {
    return $this->indexAdvisorEnabled;
  }
  /**
   * Output only. Whether canceling the out-of-memory (OOM) session is enabled.
   *
   * @param bool $oomSessionCancelEnabled
   */
  public function setOomSessionCancelEnabled($oomSessionCancelEnabled)
  {
    $this->oomSessionCancelEnabled = $oomSessionCancelEnabled;
  }
  /**
   * @return bool
   */
  public function getOomSessionCancelEnabled()
  {
    return $this->oomSessionCancelEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeminiInstanceConfig::class, 'Google_Service_SQLAdmin_GeminiInstanceConfig');
