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

class Activities extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BEACH_ACCESS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BEACH_ACCESS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BEACH_ACCESS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BEACH_ACCESS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BEACH_FRONT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BEACH_FRONT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BEACH_FRONT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BEACH_FRONT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BICYCLE_RENTAL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BICYCLE_RENTAL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BICYCLE_RENTAL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BICYCLE_RENTAL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BOUTIQUE_STORES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BOUTIQUE_STORES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BOUTIQUE_STORES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BOUTIQUE_STORES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CASINO_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CASINO_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CASINO_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CASINO_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_BICYCLE_RENTAL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_BICYCLE_RENTAL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_BICYCLE_RENTAL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_BICYCLE_RENTAL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_WATERCRAFT_RENTAL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_WATERCRAFT_RENTAL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_WATERCRAFT_RENTAL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_WATERCRAFT_RENTAL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GAME_ROOM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GAME_ROOM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GAME_ROOM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GAME_ROOM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GOLF_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GOLF_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GOLF_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GOLF_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HORSEBACK_RIDING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HORSEBACK_RIDING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HORSEBACK_RIDING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HORSEBACK_RIDING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NIGHTCLUB_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NIGHTCLUB_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NIGHTCLUB_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NIGHTCLUB_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PRIVATE_BEACH_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PRIVATE_BEACH_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PRIVATE_BEACH_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PRIVATE_BEACH_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SCUBA_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SCUBA_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SCUBA_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SCUBA_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SNORKELING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SNORKELING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SNORKELING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SNORKELING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TENNIS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TENNIS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TENNIS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TENNIS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATER_SKIING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATER_SKIING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATER_SKIING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATER_SKIING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WATERCRAFT_RENTAL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WATERCRAFT_RENTAL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WATERCRAFT_RENTAL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WATERCRAFT_RENTAL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Beach access. The hotel property is in close proximity to a beach and
   * offers a way to get to that beach. This can include a route to the beach
   * such as stairs down if hotel is on a bluff, or a short trail. Not the same
   * as beachfront (with beach access, the hotel's proximity is close to but not
   * right on the beach).
   *
   * @var bool
   */
  public $beachAccess;
  /**
   * Beach access exception.
   *
   * @var string
   */
  public $beachAccessException;
  /**
   * Breach front. The hotel property is physically located on the beach
   * alongside an ocean, sea, gulf, or bay. It is not on a lake, river, stream,
   * or pond. The hotel is not separated from the beach by a public road
   * allowing vehicular, pedestrian, or bicycle traffic.
   *
   * @var bool
   */
  public $beachFront;
  /**
   * Beach front exception.
   *
   * @var string
   */
  public $beachFrontException;
  /**
   * Bicycle rental. The hotel owns bicycles that it permits guests to borrow
   * and use. Can be free or for a fee.
   *
   * @var bool
   */
  public $bicycleRental;
  /**
   * Bicycle rental exception.
   *
   * @var string
   */
  public $bicycleRentalException;
  /**
   * Boutique stores. There are stores selling clothing, jewelry, art and decor
   * either on hotel premises or very close by. Does not refer to the hotel gift
   * shop or convenience store.
   *
   * @var bool
   */
  public $boutiqueStores;
  /**
   * Boutique stores exception.
   *
   * @var string
   */
  public $boutiqueStoresException;
  /**
   * Casino. A space designated for gambling and gaming featuring croupier-run
   * table and card games, as well as electronic slot machines. May be on hotel
   * premises or located nearby.
   *
   * @var bool
   */
  public $casino;
  /**
   * Casino exception.
   *
   * @var string
   */
  public $casinoException;
  /**
   * Free bicycle rental. The hotel owns bicycles that it permits guests to
   * borrow and use for free.
   *
   * @var bool
   */
  public $freeBicycleRental;
  /**
   * Free bicycle rental exception.
   *
   * @var string
   */
  public $freeBicycleRentalException;
  /**
   * Free watercraft rental. The hotel owns watercraft that it permits guests to
   * borrow and use for free.
   *
   * @var bool
   */
  public $freeWatercraftRental;
  /**
   * Free Watercraft rental exception.
   *
   * @var string
   */
  public $freeWatercraftRentalException;
  /**
   * Game room. There is a room at the hotel containing electronic machines for
   * play such as pinball, prize machines, driving simulators, and other items
   * commonly found at a family fun center or arcade. May also include non-
   * electronic games like pool, foosball, darts, and more. May or may not be
   * designed for children. Also known as arcade, fun room, or family fun
   * center.
   *
   * @var bool
   */
  public $gameRoom;
  /**
   * Game room exception.
   *
   * @var string
   */
  public $gameRoomException;
  /**
   * Golf. There is a golf course on hotel grounds or there is a nearby,
   * independently run golf course that allows use by hotel guests. Can be free
   * or for a fee.
   *
   * @var bool
   */
  public $golf;
  /**
   * Golf exception.
   *
   * @var string
   */
  public $golfException;
  /**
   * Horseback riding. The hotel has a horse barn onsite or an affiliation with
   * a nearby barn to allow for guests to sit astride a horse and direct it to
   * walk, trot, cantor, gallop and/or jump. Can be in a riding ring, on
   * designated paths, or in the wilderness. May or may not involve instruction.
   *
   * @var bool
   */
  public $horsebackRiding;
  /**
   * Horseback riding exception.
   *
   * @var string
   */
  public $horsebackRidingException;
  /**
   * Nightclub. There is a room at the hotel with a bar, a dance floor, and
   * seating where designated staffers play dance music. There may also be a
   * designated area for the performance of live music, singing and comedy acts.
   *
   * @var bool
   */
  public $nightclub;
  /**
   * Nightclub exception.
   *
   * @var string
   */
  public $nightclubException;
  /**
   * Private beach. The beach which is in close proximity to the hotel is open
   * only to guests.
   *
   * @var bool
   */
  public $privateBeach;
  /**
   * Private beach exception.
   *
   * @var string
   */
  public $privateBeachException;
  /**
   * Scuba. The provision for guests to dive under naturally occurring water
   * fitted with a self-contained underwater breathing apparatus (SCUBA) for the
   * purpose of exploring underwater life. Apparatus consists of a tank
   * providing oxygen to the diver through a mask. Requires certification of the
   * diver and supervision. The hotel may have the activity at its own
   * waterfront or have an affiliation with a nearby facility. Required
   * equipment is most often supplied to guests. Can be free or for a fee. Not
   * snorkeling. Not done in a swimming pool.
   *
   * @var bool
   */
  public $scuba;
  /**
   * Scuba exception.
   *
   * @var string
   */
  public $scubaException;
  /**
   * Snorkeling. The provision for guests to participate in a recreational water
   * activity in which swimmers wear a diving mask, a simple, shaped breathing
   * tube and flippers/swim fins for the purpose of exploring below the surface
   * of an ocean, gulf or lake. Does not usually require user certification or
   * professional supervision. Equipment may or may not be available for rent or
   * purchase. Not scuba diving.
   *
   * @var bool
   */
  public $snorkeling;
  /**
   * Snorkeling exception.
   *
   * @var string
   */
  public $snorkelingException;
  /**
   * Tennis. The hotel has the requisite court(s) on site or has an affiliation
   * with a nearby facility for the purpose of providing guests with the
   * opportunity to play a two-sided court-based game in which players use a
   * stringed racquet to hit a ball across a net to the side of the opposing
   * player. The court can be indoors or outdoors. Instructors, racquets and
   * balls may or may not be provided.
   *
   * @var bool
   */
  public $tennis;
  /**
   * Tennis exception.
   *
   * @var string
   */
  public $tennisException;
  /**
   * Water skiing. The provision of giving guests the opportunity to be pulled
   * across naturally occurring water while standing on skis and holding a tow
   * rope attached to a motorboat. Can occur on hotel premises or at a nearby
   * waterfront. Most often performed in a lake or ocean.
   *
   * @var bool
   */
  public $waterSkiing;
  /**
   * Water skiing exception.
   *
   * @var string
   */
  public $waterSkiingException;
  /**
   * Watercraft rental. The hotel owns water vessels that it permits guests to
   * borrow and use. Can be free or for a fee. Watercraft may include boats,
   * pedal boats, rowboats, sailboats, powerboats, canoes, kayaks, or personal
   * watercraft (such as a Jet Ski).
   *
   * @var bool
   */
  public $watercraftRental;
  /**
   * Watercraft rental exception.
   *
   * @var string
   */
  public $watercraftRentalException;

  /**
   * Beach access. The hotel property is in close proximity to a beach and
   * offers a way to get to that beach. This can include a route to the beach
   * such as stairs down if hotel is on a bluff, or a short trail. Not the same
   * as beachfront (with beach access, the hotel's proximity is close to but not
   * right on the beach).
   *
   * @param bool $beachAccess
   */
  public function setBeachAccess($beachAccess)
  {
    $this->beachAccess = $beachAccess;
  }
  /**
   * @return bool
   */
  public function getBeachAccess()
  {
    return $this->beachAccess;
  }
  /**
   * Beach access exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BEACH_ACCESS_EXCEPTION_* $beachAccessException
   */
  public function setBeachAccessException($beachAccessException)
  {
    $this->beachAccessException = $beachAccessException;
  }
  /**
   * @return self::BEACH_ACCESS_EXCEPTION_*
   */
  public function getBeachAccessException()
  {
    return $this->beachAccessException;
  }
  /**
   * Breach front. The hotel property is physically located on the beach
   * alongside an ocean, sea, gulf, or bay. It is not on a lake, river, stream,
   * or pond. The hotel is not separated from the beach by a public road
   * allowing vehicular, pedestrian, or bicycle traffic.
   *
   * @param bool $beachFront
   */
  public function setBeachFront($beachFront)
  {
    $this->beachFront = $beachFront;
  }
  /**
   * @return bool
   */
  public function getBeachFront()
  {
    return $this->beachFront;
  }
  /**
   * Beach front exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BEACH_FRONT_EXCEPTION_* $beachFrontException
   */
  public function setBeachFrontException($beachFrontException)
  {
    $this->beachFrontException = $beachFrontException;
  }
  /**
   * @return self::BEACH_FRONT_EXCEPTION_*
   */
  public function getBeachFrontException()
  {
    return $this->beachFrontException;
  }
  /**
   * Bicycle rental. The hotel owns bicycles that it permits guests to borrow
   * and use. Can be free or for a fee.
   *
   * @param bool $bicycleRental
   */
  public function setBicycleRental($bicycleRental)
  {
    $this->bicycleRental = $bicycleRental;
  }
  /**
   * @return bool
   */
  public function getBicycleRental()
  {
    return $this->bicycleRental;
  }
  /**
   * Bicycle rental exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BICYCLE_RENTAL_EXCEPTION_* $bicycleRentalException
   */
  public function setBicycleRentalException($bicycleRentalException)
  {
    $this->bicycleRentalException = $bicycleRentalException;
  }
  /**
   * @return self::BICYCLE_RENTAL_EXCEPTION_*
   */
  public function getBicycleRentalException()
  {
    return $this->bicycleRentalException;
  }
  /**
   * Boutique stores. There are stores selling clothing, jewelry, art and decor
   * either on hotel premises or very close by. Does not refer to the hotel gift
   * shop or convenience store.
   *
   * @param bool $boutiqueStores
   */
  public function setBoutiqueStores($boutiqueStores)
  {
    $this->boutiqueStores = $boutiqueStores;
  }
  /**
   * @return bool
   */
  public function getBoutiqueStores()
  {
    return $this->boutiqueStores;
  }
  /**
   * Boutique stores exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BOUTIQUE_STORES_EXCEPTION_* $boutiqueStoresException
   */
  public function setBoutiqueStoresException($boutiqueStoresException)
  {
    $this->boutiqueStoresException = $boutiqueStoresException;
  }
  /**
   * @return self::BOUTIQUE_STORES_EXCEPTION_*
   */
  public function getBoutiqueStoresException()
  {
    return $this->boutiqueStoresException;
  }
  /**
   * Casino. A space designated for gambling and gaming featuring croupier-run
   * table and card games, as well as electronic slot machines. May be on hotel
   * premises or located nearby.
   *
   * @param bool $casino
   */
  public function setCasino($casino)
  {
    $this->casino = $casino;
  }
  /**
   * @return bool
   */
  public function getCasino()
  {
    return $this->casino;
  }
  /**
   * Casino exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CASINO_EXCEPTION_* $casinoException
   */
  public function setCasinoException($casinoException)
  {
    $this->casinoException = $casinoException;
  }
  /**
   * @return self::CASINO_EXCEPTION_*
   */
  public function getCasinoException()
  {
    return $this->casinoException;
  }
  /**
   * Free bicycle rental. The hotel owns bicycles that it permits guests to
   * borrow and use for free.
   *
   * @param bool $freeBicycleRental
   */
  public function setFreeBicycleRental($freeBicycleRental)
  {
    $this->freeBicycleRental = $freeBicycleRental;
  }
  /**
   * @return bool
   */
  public function getFreeBicycleRental()
  {
    return $this->freeBicycleRental;
  }
  /**
   * Free bicycle rental exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_BICYCLE_RENTAL_EXCEPTION_* $freeBicycleRentalException
   */
  public function setFreeBicycleRentalException($freeBicycleRentalException)
  {
    $this->freeBicycleRentalException = $freeBicycleRentalException;
  }
  /**
   * @return self::FREE_BICYCLE_RENTAL_EXCEPTION_*
   */
  public function getFreeBicycleRentalException()
  {
    return $this->freeBicycleRentalException;
  }
  /**
   * Free watercraft rental. The hotel owns watercraft that it permits guests to
   * borrow and use for free.
   *
   * @param bool $freeWatercraftRental
   */
  public function setFreeWatercraftRental($freeWatercraftRental)
  {
    $this->freeWatercraftRental = $freeWatercraftRental;
  }
  /**
   * @return bool
   */
  public function getFreeWatercraftRental()
  {
    return $this->freeWatercraftRental;
  }
  /**
   * Free Watercraft rental exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_WATERCRAFT_RENTAL_EXCEPTION_* $freeWatercraftRentalException
   */
  public function setFreeWatercraftRentalException($freeWatercraftRentalException)
  {
    $this->freeWatercraftRentalException = $freeWatercraftRentalException;
  }
  /**
   * @return self::FREE_WATERCRAFT_RENTAL_EXCEPTION_*
   */
  public function getFreeWatercraftRentalException()
  {
    return $this->freeWatercraftRentalException;
  }
  /**
   * Game room. There is a room at the hotel containing electronic machines for
   * play such as pinball, prize machines, driving simulators, and other items
   * commonly found at a family fun center or arcade. May also include non-
   * electronic games like pool, foosball, darts, and more. May or may not be
   * designed for children. Also known as arcade, fun room, or family fun
   * center.
   *
   * @param bool $gameRoom
   */
  public function setGameRoom($gameRoom)
  {
    $this->gameRoom = $gameRoom;
  }
  /**
   * @return bool
   */
  public function getGameRoom()
  {
    return $this->gameRoom;
  }
  /**
   * Game room exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GAME_ROOM_EXCEPTION_* $gameRoomException
   */
  public function setGameRoomException($gameRoomException)
  {
    $this->gameRoomException = $gameRoomException;
  }
  /**
   * @return self::GAME_ROOM_EXCEPTION_*
   */
  public function getGameRoomException()
  {
    return $this->gameRoomException;
  }
  /**
   * Golf. There is a golf course on hotel grounds or there is a nearby,
   * independently run golf course that allows use by hotel guests. Can be free
   * or for a fee.
   *
   * @param bool $golf
   */
  public function setGolf($golf)
  {
    $this->golf = $golf;
  }
  /**
   * @return bool
   */
  public function getGolf()
  {
    return $this->golf;
  }
  /**
   * Golf exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GOLF_EXCEPTION_* $golfException
   */
  public function setGolfException($golfException)
  {
    $this->golfException = $golfException;
  }
  /**
   * @return self::GOLF_EXCEPTION_*
   */
  public function getGolfException()
  {
    return $this->golfException;
  }
  /**
   * Horseback riding. The hotel has a horse barn onsite or an affiliation with
   * a nearby barn to allow for guests to sit astride a horse and direct it to
   * walk, trot, cantor, gallop and/or jump. Can be in a riding ring, on
   * designated paths, or in the wilderness. May or may not involve instruction.
   *
   * @param bool $horsebackRiding
   */
  public function setHorsebackRiding($horsebackRiding)
  {
    $this->horsebackRiding = $horsebackRiding;
  }
  /**
   * @return bool
   */
  public function getHorsebackRiding()
  {
    return $this->horsebackRiding;
  }
  /**
   * Horseback riding exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HORSEBACK_RIDING_EXCEPTION_* $horsebackRidingException
   */
  public function setHorsebackRidingException($horsebackRidingException)
  {
    $this->horsebackRidingException = $horsebackRidingException;
  }
  /**
   * @return self::HORSEBACK_RIDING_EXCEPTION_*
   */
  public function getHorsebackRidingException()
  {
    return $this->horsebackRidingException;
  }
  /**
   * Nightclub. There is a room at the hotel with a bar, a dance floor, and
   * seating where designated staffers play dance music. There may also be a
   * designated area for the performance of live music, singing and comedy acts.
   *
   * @param bool $nightclub
   */
  public function setNightclub($nightclub)
  {
    $this->nightclub = $nightclub;
  }
  /**
   * @return bool
   */
  public function getNightclub()
  {
    return $this->nightclub;
  }
  /**
   * Nightclub exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NIGHTCLUB_EXCEPTION_* $nightclubException
   */
  public function setNightclubException($nightclubException)
  {
    $this->nightclubException = $nightclubException;
  }
  /**
   * @return self::NIGHTCLUB_EXCEPTION_*
   */
  public function getNightclubException()
  {
    return $this->nightclubException;
  }
  /**
   * Private beach. The beach which is in close proximity to the hotel is open
   * only to guests.
   *
   * @param bool $privateBeach
   */
  public function setPrivateBeach($privateBeach)
  {
    $this->privateBeach = $privateBeach;
  }
  /**
   * @return bool
   */
  public function getPrivateBeach()
  {
    return $this->privateBeach;
  }
  /**
   * Private beach exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PRIVATE_BEACH_EXCEPTION_* $privateBeachException
   */
  public function setPrivateBeachException($privateBeachException)
  {
    $this->privateBeachException = $privateBeachException;
  }
  /**
   * @return self::PRIVATE_BEACH_EXCEPTION_*
   */
  public function getPrivateBeachException()
  {
    return $this->privateBeachException;
  }
  /**
   * Scuba. The provision for guests to dive under naturally occurring water
   * fitted with a self-contained underwater breathing apparatus (SCUBA) for the
   * purpose of exploring underwater life. Apparatus consists of a tank
   * providing oxygen to the diver through a mask. Requires certification of the
   * diver and supervision. The hotel may have the activity at its own
   * waterfront or have an affiliation with a nearby facility. Required
   * equipment is most often supplied to guests. Can be free or for a fee. Not
   * snorkeling. Not done in a swimming pool.
   *
   * @param bool $scuba
   */
  public function setScuba($scuba)
  {
    $this->scuba = $scuba;
  }
  /**
   * @return bool
   */
  public function getScuba()
  {
    return $this->scuba;
  }
  /**
   * Scuba exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SCUBA_EXCEPTION_* $scubaException
   */
  public function setScubaException($scubaException)
  {
    $this->scubaException = $scubaException;
  }
  /**
   * @return self::SCUBA_EXCEPTION_*
   */
  public function getScubaException()
  {
    return $this->scubaException;
  }
  /**
   * Snorkeling. The provision for guests to participate in a recreational water
   * activity in which swimmers wear a diving mask, a simple, shaped breathing
   * tube and flippers/swim fins for the purpose of exploring below the surface
   * of an ocean, gulf or lake. Does not usually require user certification or
   * professional supervision. Equipment may or may not be available for rent or
   * purchase. Not scuba diving.
   *
   * @param bool $snorkeling
   */
  public function setSnorkeling($snorkeling)
  {
    $this->snorkeling = $snorkeling;
  }
  /**
   * @return bool
   */
  public function getSnorkeling()
  {
    return $this->snorkeling;
  }
  /**
   * Snorkeling exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SNORKELING_EXCEPTION_* $snorkelingException
   */
  public function setSnorkelingException($snorkelingException)
  {
    $this->snorkelingException = $snorkelingException;
  }
  /**
   * @return self::SNORKELING_EXCEPTION_*
   */
  public function getSnorkelingException()
  {
    return $this->snorkelingException;
  }
  /**
   * Tennis. The hotel has the requisite court(s) on site or has an affiliation
   * with a nearby facility for the purpose of providing guests with the
   * opportunity to play a two-sided court-based game in which players use a
   * stringed racquet to hit a ball across a net to the side of the opposing
   * player. The court can be indoors or outdoors. Instructors, racquets and
   * balls may or may not be provided.
   *
   * @param bool $tennis
   */
  public function setTennis($tennis)
  {
    $this->tennis = $tennis;
  }
  /**
   * @return bool
   */
  public function getTennis()
  {
    return $this->tennis;
  }
  /**
   * Tennis exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TENNIS_EXCEPTION_* $tennisException
   */
  public function setTennisException($tennisException)
  {
    $this->tennisException = $tennisException;
  }
  /**
   * @return self::TENNIS_EXCEPTION_*
   */
  public function getTennisException()
  {
    return $this->tennisException;
  }
  /**
   * Water skiing. The provision of giving guests the opportunity to be pulled
   * across naturally occurring water while standing on skis and holding a tow
   * rope attached to a motorboat. Can occur on hotel premises or at a nearby
   * waterfront. Most often performed in a lake or ocean.
   *
   * @param bool $waterSkiing
   */
  public function setWaterSkiing($waterSkiing)
  {
    $this->waterSkiing = $waterSkiing;
  }
  /**
   * @return bool
   */
  public function getWaterSkiing()
  {
    return $this->waterSkiing;
  }
  /**
   * Water skiing exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATER_SKIING_EXCEPTION_* $waterSkiingException
   */
  public function setWaterSkiingException($waterSkiingException)
  {
    $this->waterSkiingException = $waterSkiingException;
  }
  /**
   * @return self::WATER_SKIING_EXCEPTION_*
   */
  public function getWaterSkiingException()
  {
    return $this->waterSkiingException;
  }
  /**
   * Watercraft rental. The hotel owns water vessels that it permits guests to
   * borrow and use. Can be free or for a fee. Watercraft may include boats,
   * pedal boats, rowboats, sailboats, powerboats, canoes, kayaks, or personal
   * watercraft (such as a Jet Ski).
   *
   * @param bool $watercraftRental
   */
  public function setWatercraftRental($watercraftRental)
  {
    $this->watercraftRental = $watercraftRental;
  }
  /**
   * @return bool
   */
  public function getWatercraftRental()
  {
    return $this->watercraftRental;
  }
  /**
   * Watercraft rental exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WATERCRAFT_RENTAL_EXCEPTION_* $watercraftRentalException
   */
  public function setWatercraftRentalException($watercraftRentalException)
  {
    $this->watercraftRentalException = $watercraftRentalException;
  }
  /**
   * @return self::WATERCRAFT_RENTAL_EXCEPTION_*
   */
  public function getWatercraftRentalException()
  {
    return $this->watercraftRentalException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Activities::class, 'Google_Service_MyBusinessLodging_Activities');
