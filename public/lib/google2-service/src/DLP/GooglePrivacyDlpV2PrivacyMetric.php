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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2PrivacyMetric extends \Google\Model
{
  protected $categoricalStatsConfigType = GooglePrivacyDlpV2CategoricalStatsConfig::class;
  protected $categoricalStatsConfigDataType = '';
  protected $deltaPresenceEstimationConfigType = GooglePrivacyDlpV2DeltaPresenceEstimationConfig::class;
  protected $deltaPresenceEstimationConfigDataType = '';
  protected $kAnonymityConfigType = GooglePrivacyDlpV2KAnonymityConfig::class;
  protected $kAnonymityConfigDataType = '';
  protected $kMapEstimationConfigType = GooglePrivacyDlpV2KMapEstimationConfig::class;
  protected $kMapEstimationConfigDataType = '';
  protected $lDiversityConfigType = GooglePrivacyDlpV2LDiversityConfig::class;
  protected $lDiversityConfigDataType = '';
  protected $numericalStatsConfigType = GooglePrivacyDlpV2NumericalStatsConfig::class;
  protected $numericalStatsConfigDataType = '';

  /**
   * Categorical stats
   *
   * @param GooglePrivacyDlpV2CategoricalStatsConfig $categoricalStatsConfig
   */
  public function setCategoricalStatsConfig(GooglePrivacyDlpV2CategoricalStatsConfig $categoricalStatsConfig)
  {
    $this->categoricalStatsConfig = $categoricalStatsConfig;
  }
  /**
   * @return GooglePrivacyDlpV2CategoricalStatsConfig
   */
  public function getCategoricalStatsConfig()
  {
    return $this->categoricalStatsConfig;
  }
  /**
   * delta-presence
   *
   * @param GooglePrivacyDlpV2DeltaPresenceEstimationConfig $deltaPresenceEstimationConfig
   */
  public function setDeltaPresenceEstimationConfig(GooglePrivacyDlpV2DeltaPresenceEstimationConfig $deltaPresenceEstimationConfig)
  {
    $this->deltaPresenceEstimationConfig = $deltaPresenceEstimationConfig;
  }
  /**
   * @return GooglePrivacyDlpV2DeltaPresenceEstimationConfig
   */
  public function getDeltaPresenceEstimationConfig()
  {
    return $this->deltaPresenceEstimationConfig;
  }
  /**
   * K-anonymity
   *
   * @param GooglePrivacyDlpV2KAnonymityConfig $kAnonymityConfig
   */
  public function setKAnonymityConfig(GooglePrivacyDlpV2KAnonymityConfig $kAnonymityConfig)
  {
    $this->kAnonymityConfig = $kAnonymityConfig;
  }
  /**
   * @return GooglePrivacyDlpV2KAnonymityConfig
   */
  public function getKAnonymityConfig()
  {
    return $this->kAnonymityConfig;
  }
  /**
   * k-map
   *
   * @param GooglePrivacyDlpV2KMapEstimationConfig $kMapEstimationConfig
   */
  public function setKMapEstimationConfig(GooglePrivacyDlpV2KMapEstimationConfig $kMapEstimationConfig)
  {
    $this->kMapEstimationConfig = $kMapEstimationConfig;
  }
  /**
   * @return GooglePrivacyDlpV2KMapEstimationConfig
   */
  public function getKMapEstimationConfig()
  {
    return $this->kMapEstimationConfig;
  }
  /**
   * l-diversity
   *
   * @param GooglePrivacyDlpV2LDiversityConfig $lDiversityConfig
   */
  public function setLDiversityConfig(GooglePrivacyDlpV2LDiversityConfig $lDiversityConfig)
  {
    $this->lDiversityConfig = $lDiversityConfig;
  }
  /**
   * @return GooglePrivacyDlpV2LDiversityConfig
   */
  public function getLDiversityConfig()
  {
    return $this->lDiversityConfig;
  }
  /**
   * Numerical stats
   *
   * @param GooglePrivacyDlpV2NumericalStatsConfig $numericalStatsConfig
   */
  public function setNumericalStatsConfig(GooglePrivacyDlpV2NumericalStatsConfig $numericalStatsConfig)
  {
    $this->numericalStatsConfig = $numericalStatsConfig;
  }
  /**
   * @return GooglePrivacyDlpV2NumericalStatsConfig
   */
  public function getNumericalStatsConfig()
  {
    return $this->numericalStatsConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2PrivacyMetric::class, 'Google_Service_DLP_GooglePrivacyDlpV2PrivacyMetric');
