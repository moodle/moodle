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

class LivingAreaFeatures extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const AIR_CONDITIONING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const AIR_CONDITIONING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const AIR_CONDITIONING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const AIR_CONDITIONING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BATHTUB_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BATHTUB_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BATHTUB_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BATHTUB_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BIDET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BIDET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BIDET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BIDET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DRYER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DRYER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DRYER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DRYER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ELECTRONIC_ROOM_KEY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ELECTRONIC_ROOM_KEY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ELECTRONIC_ROOM_KEY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ELECTRONIC_ROOM_KEY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FIREPLACE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FIREPLACE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FIREPLACE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FIREPLACE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HAIRDRYER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HAIRDRYER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HAIRDRYER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HAIRDRYER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HEATING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HEATING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HEATING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HEATING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INUNIT_SAFE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INUNIT_SAFE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INUNIT_SAFE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INUNIT_SAFE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const INUNIT_WIFI_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const INUNIT_WIFI_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const INUNIT_WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const INUNIT_WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const IRONING_EQUIPMENT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const IRONING_EQUIPMENT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const IRONING_EQUIPMENT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const IRONING_EQUIPMENT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PAY_PER_VIEW_MOVIES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PAY_PER_VIEW_MOVIES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PAY_PER_VIEW_MOVIES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PAY_PER_VIEW_MOVIES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PRIVATE_BATHROOM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PRIVATE_BATHROOM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PRIVATE_BATHROOM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PRIVATE_BATHROOM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SHOWER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SHOWER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SHOWER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SHOWER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TOILET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TOILET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TOILET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TOILET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TV_CASTING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TV_CASTING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TV_CASTING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TV_CASTING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TV_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TV_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TV_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TV_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TV_STREAMING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TV_STREAMING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TV_STREAMING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TV_STREAMING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const UNIVERSAL_POWER_ADAPTERS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const UNIVERSAL_POWER_ADAPTERS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const UNIVERSAL_POWER_ADAPTERS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const UNIVERSAL_POWER_ADAPTERS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WASHER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WASHER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WASHER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WASHER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Air conditioning. An electrical machine used to cool the temperature of the
   * guestroom.
   *
   * @var bool
   */
  public $airConditioning;
  /**
   * Air conditioning exception.
   *
   * @var string
   */
  public $airConditioningException;
  /**
   * Bathtub. A fixed plumbing feature set on the floor and consisting of a
   * large container that accommodates the body of an adult for the purpose of
   * seated bathing. Includes knobs or fixtures to control the temperature of
   * the water, a faucet through which the water flows, and a drain that can be
   * closed for filling and opened for draining.
   *
   * @var bool
   */
  public $bathtub;
  /**
   * Bathtub exception.
   *
   * @var string
   */
  public $bathtubException;
  /**
   * Bidet. A plumbing fixture attached to a toilet or a low, fixed sink
   * designed for the purpose of washing after toilet use.
   *
   * @var bool
   */
  public $bidet;
  /**
   * Bidet exception.
   *
   * @var string
   */
  public $bidetException;
  /**
   * Dryer. An electrical machine designed to dry clothing.
   *
   * @var bool
   */
  public $dryer;
  /**
   * Dryer exception.
   *
   * @var string
   */
  public $dryerException;
  /**
   * Electronic room key. A card coded by the check-in computer that is read by
   * the lock on the hotel guestroom door to allow for entry.
   *
   * @var bool
   */
  public $electronicRoomKey;
  /**
   * Electronic room key exception.
   *
   * @var string
   */
  public $electronicRoomKeyException;
  /**
   * Fireplace. A framed opening (aka hearth) at the base of a chimney in which
   * logs or an electrical fire feature are burned to provide a relaxing
   * ambiance or to heat the room. Often made of bricks or stone.
   *
   * @var bool
   */
  public $fireplace;
  /**
   * Fireplace exception.
   *
   * @var string
   */
  public $fireplaceException;
  /**
   * Hairdryer. A handheld electric appliance that blows temperature-controlled
   * air for the purpose of drying wet hair. Can be mounted to a bathroom wall
   * or a freestanding device stored in the guestroom's bathroom or closet.
   *
   * @var bool
   */
  public $hairdryer;
  /**
   * Hairdryer exception.
   *
   * @var string
   */
  public $hairdryerException;
  /**
   * Heating. An electrical machine used to warm the temperature of the
   * guestroom.
   *
   * @var bool
   */
  public $heating;
  /**
   * Heating exception.
   *
   * @var string
   */
  public $heatingException;
  /**
   * In-unit safe. A strong fireproof cabinet with a programmable lock, used for
   * the protected storage of valuables in a guestroom. Often built into a
   * closet.
   *
   * @var bool
   */
  public $inunitSafe;
  /**
   * In-unit safe exception.
   *
   * @var string
   */
  public $inunitSafeException;
  /**
   * In-unit Wifi available. Guests can wirelessly connect to the Internet in
   * the guestroom. Can be free or for a fee.
   *
   * @var bool
   */
  public $inunitWifiAvailable;
  /**
   * In-unit Wifi available exception.
   *
   * @var string
   */
  public $inunitWifiAvailableException;
  /**
   * Ironing equipment. A device, usually with a flat metal base, that is heated
   * to smooth, finish, or press clothes and a flat, padded, cloth-covered
   * surface on which the clothes are worked.
   *
   * @var bool
   */
  public $ironingEquipment;
  /**
   * Ironing equipment exception.
   *
   * @var string
   */
  public $ironingEquipmentException;
  /**
   * Pay per view movies. Televisions with channels that offer films that can be
   * viewed for a fee, and have an interface to allow the viewer to accept the
   * terms and approve payment.
   *
   * @var bool
   */
  public $payPerViewMovies;
  /**
   * Pay per view movies exception.
   *
   * @var string
   */
  public $payPerViewMoviesException;
  /**
   * Private bathroom. A bathroom designated for the express use of the guests
   * staying in a specific guestroom.
   *
   * @var bool
   */
  public $privateBathroom;
  /**
   * Private bathroom exception.
   *
   * @var string
   */
  public $privateBathroomException;
  /**
   * Shower. A fixed plumbing fixture for standing bathing that features a tall
   * spray spout or faucet through which water flows, a knob or knobs that
   * control the water's temperature, and a drain in the floor.
   *
   * @var bool
   */
  public $shower;
  /**
   * Shower exception.
   *
   * @var string
   */
  public $showerException;
  /**
   * Toilet. A fixed bathroom feature connected to a sewer or septic system and
   * consisting of a water-flushed bowl with a seat, as well as a device that
   * elicites the water-flushing action. Used for the process and disposal of
   * human waste.
   *
   * @var bool
   */
  public $toilet;
  /**
   * Toilet exception.
   *
   * @var string
   */
  public $toiletException;
  /**
   * TV. A television is available in the guestroom.
   *
   * @var bool
   */
  public $tv;
  /**
   * TV casting. A television equipped with a device through which the video
   * entertainment accessed on a personal computer, phone or tablet can be
   * wirelessly delivered to and viewed on the guestroom's television.
   *
   * @var bool
   */
  public $tvCasting;
  /**
   * TV exception.
   *
   * @var string
   */
  public $tvCastingException;
  /**
   * TV exception.
   *
   * @var string
   */
  public $tvException;
  /**
   * TV streaming. Televisions that embed a range of web-based apps to allow for
   * watching media from those apps.
   *
   * @var bool
   */
  public $tvStreaming;
  /**
   * TV streaming exception.
   *
   * @var string
   */
  public $tvStreamingException;
  /**
   * Universal power adapters. A power supply for electronic devices which plugs
   * into a wall for the purpose of converting AC to a single DC voltage. Also
   * know as AC adapter or charger.
   *
   * @var bool
   */
  public $universalPowerAdapters;
  /**
   * Universal power adapters exception.
   *
   * @var string
   */
  public $universalPowerAdaptersException;
  /**
   * Washer. An electrical machine connected to a running water source designed
   * to launder clothing.
   *
   * @var bool
   */
  public $washer;
  /**
   * Washer exception.
   *
   * @var string
   */
  public $washerException;

  /**
   * Air conditioning. An electrical machine used to cool the temperature of the
   * guestroom.
   *
   * @param bool $airConditioning
   */
  public function setAirConditioning($airConditioning)
  {
    $this->airConditioning = $airConditioning;
  }
  /**
   * @return bool
   */
  public function getAirConditioning()
  {
    return $this->airConditioning;
  }
  /**
   * Air conditioning exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::AIR_CONDITIONING_EXCEPTION_* $airConditioningException
   */
  public function setAirConditioningException($airConditioningException)
  {
    $this->airConditioningException = $airConditioningException;
  }
  /**
   * @return self::AIR_CONDITIONING_EXCEPTION_*
   */
  public function getAirConditioningException()
  {
    return $this->airConditioningException;
  }
  /**
   * Bathtub. A fixed plumbing feature set on the floor and consisting of a
   * large container that accommodates the body of an adult for the purpose of
   * seated bathing. Includes knobs or fixtures to control the temperature of
   * the water, a faucet through which the water flows, and a drain that can be
   * closed for filling and opened for draining.
   *
   * @param bool $bathtub
   */
  public function setBathtub($bathtub)
  {
    $this->bathtub = $bathtub;
  }
  /**
   * @return bool
   */
  public function getBathtub()
  {
    return $this->bathtub;
  }
  /**
   * Bathtub exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BATHTUB_EXCEPTION_* $bathtubException
   */
  public function setBathtubException($bathtubException)
  {
    $this->bathtubException = $bathtubException;
  }
  /**
   * @return self::BATHTUB_EXCEPTION_*
   */
  public function getBathtubException()
  {
    return $this->bathtubException;
  }
  /**
   * Bidet. A plumbing fixture attached to a toilet or a low, fixed sink
   * designed for the purpose of washing after toilet use.
   *
   * @param bool $bidet
   */
  public function setBidet($bidet)
  {
    $this->bidet = $bidet;
  }
  /**
   * @return bool
   */
  public function getBidet()
  {
    return $this->bidet;
  }
  /**
   * Bidet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BIDET_EXCEPTION_* $bidetException
   */
  public function setBidetException($bidetException)
  {
    $this->bidetException = $bidetException;
  }
  /**
   * @return self::BIDET_EXCEPTION_*
   */
  public function getBidetException()
  {
    return $this->bidetException;
  }
  /**
   * Dryer. An electrical machine designed to dry clothing.
   *
   * @param bool $dryer
   */
  public function setDryer($dryer)
  {
    $this->dryer = $dryer;
  }
  /**
   * @return bool
   */
  public function getDryer()
  {
    return $this->dryer;
  }
  /**
   * Dryer exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DRYER_EXCEPTION_* $dryerException
   */
  public function setDryerException($dryerException)
  {
    $this->dryerException = $dryerException;
  }
  /**
   * @return self::DRYER_EXCEPTION_*
   */
  public function getDryerException()
  {
    return $this->dryerException;
  }
  /**
   * Electronic room key. A card coded by the check-in computer that is read by
   * the lock on the hotel guestroom door to allow for entry.
   *
   * @param bool $electronicRoomKey
   */
  public function setElectronicRoomKey($electronicRoomKey)
  {
    $this->electronicRoomKey = $electronicRoomKey;
  }
  /**
   * @return bool
   */
  public function getElectronicRoomKey()
  {
    return $this->electronicRoomKey;
  }
  /**
   * Electronic room key exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ELECTRONIC_ROOM_KEY_EXCEPTION_* $electronicRoomKeyException
   */
  public function setElectronicRoomKeyException($electronicRoomKeyException)
  {
    $this->electronicRoomKeyException = $electronicRoomKeyException;
  }
  /**
   * @return self::ELECTRONIC_ROOM_KEY_EXCEPTION_*
   */
  public function getElectronicRoomKeyException()
  {
    return $this->electronicRoomKeyException;
  }
  /**
   * Fireplace. A framed opening (aka hearth) at the base of a chimney in which
   * logs or an electrical fire feature are burned to provide a relaxing
   * ambiance or to heat the room. Often made of bricks or stone.
   *
   * @param bool $fireplace
   */
  public function setFireplace($fireplace)
  {
    $this->fireplace = $fireplace;
  }
  /**
   * @return bool
   */
  public function getFireplace()
  {
    return $this->fireplace;
  }
  /**
   * Fireplace exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FIREPLACE_EXCEPTION_* $fireplaceException
   */
  public function setFireplaceException($fireplaceException)
  {
    $this->fireplaceException = $fireplaceException;
  }
  /**
   * @return self::FIREPLACE_EXCEPTION_*
   */
  public function getFireplaceException()
  {
    return $this->fireplaceException;
  }
  /**
   * Hairdryer. A handheld electric appliance that blows temperature-controlled
   * air for the purpose of drying wet hair. Can be mounted to a bathroom wall
   * or a freestanding device stored in the guestroom's bathroom or closet.
   *
   * @param bool $hairdryer
   */
  public function setHairdryer($hairdryer)
  {
    $this->hairdryer = $hairdryer;
  }
  /**
   * @return bool
   */
  public function getHairdryer()
  {
    return $this->hairdryer;
  }
  /**
   * Hairdryer exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HAIRDRYER_EXCEPTION_* $hairdryerException
   */
  public function setHairdryerException($hairdryerException)
  {
    $this->hairdryerException = $hairdryerException;
  }
  /**
   * @return self::HAIRDRYER_EXCEPTION_*
   */
  public function getHairdryerException()
  {
    return $this->hairdryerException;
  }
  /**
   * Heating. An electrical machine used to warm the temperature of the
   * guestroom.
   *
   * @param bool $heating
   */
  public function setHeating($heating)
  {
    $this->heating = $heating;
  }
  /**
   * @return bool
   */
  public function getHeating()
  {
    return $this->heating;
  }
  /**
   * Heating exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HEATING_EXCEPTION_* $heatingException
   */
  public function setHeatingException($heatingException)
  {
    $this->heatingException = $heatingException;
  }
  /**
   * @return self::HEATING_EXCEPTION_*
   */
  public function getHeatingException()
  {
    return $this->heatingException;
  }
  /**
   * In-unit safe. A strong fireproof cabinet with a programmable lock, used for
   * the protected storage of valuables in a guestroom. Often built into a
   * closet.
   *
   * @param bool $inunitSafe
   */
  public function setInunitSafe($inunitSafe)
  {
    $this->inunitSafe = $inunitSafe;
  }
  /**
   * @return bool
   */
  public function getInunitSafe()
  {
    return $this->inunitSafe;
  }
  /**
   * In-unit safe exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INUNIT_SAFE_EXCEPTION_* $inunitSafeException
   */
  public function setInunitSafeException($inunitSafeException)
  {
    $this->inunitSafeException = $inunitSafeException;
  }
  /**
   * @return self::INUNIT_SAFE_EXCEPTION_*
   */
  public function getInunitSafeException()
  {
    return $this->inunitSafeException;
  }
  /**
   * In-unit Wifi available. Guests can wirelessly connect to the Internet in
   * the guestroom. Can be free or for a fee.
   *
   * @param bool $inunitWifiAvailable
   */
  public function setInunitWifiAvailable($inunitWifiAvailable)
  {
    $this->inunitWifiAvailable = $inunitWifiAvailable;
  }
  /**
   * @return bool
   */
  public function getInunitWifiAvailable()
  {
    return $this->inunitWifiAvailable;
  }
  /**
   * In-unit Wifi available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::INUNIT_WIFI_AVAILABLE_EXCEPTION_* $inunitWifiAvailableException
   */
  public function setInunitWifiAvailableException($inunitWifiAvailableException)
  {
    $this->inunitWifiAvailableException = $inunitWifiAvailableException;
  }
  /**
   * @return self::INUNIT_WIFI_AVAILABLE_EXCEPTION_*
   */
  public function getInunitWifiAvailableException()
  {
    return $this->inunitWifiAvailableException;
  }
  /**
   * Ironing equipment. A device, usually with a flat metal base, that is heated
   * to smooth, finish, or press clothes and a flat, padded, cloth-covered
   * surface on which the clothes are worked.
   *
   * @param bool $ironingEquipment
   */
  public function setIroningEquipment($ironingEquipment)
  {
    $this->ironingEquipment = $ironingEquipment;
  }
  /**
   * @return bool
   */
  public function getIroningEquipment()
  {
    return $this->ironingEquipment;
  }
  /**
   * Ironing equipment exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::IRONING_EQUIPMENT_EXCEPTION_* $ironingEquipmentException
   */
  public function setIroningEquipmentException($ironingEquipmentException)
  {
    $this->ironingEquipmentException = $ironingEquipmentException;
  }
  /**
   * @return self::IRONING_EQUIPMENT_EXCEPTION_*
   */
  public function getIroningEquipmentException()
  {
    return $this->ironingEquipmentException;
  }
  /**
   * Pay per view movies. Televisions with channels that offer films that can be
   * viewed for a fee, and have an interface to allow the viewer to accept the
   * terms and approve payment.
   *
   * @param bool $payPerViewMovies
   */
  public function setPayPerViewMovies($payPerViewMovies)
  {
    $this->payPerViewMovies = $payPerViewMovies;
  }
  /**
   * @return bool
   */
  public function getPayPerViewMovies()
  {
    return $this->payPerViewMovies;
  }
  /**
   * Pay per view movies exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PAY_PER_VIEW_MOVIES_EXCEPTION_* $payPerViewMoviesException
   */
  public function setPayPerViewMoviesException($payPerViewMoviesException)
  {
    $this->payPerViewMoviesException = $payPerViewMoviesException;
  }
  /**
   * @return self::PAY_PER_VIEW_MOVIES_EXCEPTION_*
   */
  public function getPayPerViewMoviesException()
  {
    return $this->payPerViewMoviesException;
  }
  /**
   * Private bathroom. A bathroom designated for the express use of the guests
   * staying in a specific guestroom.
   *
   * @param bool $privateBathroom
   */
  public function setPrivateBathroom($privateBathroom)
  {
    $this->privateBathroom = $privateBathroom;
  }
  /**
   * @return bool
   */
  public function getPrivateBathroom()
  {
    return $this->privateBathroom;
  }
  /**
   * Private bathroom exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PRIVATE_BATHROOM_EXCEPTION_* $privateBathroomException
   */
  public function setPrivateBathroomException($privateBathroomException)
  {
    $this->privateBathroomException = $privateBathroomException;
  }
  /**
   * @return self::PRIVATE_BATHROOM_EXCEPTION_*
   */
  public function getPrivateBathroomException()
  {
    return $this->privateBathroomException;
  }
  /**
   * Shower. A fixed plumbing fixture for standing bathing that features a tall
   * spray spout or faucet through which water flows, a knob or knobs that
   * control the water's temperature, and a drain in the floor.
   *
   * @param bool $shower
   */
  public function setShower($shower)
  {
    $this->shower = $shower;
  }
  /**
   * @return bool
   */
  public function getShower()
  {
    return $this->shower;
  }
  /**
   * Shower exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SHOWER_EXCEPTION_* $showerException
   */
  public function setShowerException($showerException)
  {
    $this->showerException = $showerException;
  }
  /**
   * @return self::SHOWER_EXCEPTION_*
   */
  public function getShowerException()
  {
    return $this->showerException;
  }
  /**
   * Toilet. A fixed bathroom feature connected to a sewer or septic system and
   * consisting of a water-flushed bowl with a seat, as well as a device that
   * elicites the water-flushing action. Used for the process and disposal of
   * human waste.
   *
   * @param bool $toilet
   */
  public function setToilet($toilet)
  {
    $this->toilet = $toilet;
  }
  /**
   * @return bool
   */
  public function getToilet()
  {
    return $this->toilet;
  }
  /**
   * Toilet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TOILET_EXCEPTION_* $toiletException
   */
  public function setToiletException($toiletException)
  {
    $this->toiletException = $toiletException;
  }
  /**
   * @return self::TOILET_EXCEPTION_*
   */
  public function getToiletException()
  {
    return $this->toiletException;
  }
  /**
   * TV. A television is available in the guestroom.
   *
   * @param bool $tv
   */
  public function setTv($tv)
  {
    $this->tv = $tv;
  }
  /**
   * @return bool
   */
  public function getTv()
  {
    return $this->tv;
  }
  /**
   * TV casting. A television equipped with a device through which the video
   * entertainment accessed on a personal computer, phone or tablet can be
   * wirelessly delivered to and viewed on the guestroom's television.
   *
   * @param bool $tvCasting
   */
  public function setTvCasting($tvCasting)
  {
    $this->tvCasting = $tvCasting;
  }
  /**
   * @return bool
   */
  public function getTvCasting()
  {
    return $this->tvCasting;
  }
  /**
   * TV exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TV_CASTING_EXCEPTION_* $tvCastingException
   */
  public function setTvCastingException($tvCastingException)
  {
    $this->tvCastingException = $tvCastingException;
  }
  /**
   * @return self::TV_CASTING_EXCEPTION_*
   */
  public function getTvCastingException()
  {
    return $this->tvCastingException;
  }
  /**
   * TV exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TV_EXCEPTION_* $tvException
   */
  public function setTvException($tvException)
  {
    $this->tvException = $tvException;
  }
  /**
   * @return self::TV_EXCEPTION_*
   */
  public function getTvException()
  {
    return $this->tvException;
  }
  /**
   * TV streaming. Televisions that embed a range of web-based apps to allow for
   * watching media from those apps.
   *
   * @param bool $tvStreaming
   */
  public function setTvStreaming($tvStreaming)
  {
    $this->tvStreaming = $tvStreaming;
  }
  /**
   * @return bool
   */
  public function getTvStreaming()
  {
    return $this->tvStreaming;
  }
  /**
   * TV streaming exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TV_STREAMING_EXCEPTION_* $tvStreamingException
   */
  public function setTvStreamingException($tvStreamingException)
  {
    $this->tvStreamingException = $tvStreamingException;
  }
  /**
   * @return self::TV_STREAMING_EXCEPTION_*
   */
  public function getTvStreamingException()
  {
    return $this->tvStreamingException;
  }
  /**
   * Universal power adapters. A power supply for electronic devices which plugs
   * into a wall for the purpose of converting AC to a single DC voltage. Also
   * know as AC adapter or charger.
   *
   * @param bool $universalPowerAdapters
   */
  public function setUniversalPowerAdapters($universalPowerAdapters)
  {
    $this->universalPowerAdapters = $universalPowerAdapters;
  }
  /**
   * @return bool
   */
  public function getUniversalPowerAdapters()
  {
    return $this->universalPowerAdapters;
  }
  /**
   * Universal power adapters exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::UNIVERSAL_POWER_ADAPTERS_EXCEPTION_* $universalPowerAdaptersException
   */
  public function setUniversalPowerAdaptersException($universalPowerAdaptersException)
  {
    $this->universalPowerAdaptersException = $universalPowerAdaptersException;
  }
  /**
   * @return self::UNIVERSAL_POWER_ADAPTERS_EXCEPTION_*
   */
  public function getUniversalPowerAdaptersException()
  {
    return $this->universalPowerAdaptersException;
  }
  /**
   * Washer. An electrical machine connected to a running water source designed
   * to launder clothing.
   *
   * @param bool $washer
   */
  public function setWasher($washer)
  {
    $this->washer = $washer;
  }
  /**
   * @return bool
   */
  public function getWasher()
  {
    return $this->washer;
  }
  /**
   * Washer exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WASHER_EXCEPTION_* $washerException
   */
  public function setWasherException($washerException)
  {
    $this->washerException = $washerException;
  }
  /**
   * @return self::WASHER_EXCEPTION_*
   */
  public function getWasherException()
  {
    return $this->washerException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivingAreaFeatures::class, 'Google_Service_MyBusinessLodging_LivingAreaFeatures');
