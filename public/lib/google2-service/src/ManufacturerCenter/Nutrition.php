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

class Nutrition extends \Google\Collection
{
  protected $collection_key = 'voluntaryNutritionFact';
  protected $addedSugarsType = FloatUnit::class;
  protected $addedSugarsDataType = '';
  /**
   * Added sugars daily percentage.
   *
   * @var 
   */
  public $addedSugarsDailyPercentage;
  protected $calciumType = FloatUnit::class;
  protected $calciumDataType = '';
  /**
   * Calcium daily percentage.
   *
   * @var 
   */
  public $calciumDailyPercentage;
  protected $cholesterolType = FloatUnit::class;
  protected $cholesterolDataType = '';
  /**
   * Cholesterol daily percentage.
   *
   * @var 
   */
  public $cholesterolDailyPercentage;
  protected $dietaryFiberType = FloatUnit::class;
  protected $dietaryFiberDataType = '';
  /**
   * Dietary fiber daily percentage.
   *
   * @var 
   */
  public $dietaryFiberDailyPercentage;
  protected $energyType = FloatUnit::class;
  protected $energyDataType = '';
  protected $energyFromFatType = FloatUnit::class;
  protected $energyFromFatDataType = '';
  /**
   * Folate daily percentage.
   *
   * @var 
   */
  public $folateDailyPercentage;
  protected $folateFolicAcidType = FloatUnit::class;
  protected $folateFolicAcidDataType = '';
  /**
   * Folate mcg DFE.
   *
   * @var 
   */
  public $folateMcgDfe;
  protected $ironType = FloatUnit::class;
  protected $ironDataType = '';
  /**
   * Iron daily percentage.
   *
   * @var 
   */
  public $ironDailyPercentage;
  protected $monounsaturatedFatType = FloatUnit::class;
  protected $monounsaturatedFatDataType = '';
  /**
   * Nutrition fact measure.
   *
   * @var string
   */
  public $nutritionFactMeasure;
  protected $polyolsType = FloatUnit::class;
  protected $polyolsDataType = '';
  protected $polyunsaturatedFatType = FloatUnit::class;
  protected $polyunsaturatedFatDataType = '';
  protected $potassiumType = FloatUnit::class;
  protected $potassiumDataType = '';
  /**
   * Potassium daily percentage.
   *
   * @var 
   */
  public $potassiumDailyPercentage;
  /**
   * Prepared size description.
   *
   * @var string
   */
  public $preparedSizeDescription;
  protected $proteinType = FloatUnit::class;
  protected $proteinDataType = '';
  /**
   * Protein daily percentage.
   *
   * @var 
   */
  public $proteinDailyPercentage;
  protected $saturatedFatType = FloatUnit::class;
  protected $saturatedFatDataType = '';
  /**
   * Saturated fat daily percentage.
   *
   * @var 
   */
  public $saturatedFatDailyPercentage;
  /**
   * Food Serving Size. Serving size description.
   *
   * @var string
   */
  public $servingSizeDescription;
  protected $servingSizeMeasureType = FloatUnit::class;
  protected $servingSizeMeasureDataType = '';
  /**
   * Servings per container.
   *
   * @var string
   */
  public $servingsPerContainer;
  protected $sodiumType = FloatUnit::class;
  protected $sodiumDataType = '';
  /**
   * Sodium daily percentage.
   *
   * @var 
   */
  public $sodiumDailyPercentage;
  protected $starchType = FloatUnit::class;
  protected $starchDataType = '';
  protected $totalCarbohydrateType = FloatUnit::class;
  protected $totalCarbohydrateDataType = '';
  /**
   * Total carbohydrate daily percentage.
   *
   * @var 
   */
  public $totalCarbohydrateDailyPercentage;
  protected $totalFatType = FloatUnit::class;
  protected $totalFatDataType = '';
  /**
   * Total fat daily percentage.
   *
   * @var 
   */
  public $totalFatDailyPercentage;
  protected $totalSugarsType = FloatUnit::class;
  protected $totalSugarsDataType = '';
  /**
   * Total sugars daily percentage.
   *
   * @var 
   */
  public $totalSugarsDailyPercentage;
  protected $transFatType = FloatUnit::class;
  protected $transFatDataType = '';
  /**
   * Trans fat daily percentage.
   *
   * @var 
   */
  public $transFatDailyPercentage;
  protected $vitaminDType = FloatUnit::class;
  protected $vitaminDDataType = '';
  /**
   * Vitamin D daily percentage.
   *
   * @var 
   */
  public $vitaminDDailyPercentage;
  protected $voluntaryNutritionFactType = VoluntaryNutritionFact::class;
  protected $voluntaryNutritionFactDataType = 'array';

