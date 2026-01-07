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

class FoodAndDrink extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BAR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BAR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BAR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BAR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BREAKFAST_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BREAKFAST_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BREAKFAST_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BREAKFAST_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BREAKFAST_BUFFET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BREAKFAST_BUFFET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BREAKFAST_BUFFET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BREAKFAST_BUFFET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BUFFET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BUFFET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BUFFET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BUFFET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DINNER_BUFFET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DINNER_BUFFET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DINNER_BUFFET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DINNER_BUFFET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_BREAKFAST_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_BREAKFAST_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_BREAKFAST_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_BREAKFAST_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const RESTAURANT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const RESTAURANT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const RESTAURANT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const RESTAURANT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const RESTAURANTS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const RESTAURANTS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const RESTAURANTS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const RESTAURANTS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ROOM_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ROOM_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ROOM_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ROOM_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TABLE_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TABLE_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TABLE_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TABLE_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const VENDING_MACHINE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const VENDING_MACHINE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const VENDING_MACHINE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const VENDING_MACHINE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Bar. A designated room, lounge or area of an on-site restaurant with
   * seating at a counter behind which a hotel staffer takes the guest's order
   * and provides the requested alcoholic drink. Can be indoors or outdoors.
   * Also known as Pub.
   *
   * @var bool
   */
  public $bar;
  /**
   * Bar exception.
   *
   * @var string
   */
  public $barException;
  /**
   * Breakfast available. The morning meal is offered to all guests. Can be free
   * or for a fee.
   *
   * @var bool
   */
  public $breakfastAvailable;
  /**
   * Breakfast available exception.
   *
   * @var string
   */
  public $breakfastAvailableException;
  /**
   * Breakfast buffet. Breakfast meal service where guests serve themselves from
   * a variety of dishes/foods that are put out on a table.
   *
   * @var bool
   */
  public $breakfastBuffet;
  /**
   * Breakfast buffet exception.
   *
   * @var string
   */
  public $breakfastBuffetException;
  /**
   * Buffet. A type of meal where guests serve themselves from a variety of
   * dishes/foods that are put out on a table. Includes lunch and/or dinner
   * meals. A breakfast-only buffet is not sufficient.
   *
   * @var bool
   */
  public $buffet;
  /**
   * Buffet exception.
   *
   * @var string
   */
  public $buffetException;
  /**
   * Dinner buffet. Dinner meal service where guests serve themselves from a
   * variety of dishes/foods that are put out on a table.
   *
   * @var bool
   */
  public $dinnerBuffet;
  /**
   * Dinner buffet exception.
   *
   * @var string
   */
  public $dinnerBuffetException;
  /**
   * Free breakfast. Breakfast is offered for free to all guests. Does not apply
   * if limited to certain room packages.
   *
   * @var bool
   */
  public $freeBreakfast;
  /**
   * Free breakfast exception.
   *
   * @var string
   */
  public $freeBreakfastException;
  /**
   * Restaurant. A business onsite at the hotel that is open to the public as
   * well as guests, and offers meals and beverages to consume at tables or
   * counters. May or may not include table service. Also known as cafe, buffet,
   * eatery. A "breakfast room" where the hotel serves breakfast only to guests
   * (not the general public) does not count as a restaurant.
   *
   * @var bool
   */
  public $restaurant;
  /**
   * Restaurant exception.
   *
   * @var string
   */
  public $restaurantException;
  /**
   * Restaurants count. The number of restaurants at the hotel.
   *
   * @var int
   */
  public $restaurantsCount;
  /**
   * Restaurants count exception.
   *
   * @var string
   */
  public $restaurantsCountException;
  /**
   * Room service. A hotel staffer delivers meals prepared onsite to a guest's
   * room as per their request. May or may not be available during specific
   * hours. Services should be available to all guests (not based on rate/room
   * booked/reward program, etc).
   *
   * @var bool
   */
  public $roomService;
  /**
   * Room service exception.
   *
   * @var string
   */
  public $roomServiceException;
  /**
   * Table service. A restaurant in which a staff member is assigned to a
   * guest's table to take their order, deliver and clear away food, and deliver
   * the bill, if applicable. Also known as sit-down restaurant.
   *
   * @var bool
   */
  public $tableService;
  /**
   * Table service exception.
   *
   * @var string
   */
  public $tableServiceException;
  /**
   * 24hr room service. Room service is available 24 hours a day.
   *
   * @var bool
   */
  public $twentyFourHourRoomService;
  /**
   * 24hr room service exception.
   *
   * @var string
   */
  public $twentyFourHourRoomServiceException;
  /**
   * Vending machine. A glass-fronted mechanized cabinet displaying and
   * dispensing snacks and beverages for purchase by coins, paper money and/or
   * credit cards.
   *
   * @var bool
   */
  public $vendingMachine;
  /**
   * Vending machine exception.
   *
   * @var string
   */
  public $vendingMachineException;

  /**
   * Bar. A designated room, lounge or area of an on-site restaurant with
   * seating at a counter behind which a hotel staffer takes the guest's order
   * and provides the requested alcoholic drink. Can be indoors or outdoors.
   * Also known as Pub.
   *
   * @param bool $bar
   */
  public function setBar($bar)
  {
    $this->bar = $bar;
  }
  /**
   * @return bool
   */
  public function getBar()
  {
    return $this->bar;
  }
  /**
   * Bar exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BAR_EXCEPTION_* $barException
   */
  public function setBarException($barException)
  {
    $this->barException = $barException;
  }
  /**
   * @return self::BAR_EXCEPTION_*
   */
  public function getBarException()
  {
    return $this->barException;
  }
  /**
   * Breakfast available. The morning meal is offered to all guests. Can be free
   * or for a fee.
   *
   * @param bool $breakfastAvailable
   */
  public function setBreakfastAvailable($breakfastAvailable)
  {
    $this->breakfastAvailable = $breakfastAvailable;
  }
  /**
   * @return bool
   */
  public function getBreakfastAvailable()
  {
    return $this->breakfastAvailable;
  }
  /**
   * Breakfast available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BREAKFAST_AVAILABLE_EXCEPTION_* $breakfastAvailableException
   */
  public function setBreakfastAvailableException($breakfastAvailableException)
  {
    $this->breakfastAvailableException = $breakfastAvailableException;
  }
  /**
   * @return self::BREAKFAST_AVAILABLE_EXCEPTION_*
   */
  public function getBreakfastAvailableException()
  {
    return $this->breakfastAvailableException;
  }
  /**
   * Breakfast buffet. Breakfast meal service where guests serve themselves from
   * a variety of dishes/foods that are put out on a table.
   *
   * @param bool $breakfastBuffet
   */
  public function setBreakfastBuffet($breakfastBuffet)
  {
    $this->breakfastBuffet = $breakfastBuffet;
  }
  /**
   * @return bool
   */
  public function getBreakfastBuffet()
  {
    return $this->breakfastBuffet;
  }
  /**
   * Breakfast buffet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BREAKFAST_BUFFET_EXCEPTION_* $breakfastBuffetException
   */
  public function setBreakfastBuffetException($breakfastBuffetException)
  {
    $this->breakfastBuffetException = $breakfastBuffetException;
  }
  /**
   * @return self::BREAKFAST_BUFFET_EXCEPTION_*
   */
  public function getBreakfastBuffetException()
  {
    return $this->breakfastBuffetException;
  }
  /**
   * Buffet. A type of meal where guests serve themselves from a variety of
   * dishes/foods that are put out on a table. Includes lunch and/or dinner
   * meals. A breakfast-only buffet is not sufficient.
   *
   * @param bool $buffet
   */
  public function setBuffet($buffet)
  {
    $this->buffet = $buffet;
  }
  /**
   * @return bool
   */
  public function getBuffet()
  {
    return $this->buffet;
  }
  /**
   * Buffet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BUFFET_EXCEPTION_* $buffetException
   */
  public function setBuffetException($buffetException)
  {
    $this->buffetException = $buffetException;
  }
  /**
   * @return self::BUFFET_EXCEPTION_*
   */
  public function getBuffetException()
  {
    return $this->buffetException;
  }
  /**
   * Dinner buffet. Dinner meal service where guests serve themselves from a
   * variety of dishes/foods that are put out on a table.
   *
   * @param bool $dinnerBuffet
   */
  public function setDinnerBuffet($dinnerBuffet)
  {
    $this->dinnerBuffet = $dinnerBuffet;
  }
  /**
   * @return bool
   */
  public function getDinnerBuffet()
  {
    return $this->dinnerBuffet;
  }
  /**
   * Dinner buffet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DINNER_BUFFET_EXCEPTION_* $dinnerBuffetException
   */
  public function setDinnerBuffetException($dinnerBuffetException)
  {
    $this->dinnerBuffetException = $dinnerBuffetException;
  }
  /**
   * @return self::DINNER_BUFFET_EXCEPTION_*
   */
  public function getDinnerBuffetException()
  {
    return $this->dinnerBuffetException;
  }
  /**
   * Free breakfast. Breakfast is offered for free to all guests. Does not apply
   * if limited to certain room packages.
   *
   * @param bool $freeBreakfast
   */
  public function setFreeBreakfast($freeBreakfast)
  {
    $this->freeBreakfast = $freeBreakfast;
  }
  /**
   * @return bool
   */
  public function getFreeBreakfast()
  {
    return $this->freeBreakfast;
  }
  /**
   * Free breakfast exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_BREAKFAST_EXCEPTION_* $freeBreakfastException
   */
  public function setFreeBreakfastException($freeBreakfastException)
  {
    $this->freeBreakfastException = $freeBreakfastException;
  }
  /**
   * @return self::FREE_BREAKFAST_EXCEPTION_*
   */
  public function getFreeBreakfastException()
  {
    return $this->freeBreakfastException;
  }
  /**
   * Restaurant. A business onsite at the hotel that is open to the public as
   * well as guests, and offers meals and beverages to consume at tables or
   * counters. May or may not include table service. Also known as cafe, buffet,
   * eatery. A "breakfast room" where the hotel serves breakfast only to guests
   * (not the general public) does not count as a restaurant.
   *
   * @param bool $restaurant
   */
  public function setRestaurant($restaurant)
  {
    $this->restaurant = $restaurant;
  }
  /**
   * @return bool
   */
  public function getRestaurant()
  {
    return $this->restaurant;
  }
  /**
   * Restaurant exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::RESTAURANT_EXCEPTION_* $restaurantException
   */
  public function setRestaurantException($restaurantException)
  {
    $this->restaurantException = $restaurantException;
  }
  /**
   * @return self::RESTAURANT_EXCEPTION_*
   */
  public function getRestaurantException()
  {
    return $this->restaurantException;
  }
  /**
   * Restaurants count. The number of restaurants at the hotel.
   *
   * @param int $restaurantsCount
   */
  public function setRestaurantsCount($restaurantsCount)
  {
    $this->restaurantsCount = $restaurantsCount;
  }
  /**
   * @return int
   */
  public function getRestaurantsCount()
  {
    return $this->restaurantsCount;
  }
  /**
   * Restaurants count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::RESTAURANTS_COUNT_EXCEPTION_* $restaurantsCountException
   */
  public function setRestaurantsCountException($restaurantsCountException)
  {
    $this->restaurantsCountException = $restaurantsCountException;
  }
  /**
   * @return self::RESTAURANTS_COUNT_EXCEPTION_*
   */
  public function getRestaurantsCountException()
  {
    return $this->restaurantsCountException;
  }
  /**
   * Room service. A hotel staffer delivers meals prepared onsite to a guest's
   * room as per their request. May or may not be available during specific
   * hours. Services should be available to all guests (not based on rate/room
   * booked/reward program, etc).
   *
   * @param bool $roomService
   */
  public function setRoomService($roomService)
  {
    $this->roomService = $roomService;
  }
  /**
   * @return bool
   */
  public function getRoomService()
  {
    return $this->roomService;
  }
  /**
   * Room service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ROOM_SERVICE_EXCEPTION_* $roomServiceException
   */
  public function setRoomServiceException($roomServiceException)
  {
    $this->roomServiceException = $roomServiceException;
  }
  /**
   * @return self::ROOM_SERVICE_EXCEPTION_*
   */
  public function getRoomServiceException()
  {
    return $this->roomServiceException;
  }
  /**
   * Table service. A restaurant in which a staff member is assigned to a
   * guest's table to take their order, deliver and clear away food, and deliver
   * the bill, if applicable. Also known as sit-down restaurant.
   *
   * @param bool $tableService
   */
  public function setTableService($tableService)
  {
    $this->tableService = $tableService;
  }
  /**
   * @return bool
   */
  public function getTableService()
  {
    return $this->tableService;
  }
  /**
   * Table service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TABLE_SERVICE_EXCEPTION_* $tableServiceException
   */
  public function setTableServiceException($tableServiceException)
  {
    $this->tableServiceException = $tableServiceException;
  }
  /**
   * @return self::TABLE_SERVICE_EXCEPTION_*
   */
  public function getTableServiceException()
  {
    return $this->tableServiceException;
  }
  /**
   * 24hr room service. Room service is available 24 hours a day.
   *
   * @param bool $twentyFourHourRoomService
   */
  public function setTwentyFourHourRoomService($twentyFourHourRoomService)
  {
    $this->twentyFourHourRoomService = $twentyFourHourRoomService;
  }
  /**
   * @return bool
   */
  public function getTwentyFourHourRoomService()
  {
    return $this->twentyFourHourRoomService;
  }
  /**
   * 24hr room service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_* $twentyFourHourRoomServiceException
   */
  public function setTwentyFourHourRoomServiceException($twentyFourHourRoomServiceException)
  {
    $this->twentyFourHourRoomServiceException = $twentyFourHourRoomServiceException;
  }
  /**
   * @return self::TWENTY_FOUR_HOUR_ROOM_SERVICE_EXCEPTION_*
   */
  public function getTwentyFourHourRoomServiceException()
  {
    return $this->twentyFourHourRoomServiceException;
  }
  /**
   * Vending machine. A glass-fronted mechanized cabinet displaying and
   * dispensing snacks and beverages for purchase by coins, paper money and/or
   * credit cards.
   *
   * @param bool $vendingMachine
   */
  public function setVendingMachine($vendingMachine)
  {
    $this->vendingMachine = $vendingMachine;
  }
  /**
   * @return bool
   */
  public function getVendingMachine()
  {
    return $this->vendingMachine;
  }
  /**
   * Vending machine exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::VENDING_MACHINE_EXCEPTION_* $vendingMachineException
   */
  public function setVendingMachineException($vendingMachineException)
  {
    $this->vendingMachineException = $vendingMachineException;
  }
  /**
   * @return self::VENDING_MACHINE_EXCEPTION_*
   */
  public function getVendingMachineException()
  {
    return $this->vendingMachineException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FoodAndDrink::class, 'Google_Service_MyBusinessLodging_FoodAndDrink');
