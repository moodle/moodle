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

namespace Google\Service\MyBusinessLodging;

class GuestUnitType extends \Google\Collection
{
  protected $collection_key = 'codes';
  /**
   * Required. Unit or room code identifiers for a single GuestUnitType. Each
   * code must be unique within a Lodging instance.
   *
   * @var string[]
   */
  public $codes;
  protected $featuresType = GuestUnitFeatures::class;
  protected $featuresDataType = '';
  /**
   * Required. Short, English label or name of the GuestUnitType. Target <50
   * chars.
   *
   * @var string
   */
  public $label;

  /**
   * Required. Unit or room code identifiers for a single GuestUnitType. Each
   * code must be unique within a Lodging instance.
   *
   * @param string[] $codes
   */
  public function setCodes($codes)
  {
    $this->codes = $codes;
  }
  /**
   * @return string[]
   */
  public function getCodes()
  {
    return $this->codes;
  }
  /**
   * Features and available amenities of the GuestUnitType.
   *
   * @param GuestUnitFeatures $features
   */
  public function setFeatures(GuestUnitFeatures $features)
  {
    $this->features = $features;
  }
  /**
   * @return GuestUnitFeatures
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * Required. Short, English label or name of the GuestUnitType. Target <50
   * chars.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestUnitType::class, 'Google_Service_MyBusinessLodging_GuestUnitType');
