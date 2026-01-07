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

class LivingAreaEating extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COFFEE_MAKER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COFFEE_MAKER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COFFEE_MAKER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COFFEE_MAKER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COOKWARE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COOKWARE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COOKWARE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COOKWARE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DISHWASHER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DISHWASHER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DISHWASHER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DISHWASHER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INDOOR_GRILL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INDOOR_GRILL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INDOOR_GRILL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INDOOR_GRILL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KETTLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KETTLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KETTLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KETTLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KITCHEN_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KITCHEN_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KITCHEN_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KITCHEN_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MICROWAVE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MICROWAVE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MICROWAVE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MICROWAVE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MINIBAR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MINIBAR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MINIBAR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MINIBAR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OUTDOOR_GRILL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OUTDOOR_GRILL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OUTDOOR_GRILL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OUTDOOR_GRILL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const OVEN_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const OVEN_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const OVEN_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const OVEN_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const REFRIGERATOR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const REFRIGERATOR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const REFRIGERATOR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const REFRIGERATOR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SINK_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SINK_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SINK_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SINK_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SNACKBAR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SNACKBAR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SNACKBAR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SNACKBAR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const STOVE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const STOVE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const STOVE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const STOVE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TEA_STATION_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TEA_STATION_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TEA_STATION_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TEA_STATION_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TOASTER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TOASTER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TOASTER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TOASTER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Coffee maker. An electric appliance that brews coffee by heating and
   * forcing water through ground coffee.
   *
   * @var bool
   */
  public $coffeeMaker;
  /**
   * Coffee maker exception.
   *
   * @var string
   */
  public $coffeeMakerException;
  /**
   * Cookware. Kitchen pots, pans and utensils used in connection with the
   * preparation of food.
   *
   * @var bool
   */
  public $cookware;
  /**
   * Cookware exception.
   *
   * @var string
   */
  public $cookwareException;
  /**
   * Dishwasher. A counter-height electrical cabinet containing racks for dirty
   * dishware, cookware and cutlery, and a dispenser for soap built into the
   * pull-down door. The cabinet is attached to the plumbing system to
   * facilitate the automatic cleaning of its contents.
   *
   * @var bool
   */
  public $dishwasher;
  /**
   * Dishwasher exception.
   *
   * @var string
   */
  public $dishwasherException;
  /**
   * Indoor grill. Metal grates built into an indoor cooktop on which food is
   * cooked over an open flame or electric heat source.
   *
   * @var bool
   */
  public $indoorGrill;
  /**
   * Indoor grill exception.
   *
   * @var string
   */
  public $indoorGrillException;
  /**
   * Kettle. A covered container with a handle and a spout used for boiling
   * water.
   *
   * @var bool
   */
  public $kettle;
  /**
   * Kettle exception.
   *
   * @var string
   */
  public $kettleException;
  /**
   * Kitchen available. An area of the guestroom designated for the preparation
   * and storage of food via the presence of a refrigerator, cook top, oven and
   * sink, as well as cutlery, dishes and cookware. Usually includes small
   * appliances such a coffee maker and a microwave. May or may not include an
   * automatic dishwasher.
   *
   * @var bool
   */
  public $kitchenAvailable;
  /**
   * Kitchen available exception.
   *
   * @var string
   */
  public $kitchenAvailableException;
  /**
   * Microwave. An electric oven that quickly cooks and heats food by microwave
   * energy. Smaller than a standing or wall mounted oven. Usually placed on a
   * kitchen counter, a shelf or tabletop or mounted above a cooktop.
   *
   * @var bool
   */
  public $microwave;
  /**
   * Microwave exception.
   *
   * @var string
   */
  public $microwaveException;
  /**
   * Minibar. A small refrigerated cabinet in the guestroom containing
   * bottles/cans of soft drinks, mini bottles of alcohol, and snacks. The items
   * are most commonly available for a fee.
   *
   * @var bool
   */
  public $minibar;
  /**
   * Minibar exception.
   *
   * @var string
   */
  public $minibarException;
  /**
   * Outdoor grill. Metal grates on which food is cooked over an open flame or
   * electric heat source. Part of an outdoor apparatus that supports the
   * grates. Also known as barbecue grill or barbecue.
   *
   * @var bool
   */
  public $outdoorGrill;
  /**
   * Outdoor grill exception.
   *
   * @var string
   */
  public $outdoorGrillException;
  /**
   * Oven. A temperature controlled, heated metal cabinet powered by gas or
   * electricity in which food is placed for the purpose of cooking or
   * reheating.
   *
   * @var bool
   */
  public $oven;
  /**
   * Oven exception.
   *
   * @var string
   */
  public $ovenException;
  /**
   * Refrigerator. A large, climate-controlled electrical cabinet with vertical
   * doors. Built for the purpose of chilling and storing perishable foods.
   *
   * @var bool
   */
  public $refrigerator;
  /**
   * Refrigerator exception.
   *
   * @var string
   */
  public $refrigeratorException;
  /**
   * Sink. A basin with a faucet attached to a water source and used for the
   * purpose of washing and rinsing.
   *
   * @var bool
   */
  public $sink;
  /**
   * Sink exception.
   *
   * @var string
   */
  public $sinkException;
  /**
   * Snackbar. A small cabinet in the guestroom containing snacks. The items are
   * most commonly available for a fee.
   *
   * @var bool
   */
  public $snackbar;
  /**
   * Snackbar exception.
   *
   * @var string
   */
  public $snackbarException;
  /**
   * Stove. A kitchen appliance powered by gas or electricity for the purpose of
   * creating a flame or hot surface on which pots of food can be cooked. Also
   * known as cooktop or hob.
   *
   * @var bool
   */
  public $stove;
  /**
   * Stove exception.
   *
   * @var string
   */
  public $stoveException;
  /**
   * Tea station. A small area with the supplies needed to heat water and make
   * tea.
   *
   * @var bool
   */
  public $teaStation;
  /**
   * Tea station exception.
   *
   * @var string
   */
  public $teaStationException;
  /**
   * Toaster. A small, temperature controlled electric appliance with
   * rectangular slots at the top that are lined with heated coils for the
   * purpose of browning slices of bread products.
   *
   * @var bool
   */
  public $toaster;
  /**
   * Toaster exception.
   *
   * @var string
   */
  public $toasterException;

  /**
   * Coffee maker. An electric appliance that brews coffee by heating and
   * forcing water through ground coffee.
   *
   * @param bool $coffeeMaker
   */
  public function setCoffeeMaker($coffeeMaker)
  {
    $this->coffeeMaker = $coffeeMaker;
  }
  /**
   * @return bool
   */
  public function getCoffeeMaker()
  {
    return $this->coffeeMaker;
  }
  /**
   * Coffee maker exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COFFEE_MAKER_EXCEPTION_* $coffeeMakerException
   */
  public function setCoffeeMakerException($coffeeMakerException)
  {
    $this->coffeeMakerException = $coffeeMakerException;
  }
  /**
   * @return self::COFFEE_MAKER_EXCEPTION_*
   */
  public function getCoffeeMakerException()
  {
    return $this->coffeeMakerException;
  }
  /**
   * Cookware. Kitchen pots, pans and utensils used in connection with the
   * preparation of food.
   *
   * @param bool $cookware
   */
  public function setCookware($cookware)
  {
    $this->cookware = $cookware;
  }
  /**
   * @return bool
   */
  public function getCookware()
  {
    return $this->cookware;
  }
  /**
   * Cookware exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COOKWARE_EXCEPTION_* $cookwareException
   */
  public function setCookwareException($cookwareException)
  {
    $this->cookwareException = $cookwareException;
  }
  /**
   * @return self::COOKWARE_EXCEPTION_*
   */
  public function getCookwareException()
  {
    return $this->cookwareException;
  }
  /**
   * Dishwasher. A counter-height electrical cabinet containing racks for dirty
   * dishware, cookware and cutlery, and a dispenser for soap built into the
   * pull-down door. The cabinet is attached to the plumbing system to
   * facilitate the automatic cleaning of its contents.
   *
   * @param bool $dishwasher
   */
  public function setDishwasher($dishwasher)
  {
    $this->dishwasher = $dishwasher;
  }
  /**
   * @return bool
   */
  public function getDishwasher()
  {
    return $this->dishwasher;
  }
  /**
   * Dishwasher exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DISHWASHER_EXCEPTION_* $dishwasherException
   */
  public function setDishwasherException($dishwasherException)
  {
    $this->dishwasherException = $dishwasherException;
  }
  /**
   * @return self::DISHWASHER_EXCEPTION_*
   */
  public function getDishwasherException()
  {
    return $this->dishwasherException;
  }
  /**
   * Indoor grill. Metal grates built into an indoor cooktop on which food is
   * cooked over an open flame or electric heat source.
   *
   * @param bool $indoorGrill
   */
  public function setIndoorGrill($indoorGrill)
  {
    $this->indoorGrill = $indoorGrill;
  }
  /**
   * @return bool
   */
  public function getIndoorGrill()
  {
    return $this->indoorGrill;
  }
  /**
   * Indoor grill exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INDOOR_GRILL_EXCEPTION_* $indoorGrillException
   */
  public function setIndoorGrillException($indoorGrillException)
  {
    $this->indoorGrillException = $indoorGrillException;
  }
  /**
   * @return self::INDOOR_GRILL_EXCEPTION_*
   */
  public function getIndoorGrillException()
  {
    return $this->indoorGrillException;
  }
  /**
   * Kettle. A covered container with a handle and a spout used for boiling
   * water.
   *
   * @param bool $kettle
   */
  public function setKettle($kettle)
  {
    $this->kettle = $kettle;
  }
  /**
   * @return bool
   */
  public function getKettle()
  {
    return $this->kettle;
  }
  /**
   * Kettle exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KETTLE_EXCEPTION_* $kettleException
   */
  public function setKettleException($kettleException)
  {
    $this->kettleException = $kettleException;
  }
  /**
   * @return self::KETTLE_EXCEPTION_*
   */
  public function getKettleException()
  {
    return $this->kettleException;
  }
  /**
   * Kitchen available. An area of the guestroom designated for the preparation
   * and storage of food via the presence of a refrigerator, cook top, oven and
   * sink, as well as cutlery, dishes and cookware. Usually includes small
   * appliances such a coffee maker and a microwave. May or may not include an
   * automatic dishwasher.
   *
   * @param bool $kitchenAvailable
   */
  public function setKitchenAvailable($kitchenAvailable)
  {
    $this->kitchenAvailable = $kitchenAvailable;
  }
  /**
   * @return bool
   */
  public function getKitchenAvailable()
  {
    return $this->kitchenAvailable;
  }
  /**
   * Kitchen available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KITCHEN_AVAILABLE_EXCEPTION_* $kitchenAvailableException
   */
  public function setKitchenAvailableException($kitchenAvailableException)
  {
    $this->kitchenAvailableException = $kitchenAvailableException;
  }
  /**
   * @return self::KITCHEN_AVAILABLE_EXCEPTION_*
   */
  public function getKitchenAvailableException()
  {
    return $this->kitchenAvailableException;
  }
  /**
   * Microwave. An electric oven that quickly cooks and heats food by microwave
   * energy. Smaller than a standing or wall mounted oven. Usually placed on a
   * kitchen counter, a shelf or tabletop or mounted above a cooktop.
   *
   * @param bool $microwave
   */
  public function setMicrowave($microwave)
  {
    $this->microwave = $microwave;
  }
  /**
   * @return bool
   */
  public function getMicrowave()
  {
    return $this->microwave;
  }
  /**
   * Microwave exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MICROWAVE_EXCEPTION_* $microwaveException
   */
  public function setMicrowaveException($microwaveException)
  {
    $this->microwaveException = $microwaveException;
  }
  /**
   * @return self::MICROWAVE_EXCEPTION_*
   */
  public function getMicrowaveException()
  {
    return $this->microwaveException;
  }
  /**
   * Minibar. A small refrigerated cabinet in the guestroom containing
   * bottles/cans of soft drinks, mini bottles of alcohol, and snacks. The items
   * are most commonly available for a fee.
   *
   * @param bool $minibar
   */
  public function setMinibar($minibar)
  {
    $this->minibar = $minibar;
  }
  /**
   * @return bool
   */
  public function getMinibar()
  {
    return $this->minibar;
  }
  /**
   * Minibar exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MINIBAR_EXCEPTION_* $minibarException
   */
  public function setMinibarException($minibarException)
  {
    $this->minibarException = $minibarException;
  }
  /**
   * @return self::MINIBAR_EXCEPTION_*
   */
  public function getMinibarException()
  {
    return $this->minibarException;
  }
  /**
   * Outdoor grill. Metal grates on which food is cooked over an open flame or
   * electric heat source. Part of an outdoor apparatus that supports the
   * grates. Also known as barbecue grill or barbecue.
   *
   * @param bool $outdoorGrill
   */
  public function setOutdoorGrill($outdoorGrill)
  {
    $this->outdoorGrill = $outdoorGrill;
  }
  /**
   * @return bool
   */
  public function getOutdoorGrill()
  {
    return $this->outdoorGrill;
  }
  /**
   * Outdoor grill exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OUTDOOR_GRILL_EXCEPTION_* $outdoorGrillException
   */
  public function setOutdoorGrillException($outdoorGrillException)
  {
    $this->outdoorGrillException = $outdoorGrillException;
  }
  /**
   * @return self::OUTDOOR_GRILL_EXCEPTION_*
   */
  public function getOutdoorGrillException()
  {
    return $this->outdoorGrillException;
  }
  /**
   * Oven. A temperature controlled, heated metal cabinet powered by gas or
   * electricity in which food is placed for the purpose of cooking or
   * reheating.
   *
   * @param bool $oven
   */
  public function setOven($oven)
  {
    $this->oven = $oven;
  }
  /**
   * @return bool
   */
  public function getOven()
  {
    return $this->oven;
  }
  /**
   * Oven exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::OVEN_EXCEPTION_* $ovenException
   */
  public function setOvenException($ovenException)
  {
    $this->ovenException = $ovenException;
  }
  /**
   * @return self::OVEN_EXCEPTION_*
   */
  public function getOvenException()
  {
    return $this->ovenException;
  }
  /**
   * Refrigerator. A large, climate-controlled electrical cabinet with vertical
   * doors. Built for the purpose of chilling and storing perishable foods.
   *
   * @param bool $refrigerator
   */
  public function setRefrigerator($refrigerator)
  {
    $this->refrigerator = $refrigerator;
  }
  /**
   * @return bool
   */
  public function getRefrigerator()
  {
    return $this->refrigerator;
  }
  /**
   * Refrigerator exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::REFRIGERATOR_EXCEPTION_* $refrigeratorException
   */
  public function setRefrigeratorException($refrigeratorException)
  {
    $this->refrigeratorException = $refrigeratorException;
  }
  /**
   * @return self::REFRIGERATOR_EXCEPTION_*
   */
  public function getRefrigeratorException()
  {
    return $this->refrigeratorException;
  }
  /**
   * Sink. A basin with a faucet attached to a water source and used for the
   * purpose of washing and rinsing.
   *
   * @param bool $sink
   */
  public function setSink($sink)
  {
    $this->sink = $sink;
  }
  /**
   * @return bool
   */
  public function getSink()
  {
    return $this->sink;
  }
  /**
   * Sink exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SINK_EXCEPTION_* $sinkException
   */
  public function setSinkException($sinkException)
  {
    $this->sinkException = $sinkException;
  }
  /**
   * @return self::SINK_EXCEPTION_*
   */
  public function getSinkException()
  {
    return $this->sinkException;
  }
  /**
   * Snackbar. A small cabinet in the guestroom containing snacks. The items are
   * most commonly available for a fee.
   *
   * @param bool $snackbar
   */
  public function setSnackbar($snackbar)
  {
    $this->snackbar = $snackbar;
  }
  /**
   * @return bool
   */
  public function getSnackbar()
  {
    return $this->snackbar;
  }
  /**
   * Snackbar exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SNACKBAR_EXCEPTION_* $snackbarException
   */
  public function setSnackbarException($snackbarException)
  {
    $this->snackbarException = $snackbarException;
  }
  /**
   * @return self::SNACKBAR_EXCEPTION_*
   */
  public function getSnackbarException()
  {
    return $this->snackbarException;
  }
  /**
   * Stove. A kitchen appliance powered by gas or electricity for the purpose of
   * creating a flame or hot surface on which pots of food can be cooked. Also
   * known as cooktop or hob.
   *
   * @param bool $stove
   */
  public function setStove($stove)
  {
    $this->stove = $stove;
  }
  /**
   * @return bool
   */
  public function getStove()
  {
    return $this->stove;
  }
  /**
   * Stove exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::STOVE_EXCEPTION_* $stoveException
   */
  public function setStoveException($stoveException)
  {
    $this->stoveException = $stoveException;
  }
  /**
   * @return self::STOVE_EXCEPTION_*
   */
  public function getStoveException()
  {
    return $this->stoveException;
  }
  /**
   * Tea station. A small area with the supplies needed to heat water and make
   * tea.
   *
   * @param bool $teaStation
   */
  public function setTeaStation($teaStation)
  {
    $this->teaStation = $teaStation;
  }
  /**
   * @return bool
   */
  public function getTeaStation()
  {
    return $this->teaStation;
  }
  /**
   * Tea station exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TEA_STATION_EXCEPTION_* $teaStationException
   */
  public function setTeaStationException($teaStationException)
  {
    $this->teaStationException = $teaStationException;
  }
  /**
   * @return self::TEA_STATION_EXCEPTION_*
   */
  public function getTeaStationException()
  {
    return $this->teaStationException;
  }
  /**
   * Toaster. A small, temperature controlled electric appliance with
   * rectangular slots at the top that are lined with heated coils for the
   * purpose of browning slices of bread products.
   *
   * @param bool $toaster
   */
  public function setToaster($toaster)
  {
    $this->toaster = $toaster;
  }
  /**
   * @return bool
   */
  public function getToaster()
  {
    return $this->toaster;
  }
  /**
   * Toaster exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TOASTER_EXCEPTION_* $toasterException
   */
  public function setToasterException($toasterException)
  {
    $this->toasterException = $toasterException;
  }
  /**
   * @return self::TOASTER_EXCEPTION_*
   */
  public function getToasterException()
  {
    return $this->toasterException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivingAreaEating::class, 'Google_Service_MyBusinessLodging_LivingAreaEating');
