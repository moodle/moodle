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

namespace Google\Service\ManufacturerCenter;

class Grocery extends \Google\Collection
{
  protected $collection_key = 'nutritionClaim';
  /**
   * Active ingredients.
   *
   * @var string
   */
  public $activeIngredients;
  /**
   * Alcohol by volume.
   *
   * @var 
   */
  public $alcoholByVolume;
  /**
   * Allergens.
   *
   * @var string
   */
  public $allergens;
  /**
   * Derived nutrition claim.
   *
   * @var string[]
   */
  public $derivedNutritionClaim;
  /**
   * Directions.
   *
   * @var string
   */
  public $directions;
  /**
   * Indications.
   *
   * @var string
   */
  public $indications;
  /**
   * Ingredients.
   *
   * @var string
   */
  public $ingredients;
  /**
   * Nutrition claim.
   *
   * @var string[]
   */
  public $nutritionClaim;
  /**
   * Storage instructions.
   *
   * @var string
   */
  public $storageInstructions;

  /**
   * Active ingredients.
   *
   * @param string $activeIngredients
   */
  public function setActiveIngredients($activeIngredients)
  {
    $this->activeIngredients = $activeIngredients;
  }
  /**
   * @return string
   */
  public function getActiveIngredients()
  {
    return $this->activeIngredients;
  }
  public function setAlcoholByVolume($alcoholByVolume)
  {
    $this->alcoholByVolume = $alcoholByVolume;
  }
  public function getAlcoholByVolume()
  {
    return $this->alcoholByVolume;
  }
  /**
   * Allergens.
   *
   * @param string $allergens
   */
  public function setAllergens($allergens)
  {
    $this->allergens = $allergens;
  }
  /**
   * @return string
   */
  public function getAllergens()
  {
    return $this->allergens;
  }
  /**
   * Derived nutrition claim.
   *
   * @param string[] $derivedNutritionClaim
   */
  public function setDerivedNutritionClaim($derivedNutritionClaim)
  {
    $this->derivedNutritionClaim = $derivedNutritionClaim;
  }
  /**
   * @return string[]
   */
  public function getDerivedNutritionClaim()
  {
    return $this->derivedNutritionClaim;
  }
  /**
   * Directions.
   *
   * @param string $directions
   */
  public function setDirections($directions)
  {
    $this->directions = $directions;
  }
  /**
   * @return string
   */
  public function getDirections()
  {
    return $this->directions;
  }
  /**
   * Indications.
   *
   * @param string $indications
   */
  public function setIndications($indications)
  {
    $this->indications = $indications;
  }
  /**
   * @return string
   */
  public function getIndications()
  {
    return $this->indications;
  }
  /**
   * Ingredients.
   *
   * @param string $ingredients
   */
  public function setIngredients($ingredients)
  {
    $this->ingredients = $ingredients;
  }
  /**
   * @return string
   */
  public function getIngredients()
  {
    return $this->ingredients;
  }
  /**
   * Nutrition claim.
   *
   * @param string[] $nutritionClaim
   */
  public function setNutritionClaim($nutritionClaim)
  {
    $this->nutritionClaim = $nutritionClaim;
  }
  /**
   * @return string[]
   */
  public function getNutritionClaim()
  {
    return $this->nutritionClaim;
  }
  /**
   * Storage instructions.
   *
   * @param string $storageInstructions
   */
  public function setStorageInstructions($storageInstructions)
  {
    $this->storageInstructions = $storageInstructions;
  }
  /**
   * @return string
   */
  public function getStorageInstructions()
  {
    return $this->storageInstructions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Grocery::class, 'Google_Service_ManufacturerCenter_Grocery');
