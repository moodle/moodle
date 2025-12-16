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

class LivingAreaAccessibility extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ADA_COMPLIANT_UNIT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ADA_COMPLIANT_UNIT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ADA_COMPLIANT_UNIT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ADA_COMPLIANT_UNIT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HEARING_ACCESSIBLE_UNIT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HEARING_ACCESSIBLE_UNIT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HEARING_ACCESSIBLE_UNIT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HEARING_ACCESSIBLE_UNIT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * ADA compliant unit. A guestroom designed to accommodate the physical
   * challenges of a guest with mobility and/or auditory and/or visual issues,
   * as determined by legislative policy. Usually features enlarged doorways,
   * roll-in showers with seats, bathroom grab bars, and communication equipment
   * for the hearing and sight challenged.
   *
   * @var bool
   */
  public $adaCompliantUnit;
  /**
   * ADA compliant unit exception.
   *
   * @var string
   */
  public $adaCompliantUnitException;
  /**
   * Hearing-accessible doorbell. A visual indicator(s) of a knock or ring at
   * the door.
   *
   * @var bool
   */
  public $hearingAccessibleDoorbell;
  /**
   * Hearing-accessible doorbell exception.
   *
   * @var string
   */
  public $hearingAccessibleDoorbellException;
  /**
   * Hearing-accessible fire alarm. A device that gives warning of a fire
   * through flashing lights.
   *
   * @var bool
   */
  public $hearingAccessibleFireAlarm;
  /**
   * Hearing-accessible fire alarm exception.
   *
   * @var string
   */
  public $hearingAccessibleFireAlarmException;
  /**
   * Hearing-accessible unit. A guestroom designed to accommodate the physical
   * challenges of a guest with auditory issues.
   *
   * @var bool
   */
  public $hearingAccessibleUnit;
  /**
   * Hearing-accessible unit exception.
   *
   * @var string
   */
  public $hearingAccessibleUnitException;
  /**
   * Mobility-accessible bathtub. A bathtub that accomodates the physically
   * challenged with additional railings or hand grips, a transfer seat or lift,
   * and/or a door to enable walking into the tub.
   *
   * @var bool
   */
  public $mobilityAccessibleBathtub;
  /**
   * Mobility-accessible bathtub exception.
   *
   * @var string
   */
  public $mobilityAccessibleBathtubException;
  /**
   * Mobility-accessible shower. A shower with an enlarged door or access point
   * to accommodate a wheelchair or a waterproof seat for the physically
   * challenged.
   *
   * @var bool
   */
  public $mobilityAccessibleShower;
  /**
   * Mobility-accessible shower exception.
   *
   * @var string
   */
  public $mobilityAccessibleShowerException;
  /**
   * Mobility-accessible toilet. A toilet with a higher seat, grab bars, and/or
   * a larger area around it to accommodate the physically challenged.
   *
   * @var bool
   */
  public $mobilityAccessibleToilet;
  /**
   * Mobility-accessible toilet exception.
   *
   * @var string
   */
  public $mobilityAccessibleToiletException;
  /**
   * Mobility-accessible unit. A guestroom designed to accommodate the physical
   * challenges of a guest with mobility and/or auditory and/or visual issues.
   * Usually features enlarged doorways, roll-in showers with seats, bathroom
   * grab bars, and communication equipment for the hearing and sight
   * challenged.
   *
   * @var bool
   */
  public $mobilityAccessibleUnit;
  /**
   * Mobility-accessible unit exception.
   *
   * @var string
   */
  public $mobilityAccessibleUnitException;

  /**
   * ADA compliant unit. A guestroom designed to accommodate the physical
   * challenges of a guest with mobility and/or auditory and/or visual issues,
   * as determined by legislative policy. Usually features enlarged doorways,
   * roll-in showers with seats, bathroom grab bars, and communication equipment
   * for the hearing and sight challenged.
   *
   * @param bool $adaCompliantUnit
   */
  public function setAdaCompliantUnit($adaCompliantUnit)
  {
    $this->adaCompliantUnit = $adaCompliantUnit;
  }
  /**
   * @return bool
   */
  public function getAdaCompliantUnit()
  {
    return $this->adaCompliantUnit;
  }
  /**
   * ADA compliant unit exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ADA_COMPLIANT_UNIT_EXCEPTION_* $adaCompliantUnitException
   */
  public function setAdaCompliantUnitException($adaCompliantUnitException)
  {
    $this->adaCompliantUnitException = $adaCompliantUnitException;
  }
  /**
   * @return self::ADA_COMPLIANT_UNIT_EXCEPTION_*
   */
  public function getAdaCompliantUnitException()
  {
    return $this->adaCompliantUnitException;
  }
  /**
   * Hearing-accessible doorbell. A visual indicator(s) of a knock or ring at
   * the door.
   *
   * @param bool $hearingAccessibleDoorbell
   */
  public function setHearingAccessibleDoorbell($hearingAccessibleDoorbell)
  {
    $this->hearingAccessibleDoorbell = $hearingAccessibleDoorbell;
  }
  /**
   * @return bool
   */
  public function getHearingAccessibleDoorbell()
  {
    return $this->hearingAccessibleDoorbell;
  }
  /**
   * Hearing-accessible doorbell exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_* $hearingAccessibleDoorbellException
   */
  public function setHearingAccessibleDoorbellException($hearingAccessibleDoorbellException)
  {
    $this->hearingAccessibleDoorbellException = $hearingAccessibleDoorbellException;
  }
  /**
   * @return self::HEARING_ACCESSIBLE_DOORBELL_EXCEPTION_*
   */
  public function getHearingAccessibleDoorbellException()
  {
    return $this->hearingAccessibleDoorbellException;
  }
  /**
   * Hearing-accessible fire alarm. A device that gives warning of a fire
   * through flashing lights.
   *
   * @param bool $hearingAccessibleFireAlarm
   */
  public function setHearingAccessibleFireAlarm($hearingAccessibleFireAlarm)
  {
    $this->hearingAccessibleFireAlarm = $hearingAccessibleFireAlarm;
  }
  /**
   * @return bool
   */
  public function getHearingAccessibleFireAlarm()
  {
    return $this->hearingAccessibleFireAlarm;
  }
  /**
   * Hearing-accessible fire alarm exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_* $hearingAccessibleFireAlarmException
   */
  public function setHearingAccessibleFireAlarmException($hearingAccessibleFireAlarmException)
  {
    $this->hearingAccessibleFireAlarmException = $hearingAccessibleFireAlarmException;
  }
  /**
   * @return self::HEARING_ACCESSIBLE_FIRE_ALARM_EXCEPTION_*
   */
  public function getHearingAccessibleFireAlarmException()
  {
    return $this->hearingAccessibleFireAlarmException;
  }
  /**
   * Hearing-accessible unit. A guestroom designed to accommodate the physical
   * challenges of a guest with auditory issues.
   *
   * @param bool $hearingAccessibleUnit
   */
  public function setHearingAccessibleUnit($hearingAccessibleUnit)
  {
    $this->hearingAccessibleUnit = $hearingAccessibleUnit;
  }
  /**
   * @return bool
   */
  public function getHearingAccessibleUnit()
  {
    return $this->hearingAccessibleUnit;
  }
  /**
   * Hearing-accessible unit exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HEARING_ACCESSIBLE_UNIT_EXCEPTION_* $hearingAccessibleUnitException
   */
  public function setHearingAccessibleUnitException($hearingAccessibleUnitException)
  {
    $this->hearingAccessibleUnitException = $hearingAccessibleUnitException;
  }
  /**
   * @return self::HEARING_ACCESSIBLE_UNIT_EXCEPTION_*
   */
  public function getHearingAccessibleUnitException()
  {
    return $this->hearingAccessibleUnitException;
  }
  /**
   * Mobility-accessible bathtub. A bathtub that accomodates the physically
   * challenged with additional railings or hand grips, a transfer seat or lift,
   * and/or a door to enable walking into the tub.
   *
   * @param bool $mobilityAccessibleBathtub
   */
  public function setMobilityAccessibleBathtub($mobilityAccessibleBathtub)
  {
    $this->mobilityAccessibleBathtub = $mobilityAccessibleBathtub;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleBathtub()
  {
    return $this->mobilityAccessibleBathtub;
  }
  /**
   * Mobility-accessible bathtub exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_* $mobilityAccessibleBathtubException
   */
  public function setMobilityAccessibleBathtubException($mobilityAccessibleBathtubException)
  {
    $this->mobilityAccessibleBathtubException = $mobilityAccessibleBathtubException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_BATHTUB_EXCEPTION_*
   */
  public function getMobilityAccessibleBathtubException()
  {
    return $this->mobilityAccessibleBathtubException;
  }
  /**
   * Mobility-accessible shower. A shower with an enlarged door or access point
   * to accommodate a wheelchair or a waterproof seat for the physically
   * challenged.
   *
   * @param bool $mobilityAccessibleShower
   */
  public function setMobilityAccessibleShower($mobilityAccessibleShower)
  {
    $this->mobilityAccessibleShower = $mobilityAccessibleShower;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleShower()
  {
    return $this->mobilityAccessibleShower;
  }
  /**
   * Mobility-accessible shower exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_* $mobilityAccessibleShowerException
   */
  public function setMobilityAccessibleShowerException($mobilityAccessibleShowerException)
  {
    $this->mobilityAccessibleShowerException = $mobilityAccessibleShowerException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_SHOWER_EXCEPTION_*
   */
  public function getMobilityAccessibleShowerException()
  {
    return $this->mobilityAccessibleShowerException;
  }
  /**
   * Mobility-accessible toilet. A toilet with a higher seat, grab bars, and/or
   * a larger area around it to accommodate the physically challenged.
   *
   * @param bool $mobilityAccessibleToilet
   */
  public function setMobilityAccessibleToilet($mobilityAccessibleToilet)
  {
    $this->mobilityAccessibleToilet = $mobilityAccessibleToilet;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleToilet()
  {
    return $this->mobilityAccessibleToilet;
  }
  /**
   * Mobility-accessible toilet exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_* $mobilityAccessibleToiletException
   */
  public function setMobilityAccessibleToiletException($mobilityAccessibleToiletException)
  {
    $this->mobilityAccessibleToiletException = $mobilityAccessibleToiletException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_TOILET_EXCEPTION_*
   */
  public function getMobilityAccessibleToiletException()
  {
    return $this->mobilityAccessibleToiletException;
  }
  /**
   * Mobility-accessible unit. A guestroom designed to accommodate the physical
   * challenges of a guest with mobility and/or auditory and/or visual issues.
   * Usually features enlarged doorways, roll-in showers with seats, bathroom
   * grab bars, and communication equipment for the hearing and sight
   * challenged.
   *
   * @param bool $mobilityAccessibleUnit
   */
  public function setMobilityAccessibleUnit($mobilityAccessibleUnit)
  {
    $this->mobilityAccessibleUnit = $mobilityAccessibleUnit;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleUnit()
  {
    return $this->mobilityAccessibleUnit;
  }
  /**
   * Mobility-accessible unit exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_* $mobilityAccessibleUnitException
   */
  public function setMobilityAccessibleUnitException($mobilityAccessibleUnitException)
  {
    $this->mobilityAccessibleUnitException = $mobilityAccessibleUnitException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_UNIT_EXCEPTION_*
   */
  public function getMobilityAccessibleUnitException()
  {
    return $this->mobilityAccessibleUnitException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LivingAreaAccessibility::class, 'Google_Service_MyBusinessLodging_LivingAreaAccessibility');
