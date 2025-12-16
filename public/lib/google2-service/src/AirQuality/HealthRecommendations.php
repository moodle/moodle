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

namespace Google\Service\AirQuality;

class HealthRecommendations extends \Google\Model
{
  /**
   * Sports and other strenuous outdoor activities.
   *
   * @var string
   */
  public $athletes;
  /**
   * Younger populations including children, toddlers, and babies.
   *
   * @var string
   */
  public $children;
  /**
   * Retirees and people older than the general population.
   *
   * @var string
   */
  public $elderly;
  /**
   * No specific sensitivities.
   *
   * @var string
   */
  public $generalPopulation;
  /**
   * Heart and circulatory system diseases.
   *
   * @var string
   */
  public $heartDiseasePopulation;
  /**
   * Respiratory related problems and asthma suffers.
   *
   * @var string
   */
  public $lungDiseasePopulation;
  /**
   * Women at all stages of pregnancy.
   *
   * @var string
   */
  public $pregnantWomen;

  /**
   * Sports and other strenuous outdoor activities.
   *
   * @param string $athletes
   */
  public function setAthletes($athletes)
  {
    $this->athletes = $athletes;
  }
  /**
   * @return string
   */
  public function getAthletes()
  {
    return $this->athletes;
  }
  /**
   * Younger populations including children, toddlers, and babies.
   *
   * @param string $children
   */
  public function setChildren($children)
  {
    $this->children = $children;
  }
  /**
   * @return string
   */
  public function getChildren()
  {
    return $this->children;
  }
  /**
   * Retirees and people older than the general population.
   *
   * @param string $elderly
   */
  public function setElderly($elderly)
  {
    $this->elderly = $elderly;
  }
  /**
   * @return string
   */
  public function getElderly()
  {
    return $this->elderly;
  }
  /**
   * No specific sensitivities.
   *
   * @param string $generalPopulation
   */
  public function setGeneralPopulation($generalPopulation)
  {
    $this->generalPopulation = $generalPopulation;
  }
  /**
   * @return string
   */
  public function getGeneralPopulation()
  {
    return $this->generalPopulation;
  }
  /**
   * Heart and circulatory system diseases.
   *
   * @param string $heartDiseasePopulation
   */
  public function setHeartDiseasePopulation($heartDiseasePopulation)
  {
    $this->heartDiseasePopulation = $heartDiseasePopulation;
  }
  /**
   * @return string
   */
  public function getHeartDiseasePopulation()
  {
    return $this->heartDiseasePopulation;
  }
  /**
   * Respiratory related problems and asthma suffers.
   *
   * @param string $lungDiseasePopulation
   */
  public function setLungDiseasePopulation($lungDiseasePopulation)
  {
    $this->lungDiseasePopulation = $lungDiseasePopulation;
  }
  /**
   * @return string
   */
  public function getLungDiseasePopulation()
  {
    return $this->lungDiseasePopulation;
  }
  /**
   * Women at all stages of pregnancy.
   *
   * @param string $pregnantWomen
   */
  public function setPregnantWomen($pregnantWomen)
  {
    $this->pregnantWomen = $pregnantWomen;
  }
  /**
   * @return string
   */
  public function getPregnantWomen()
  {
    return $this->pregnantWomen;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthRecommendations::class, 'Google_Service_AirQuality_HealthRecommendations');