  /**
   * Added sugars.
   *
   * @param FloatUnit $addedSugars
   */
  public function setAddedSugars(FloatUnit $addedSugars)
  {
    $this->addedSugars = $addedSugars;
  }
  /**
   * @return FloatUnit
   */
  public function getAddedSugars()
  {
    return $this->addedSugars;
  }
  public function setAddedSugarsDailyPercentage($addedSugarsDailyPercentage)
  {
    $this->addedSugarsDailyPercentage = $addedSugarsDailyPercentage;
  }
  public function getAddedSugarsDailyPercentage()
  {
    return $this->addedSugarsDailyPercentage;
  }
  /**
   * Calcium.
   *
   * @param FloatUnit $calcium
   */
  public function setCalcium(FloatUnit $calcium)
  {
    $this->calcium = $calcium;
  }
  /**
   * @return FloatUnit
   */
  public function getCalcium()
  {
    return $this->calcium;
  }
  public function setCalciumDailyPercentage($calciumDailyPercentage)
  {
    $this->calciumDailyPercentage = $calciumDailyPercentage;
  }
  public function getCalciumDailyPercentage()
  {
    return $this->calciumDailyPercentage;
  }
  /**
   * Cholesterol.
   *
   * @param FloatUnit $cholesterol
   */
  public function setCholesterol(FloatUnit $cholesterol)
  {
    $this->cholesterol = $cholesterol;
  }
  /**
   * @return FloatUnit
   */
  public function getCholesterol()
  {
    return $this->cholesterol;
  }
  public function setCholesterolDailyPercentage($cholesterolDailyPercentage)
  {
    $this->cholesterolDailyPercentage = $cholesterolDailyPercentage;
  }
  public function getCholesterolDailyPercentage()
  {
    return $this->cholesterolDailyPercentage;
  }
  /**
   * Dietary fiber.
   *
   * @param FloatUnit $dietaryFiber
   */
  public function setDietaryFiber(FloatUnit $dietaryFiber)
  {
    $this->dietaryFiber = $dietaryFiber;
  }
  /**
   * @return FloatUnit
   */
  public function getDietaryFiber()
  {
    return $this->dietaryFiber;
  }
  public function setDietaryFiberDailyPercentage($dietaryFiberDailyPercentage)
  {
    $this->dietaryFiberDailyPercentage = $dietaryFiberDailyPercentage;
  }
  public function getDietaryFiberDailyPercentage()
  {
    return $this->dietaryFiberDailyPercentage;
  }
  /**
   * Mandatory Nutrition Facts. Energy.
   *
   * @param FloatUnit $energy
   */
  public function setEnergy(FloatUnit $energy)
  {
    $this->energy = $energy;
  }
  /**
   * @return FloatUnit
   */
  public function getEnergy()
  {
    return $this->energy;
  }
  /**
   * Energy from fat.
   *
   * @param FloatUnit $energyFromFat
   */
  public function setEnergyFromFat(FloatUnit $energyFromFat)
  {
    $this->energyFromFat = $energyFromFat;
  }
  /**
   * @return FloatUnit
   */
  public function getEnergyFromFat()
  {
    return $this->energyFromFat;
  }
  public function setFolateDailyPercentage($folateDailyPercentage)
  {
    $this->folateDailyPercentage = $folateDailyPercentage;
  }
  public function getFolateDailyPercentage()
  {
    return $this->folateDailyPercentage;
  }
  /**
   * Folate folic acid.
   *
   * @param FloatUnit $folateFolicAcid
   */
  public function setFolateFolicAcid(FloatUnit $folateFolicAcid)
  {
    $this->folateFolicAcid = $folateFolicAcid;
  }
  /**
   * @return FloatUnit
   */
  public function getFolateFolicAcid()
  {
    return $this->folateFolicAcid;
  }
  public function setFolateMcgDfe($folateMcgDfe)
  {
    $this->folateMcgDfe = $folateMcgDfe;
  }
  public function getFolateMcgDfe()
  {
    return $this->folateMcgDfe;
  }
  /**
   * Iron.
   *
   * @param FloatUnit $iron
   */
  public function setIron(FloatUnit $iron)
  {
    $this->iron = $iron;
  }
  /**
   * @return FloatUnit
   */
  public function getIron()
  {
    return $this->iron;
  }
  public function setIronDailyPercentage($ironDailyPercentage)
  {
    $this->ironDailyPercentage = $ironDailyPercentage;
  }
  public function getIronDailyPercentage()
  {
    return $this->ironDailyPercentage;
  }
  /**
   * Monounsaturated fat.
   *
   * @param FloatUnit $monounsaturatedFat
   */
  public function setMonounsaturatedFat(FloatUnit $monounsaturatedFat)
  {
    $this->monounsaturatedFat = $monounsaturatedFat;
  }
  /**
   * @return FloatUnit
   */
  public function getMonounsaturatedFat()
  {
    return $this->monounsaturatedFat;
  }
  /**
   * Nutrition fact measure.
   *
   * @param string $nutritionFactMeasure
   */
  public function setNutritionFactMeasure($nutritionFactMeasure)
  {
    $this->nutritionFactMeasure = $nutritionFactMeasure;
  }
  /**
   * @return string
   */
  public function getNutritionFactMeasure()
  {
    return $this->nutritionFactMeasure;
  }
  /**
   * Polyols.
   *
   * @param FloatUnit $polyols
   */
  public function setPolyols(FloatUnit $polyols)
  {
    $this->polyols = $polyols;
  }
  /**
   * @return FloatUnit
   */
  public function getPolyols()
  {
    return $this->polyols;
  }
  /**
   * Polyunsaturated fat.
   *
   * @param FloatUnit $polyunsaturatedFat
   */
  public function setPolyunsaturatedFat(FloatUnit $polyunsaturatedFat)
  {
    $this->polyunsaturatedFat = $polyunsaturatedFat;
  }
  /**
   * @return FloatUnit
   */
  public function getPolyunsaturatedFat()
  {
    return $this->polyunsaturatedFat;
  }
  /**
   * Potassium.
   *
   * @param FloatUnit $potassium
   */
  public function setPotassium(FloatUnit $potassium)
  {
    $this->potassium = $potassium;
  }
  /**
   * @return FloatUnit
   */
  public function getPotassium()
  {
    return $this->potassium;
  }
  public function setPotassiumDailyPercentage($potassiumDailyPercentage)
  {
    $this->potassiumDailyPercentage = $potassiumDailyPercentage;
  }
  public function getPotassiumDailyPercentage()
  {
    return $this->potassiumDailyPercentage;
  }
  /**
   * Prepared size description.
   *
   * @param string $preparedSizeDescription
   */
  public function setPreparedSizeDescription($preparedSizeDescription)
  {
    $this->preparedSizeDescription = $preparedSizeDescription;
  }
  /**
   * @return string
   */
  public function getPreparedSizeDescription()
  {
    return $this->preparedSizeDescription;
  }
  /**
   * Protein.
   *
   * @param FloatUnit $protein
   */
  public function setProtein(FloatUnit $protein)
  {
    $this->protein = $protein;
  }
  /**
   * @return FloatUnit
   */
  public function getProtein()
  {
    return $this->protein;
  }
  public function setProteinDailyPercentage($proteinDailyPercentage)
  {
    $this->proteinDailyPercentage = $proteinDailyPercentage;
  }
  public function getProteinDailyPercentage()
  {
    return $this->proteinDailyPercentage;
  }
  /**
   * Saturated fat.
   *
   * @param FloatUnit $saturatedFat
   */
  public function setSaturatedFat(FloatUnit $saturatedFat)
  {
    $this->saturatedFat = $saturatedFat;
  }
  /**
   * @return FloatUnit
   */
  public function getSaturatedFat()
  {
    return $this->saturatedFat;
  }
  public function setSaturatedFatDailyPercentage($saturatedFatDailyPercentage)
  {
    $this->saturatedFatDailyPercentage = $saturatedFatDailyPercentage;
  }
  public function getSaturatedFatDailyPercentage()
  {
    return $this->saturatedFatDailyPercentage;
  }
  /**
   * Food Serving Size. Serving size description.
   *
   * @param string $servingSizeDescription
   */
  public function setServingSizeDescription($servingSizeDescription)
  {
    $this->servingSizeDescription = $servingSizeDescription;
  }
  /**
   * @return string
   */
  public function getServingSizeDescription()
  {
    return $this->servingSizeDescription;
  }
  /**
   * Serving size measure.
   *
   * @param FloatUnit $servingSizeMeasure
   */
  public function setServingSizeMeasure(FloatUnit $servingSizeMeasure)
  {
    $this->servingSizeMeasure = $servingSizeMeasure;
  }
  /**
   * @return FloatUnit
   */
  public function getServingSizeMeasure()
  {
    return $this->servingSizeMeasure;
  }
  /**
   * Servings per container.
   *
   * @param string $servingsPerContainer
   */
  public function setServingsPerContainer($servingsPerContainer)
  {
    $this->servingsPerContainer = $servingsPerContainer;
  }
  /**
   * @return string
   */
  public function getServingsPerContainer()
  {
    return $this->servingsPerContainer;
  }
  /**
   * Sodium.
   *
   * @param FloatUnit $sodium
   */
  public function setSodium(FloatUnit $sodium)
  {
    $this->sodium = $sodium;
  }
  /**
   * @return FloatUnit
   */
  public function getSodium()
  {
    return $this->sodium;
  }
  public function setSodiumDailyPercentage($sodiumDailyPercentage)
  {
    $this->sodiumDailyPercentage = $sodiumDailyPercentage;
  }
  public function getSodiumDailyPercentage()
  {
    return $this->sodiumDailyPercentage;
  }
  /**
   * Starch.
   *
   * @param FloatUnit $starch
   */
  public function setStarch(FloatUnit $starch)
  {
    $this->starch = $starch;
  }
  /**
   * @return FloatUnit
   */
  public function getStarch()
  {
    return $this->starch;
  }
  /**
   * Total carbohydrate.
   *
   * @param FloatUnit $totalCarbohydrate
   */
  public function setTotalCarbohydrate(FloatUnit $totalCarbohydrate)
  {
    $this->totalCarbohydrate = $totalCarbohydrate;
  }
  /**
   * @return FloatUnit
   */
  public function getTotalCarbohydrate()
  {
    return $this->totalCarbohydrate;
  }
  public function setTotalCarbohydrateDailyPercentage($totalCarbohydrateDailyPercentage)
  {
    $this->totalCarbohydrateDailyPercentage = $totalCarbohydrateDailyPercentage;
  }
  public function getTotalCarbohydrateDailyPercentage()
  {
    return $this->totalCarbohydrateDailyPercentage;
  }
  /**
   * Total fat.
   *
   * @param FloatUnit $totalFat
   */
  public function setTotalFat(FloatUnit $totalFat)
  {
    $this->totalFat = $totalFat;
  }
  /**
   * @return FloatUnit
   */
  public function getTotalFat()
  {
    return $this->totalFat;
  }
  public function setTotalFatDailyPercentage($totalFatDailyPercentage)
  {
    $this->totalFatDailyPercentage = $totalFatDailyPercentage;
  }
  public function getTotalFatDailyPercentage()
  {
    return $this->totalFatDailyPercentage;
  }
  /**
   * Total sugars.
   *
   * @param FloatUnit $totalSugars
   */
  public function setTotalSugars(FloatUnit $totalSugars)
  {
    $this->totalSugars = $totalSugars;
  }
  /**
   * @return FloatUnit
   */
  public function getTotalSugars()
  {
    return $this->totalSugars;
  }
  public function setTotalSugarsDailyPercentage($totalSugarsDailyPercentage)
  {
    $this->totalSugarsDailyPercentage = $totalSugarsDailyPercentage;
  }
  public function getTotalSugarsDailyPercentage()
  {
    return $this->totalSugarsDailyPercentage;
  }
  /**
   * Trans fat.
   *
   * @param FloatUnit $transFat
   */
  public function setTransFat(FloatUnit $transFat)
  {
    $this->transFat = $transFat;
  }
  /**
   * @return FloatUnit
   */
  public function getTransFat()
  {
    return $this->transFat;
  }
  public function setTransFatDailyPercentage($transFatDailyPercentage)
  {
    $this->transFatDailyPercentage = $transFatDailyPercentage;
  }
  public function getTransFatDailyPercentage()
  {
    return $this->transFatDailyPercentage;
  }
  /**
   * Vitamin D.
   *
   * @param FloatUnit $vitaminD
   */
  public function setVitaminD(FloatUnit $vitaminD)
  {
    $this->vitaminD = $vitaminD;
  }
  /**
   * @return FloatUnit
   */
  public function getVitaminD()
  {
    return $this->vitaminD;
  }
  public function setVitaminDDailyPercentage($vitaminDDailyPercentage)
  {
    $this->vitaminDDailyPercentage = $vitaminDDailyPercentage;
  }
  public function getVitaminDDailyPercentage()
  {
    return $this->vitaminDDailyPercentage;
  }
  /**
   * Voluntary nutrition fact.
   *
   * @param VoluntaryNutritionFact[] $voluntaryNutritionFact
   */
  public function setVoluntaryNutritionFact($voluntaryNutritionFact)
  {
    $this->voluntaryNutritionFact = $voluntaryNutritionFact;
  }
  /**
   * @return VoluntaryNutritionFact[]
   */
  public function getVoluntaryNutritionFact()
  {
    return $this->voluntaryNutritionFact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Nutrition::class, 'Google_Service_ManufacturerCenter_Nutrition');
