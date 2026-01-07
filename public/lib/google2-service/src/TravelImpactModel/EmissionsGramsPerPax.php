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

namespace Google\Service\TravelImpactModel;

class EmissionsGramsPerPax extends \Google\Model
{
  /**
   * Emissions for one passenger in business class in grams. This field is
   * always computed and populated, regardless of whether the aircraft has
   * business class seats or not.
   *
   * @var int
   */
  public $business;
  /**
   * Emissions for one passenger in economy class in grams. This field is always
   * computed and populated, regardless of whether the aircraft has economy
   * class seats or not.
   *
   * @var int
   */
  public $economy;
  /**
   * Emissions for one passenger in first class in grams. This field is always
   * computed and populated, regardless of whether the aircraft has first class
   * seats or not.
   *
   * @var int
   */
  public $first;
  /**
   * Emissions for one passenger in premium economy class in grams. This field
   * is always computed and populated, regardless of whether the aircraft has
   * premium economy class seats or not.
   *
   * @var int
   */
  public $premiumEconomy;

  /**
   * Emissions for one passenger in business class in grams. This field is
   * always computed and populated, regardless of whether the aircraft has
   * business class seats or not.
   *
   * @param int $business
   */
  public function setBusiness($business)
  {
    $this->business = $business;
  }
  /**
   * @return int
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Emissions for one passenger in economy class in grams. This field is always
   * computed and populated, regardless of whether the aircraft has economy
   * class seats or not.
   *
   * @param int $economy
   */
  public function setEconomy($economy)
  {
    $this->economy = $economy;
  }
  /**
   * @return int
   */
  public function getEconomy()
  {
    return $this->economy;
  }
  /**
   * Emissions for one passenger in first class in grams. This field is always
   * computed and populated, regardless of whether the aircraft has first class
   * seats or not.
   *
   * @param int $first
   */
  public function setFirst($first)
  {
    $this->first = $first;
  }
  /**
   * @return int
   */
  public function getFirst()
  {
    return $this->first;
  }
  /**
   * Emissions for one passenger in premium economy class in grams. This field
   * is always computed and populated, regardless of whether the aircraft has
   * premium economy class seats or not.
   *
   * @param int $premiumEconomy
   */
  public function setPremiumEconomy($premiumEconomy)
  {
    $this->premiumEconomy = $premiumEconomy;
  }
  /**
   * @return int
   */
  public function getPremiumEconomy()
  {
    return $this->premiumEconomy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmissionsGramsPerPax::class, 'Google_Service_TravelImpactModel_EmissionsGramsPerPax');
