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

class Wellness extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DOCTOR_ON_CALL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DOCTOR_ON_CALL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DOCTOR_ON_CALL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DOCTOR_ON_CALL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ELLIPTICAL_MACHINE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ELLIPTICAL_MACHINE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ELLIPTICAL_MACHINE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ELLIPTICAL_MACHINE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FITNESS_CENTER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FITNESS_CENTER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FITNESS_CENTER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FITNESS_CENTER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_FITNESS_CENTER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_FITNESS_CENTER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_FITNESS_CENTER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_FITNESS_CENTER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_WEIGHTS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_WEIGHTS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_WEIGHTS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_WEIGHTS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MASSAGE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MASSAGE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MASSAGE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MASSAGE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SALON_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SALON_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SALON_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SALON_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SAUNA_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SAUNA_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SAUNA_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SAUNA_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SPA_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SPA_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SPA_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SPA_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TREADMILL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TREADMILL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TREADMILL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TREADMILL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WEIGHT_MACHINE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WEIGHT_MACHINE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WEIGHT_MACHINE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WEIGHT_MACHINE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Doctor on call. The hotel has a contract with a medical professional who
   * provides services to hotel guests should they fall ill during their stay.
   * The doctor may or may not have an on-site office or be at the hotel at all
   * times.
   *
   * @var bool
   */
  public $doctorOnCall;
  /**
   * Doctor on call exception.
   *
   * @var string
   */
  public $doctorOnCallException;
  /**
   * Elliptical machine. An electric, stationary fitness machine with pedals
   * that simulates climbing, walking or running and provides a user-controlled
   * range of speeds and tensions. May not have arm-controlled levers to work
   * out the upper body as well. Commonly found in a gym, fitness room, health
   * center, or health club.
   *
   * @var bool
   */
  public $ellipticalMachine;
  /**
   * Elliptical machine exception.
   *
   * @var string
   */
  public $ellipticalMachineException;
  /**
   * Fitness center. A room or building at the hotel containing equipment to
   * promote physical activity, such as treadmills, elliptical machines,
   * stationary bikes, weight machines, free weights, and/or stretching mats.
   * Use of the fitness center can be free or for a fee. May or may not be
   * staffed. May or may not offer instructor-led classes in various styles of
   * physical conditioning. May or may not be open 24/7. May or may not include
   * locker rooms and showers. Also known as health club, gym, fitness room,
   * health center.
   *
   * @var bool
   */
  public $fitnessCenter;
  /**
   * Fitness center exception.
   *
   * @var string
   */
  public $fitnessCenterException;
  /**
   * Free fitness center. Guests may use the fitness center for free.
   *
   * @var bool
   */
  public $freeFitnessCenter;
  /**
   * Free fitness center exception.
   *
   * @var string
   */
  public $freeFitnessCenterException;
  /**
   * Free weights. Individual handheld fitness equipment of varied weights used
   * for upper body strength training or bodybuilding. Also known as barbells,
   * dumbbells, or kettlebells. Often stored on a rack with the weights arranged
   * from light to heavy. Commonly found in a gym, fitness room, health center,
   * or health club.
   *
   * @var bool
   */
  public $freeWeights;
  /**
   * Free weights exception.
   *
   * @var string
   */
  public $freeWeightsException;
  /**
   * Massage. A service provided by a trained massage therapist involving the
   * physical manipulation of a guest's muscles in order to achieve relaxation
   * or pain relief.
   *
   * @var bool
   */
  public $massage;
  /**
   * Massage exception.
   *
   * @var string
   */
  public $massageException;
  /**
   * Salon. A room at the hotel where professionals provide hair styling
   * services such as shampooing, blow drying, hair dos, hair cutting and hair
   * coloring. Also known as hairdresser or beauty salon.
   *
   * @var bool
   */
  public $salon;
  /**
   * Salon exception.
   *
   * @var string
   */
  public $salonException;
  /**
   * Sauna. A wood-paneled room heated to a high temperature where guests sit on
   * built-in wood benches for the purpose of perspiring and relaxing their
   * muscles. Can be dry or slightly wet heat. Not a steam room.
   *
   * @var bool
   */
  public $sauna;
  /**
   * Sauna exception.
   *
   * @var string
   */
  public $saunaException;
  /**
   * Spa. A designated area, room or building at the hotel offering health and
   * beauty treatment through such means as steam baths, exercise equipment, and
   * massage. May also offer facials, nail care, and hair care. Services are
   * usually available by appointment and for an additional fee. Does not apply
   * if hotel only offers a steam room; must offer other beauty and/or health
   * treatments as well.
   *
   * @var bool
   */
  public $spa;
  /**
   * Spa exception.
   *
   * @var string
   */
  public $spaException;
  /**
   * Treadmill. An electric stationary fitness machine that simulates a moving
   * path to promote walking or running within a range of user-controlled speeds
   * and inclines. Also known as running machine. Commonly found in a gym,
   * fitness room, health center, or health club.
   *
   * @var bool
   */
  public $treadmill;
  /**
   * Treadmill exception.
   *
   * @var string
   */
  public $treadmillException;
  /**
   * Weight machine. Non-electronic fitness equipment designed for the user to
   * target the exertion of different muscles. Usually incorporates a padded
   * seat, a stack of flat weights and various bars and pulleys. May be designed
   * for toning a specific part of the body or may involve different user-
   * controlled settings, hardware and pulleys so as to provide an overall
   * workout in one machine. Commonly found in a gym, fitness center, fitness
   * room, or health club.
   *
   * @var bool
   */
  public $weightMachine;
  /**
   * Weight machine exception.
   *
   * @var string
   */
  public $weightMachineException;

  /**
   * Doctor on call. The hotel has a contract with a medical professional who
   * provides services to hotel guests should they fall ill during their stay.
   * The doctor may or may not have an on-site office or be at the hotel at all
   * times.
   *
   * @param bool $doctorOnCall
   */
  public function setDoctorOnCall($doctorOnCall)
  {
    $this->doctorOnCall = $doctorOnCall;
  }
  /**
   * @return bool
   */
  public function getDoctorOnCall()
  {
    return $this->doctorOnCall;
  }
  /**
   * Doctor on call exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DOCTOR_ON_CALL_EXCEPTION_* $doctorOnCallException
   */
  public function setDoctorOnCallException($doctorOnCallException)
  {
    $this->doctorOnCallException = $doctorOnCallException;
  }
  /**
   * @return self::DOCTOR_ON_CALL_EXCEPTION_*
   */
  public function getDoctorOnCallException()
  {
    return $this->doctorOnCallException;
  }
  /**
   * Elliptical machine. An electric, stationary fitness machine with pedals
   * that simulates climbing, walking or running and provides a user-controlled
   * range of speeds and tensions. May not have arm-controlled levers to work
   * out the upper body as well. Commonly found in a gym, fitness room, health
   * center, or health club.
   *
   * @param bool $ellipticalMachine
   */
  public function setEllipticalMachine($ellipticalMachine)
  {
    $this->ellipticalMachine = $ellipticalMachine;
  }
  /**
   * @return bool
   */
  public function getEllipticalMachine()
  {
    return $this->ellipticalMachine;
  }
  /**
   * Elliptical machine exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ELLIPTICAL_MACHINE_EXCEPTION_* $ellipticalMachineException
   */
  public function setEllipticalMachineException($ellipticalMachineException)
  {
    $this->ellipticalMachineException = $ellipticalMachineException;
  }
  /**
   * @return self::ELLIPTICAL_MACHINE_EXCEPTION_*
   */
  public function getEllipticalMachineException()
  {
    return $this->ellipticalMachineException;
  }
  /**
   * Fitness center. A room or building at the hotel containing equipment to
   * promote physical activity, such as treadmills, elliptical machines,
   * stationary bikes, weight machines, free weights, and/or stretching mats.
   * Use of the fitness center can be free or for a fee. May or may not be
   * staffed. May or may not offer instructor-led classes in various styles of
   * physical conditioning. May or may not be open 24/7. May or may not include
   * locker rooms and showers. Also known as health club, gym, fitness room,
   * health center.
   *
   * @param bool $fitnessCenter
   */
  public function setFitnessCenter($fitnessCenter)
  {
    $this->fitnessCenter = $fitnessCenter;
  }
  /**
   * @return bool
   */
  public function getFitnessCenter()
  {
    return $this->fitnessCenter;
  }
  /**
   * Fitness center exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FITNESS_CENTER_EXCEPTION_* $fitnessCenterException
   */
  public function setFitnessCenterException($fitnessCenterException)
  {
    $this->fitnessCenterException = $fitnessCenterException;
  }
  /**
   * @return self::FITNESS_CENTER_EXCEPTION_*
   */
  public function getFitnessCenterException()
  {
    return $this->fitnessCenterException;
  }
  /**
   * Free fitness center. Guests may use the fitness center for free.
   *
   * @param bool $freeFitnessCenter
   */
  public function setFreeFitnessCenter($freeFitnessCenter)
  {
    $this->freeFitnessCenter = $freeFitnessCenter;
  }
  /**
   * @return bool
   */
  public function getFreeFitnessCenter()
  {
    return $this->freeFitnessCenter;
  }
  /**
   * Free fitness center exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_FITNESS_CENTER_EXCEPTION_* $freeFitnessCenterException
   */
  public function setFreeFitnessCenterException($freeFitnessCenterException)
  {
    $this->freeFitnessCenterException = $freeFitnessCenterException;
  }
  /**
   * @return self::FREE_FITNESS_CENTER_EXCEPTION_*
   */
  public function getFreeFitnessCenterException()
  {
    return $this->freeFitnessCenterException;
  }
  /**
   * Free weights. Individual handheld fitness equipment of varied weights used
   * for upper body strength training or bodybuilding. Also known as barbells,
   * dumbbells, or kettlebells. Often stored on a rack with the weights arranged
   * from light to heavy. Commonly found in a gym, fitness room, health center,
   * or health club.
   *
   * @param bool $freeWeights
   */
  public function setFreeWeights($freeWeights)
  {
    $this->freeWeights = $freeWeights;
  }
  /**
   * @return bool
   */
  public function getFreeWeights()
  {
    return $this->freeWeights;
  }
  /**
   * Free weights exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_WEIGHTS_EXCEPTION_* $freeWeightsException
   */
  public function setFreeWeightsException($freeWeightsException)
  {
    $this->freeWeightsException = $freeWeightsException;
  }
  /**
   * @return self::FREE_WEIGHTS_EXCEPTION_*
   */
  public function getFreeWeightsException()
  {
    return $this->freeWeightsException;
  }
  /**
   * Massage. A service provided by a trained massage therapist involving the
   * physical manipulation of a guest's muscles in order to achieve relaxation
   * or pain relief.
   *
   * @param bool $massage
   */
  public function setMassage($massage)
  {
    $this->massage = $massage;
  }
  /**
   * @return bool
   */
  public function getMassage()
  {
    return $this->massage;
  }
  /**
   * Massage exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MASSAGE_EXCEPTION_* $massageException
   */
  public function setMassageException($massageException)
  {
    $this->massageException = $massageException;
  }
  /**
   * @return self::MASSAGE_EXCEPTION_*
   */
  public function getMassageException()
  {
    return $this->massageException;
  }
  /**
   * Salon. A room at the hotel where professionals provide hair styling
   * services such as shampooing, blow drying, hair dos, hair cutting and hair
   * coloring. Also known as hairdresser or beauty salon.
   *
   * @param bool $salon
   */
  public function setSalon($salon)
  {
    $this->salon = $salon;
  }
  /**
   * @return bool
   */
  public function getSalon()
  {
    return $this->salon;
  }
  /**
   * Salon exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SALON_EXCEPTION_* $salonException
   */
  public function setSalonException($salonException)
  {
    $this->salonException = $salonException;
  }
  /**
   * @return self::SALON_EXCEPTION_*
   */
  public function getSalonException()
  {
    return $this->salonException;
  }
  /**
   * Sauna. A wood-paneled room heated to a high temperature where guests sit on
   * built-in wood benches for the purpose of perspiring and relaxing their
   * muscles. Can be dry or slightly wet heat. Not a steam room.
   *
   * @param bool $sauna
   */
  public function setSauna($sauna)
  {
    $this->sauna = $sauna;
  }
  /**
   * @return bool
   */
  public function getSauna()
  {
    return $this->sauna;
  }
  /**
   * Sauna exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SAUNA_EXCEPTION_* $saunaException
   */
  public function setSaunaException($saunaException)
  {
    $this->saunaException = $saunaException;
  }
  /**
   * @return self::SAUNA_EXCEPTION_*
   */
  public function getSaunaException()
  {
    return $this->saunaException;
  }
  /**
   * Spa. A designated area, room or building at the hotel offering health and
   * beauty treatment through such means as steam baths, exercise equipment, and
   * massage. May also offer facials, nail care, and hair care. Services are
   * usually available by appointment and for an additional fee. Does not apply
   * if hotel only offers a steam room; must offer other beauty and/or health
   * treatments as well.
   *
   * @param bool $spa
   */
  public function setSpa($spa)
  {
    $this->spa = $spa;
  }
  /**
   * @return bool
   */
  public function getSpa()
  {
    return $this->spa;
  }
  /**
   * Spa exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SPA_EXCEPTION_* $spaException
   */
  public function setSpaException($spaException)
  {
    $this->spaException = $spaException;
  }
  /**
   * @return self::SPA_EXCEPTION_*
   */
  public function getSpaException()
  {
    return $this->spaException;
  }
  /**
   * Treadmill. An electric stationary fitness machine that simulates a moving
   * path to promote walking or running within a range of user-controlled speeds
   * and inclines. Also known as running machine. Commonly found in a gym,
   * fitness room, health center, or health club.
   *
   * @param bool $treadmill
   */
  public function setTreadmill($treadmill)
  {
    $this->treadmill = $treadmill;
  }
  /**
   * @return bool
   */
  public function getTreadmill()
  {
    return $this->treadmill;
  }
  /**
   * Treadmill exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TREADMILL_EXCEPTION_* $treadmillException
   */
  public function setTreadmillException($treadmillException)
  {
    $this->treadmillException = $treadmillException;
  }
  /**
   * @return self::TREADMILL_EXCEPTION_*
   */
  public function getTreadmillException()
  {
    return $this->treadmillException;
  }
  /**
   * Weight machine. Non-electronic fitness equipment designed for the user to
   * target the exertion of different muscles. Usually incorporates a padded
   * seat, a stack of flat weights and various bars and pulleys. May be designed
   * for toning a specific part of the body or may involve different user-
   * controlled settings, hardware and pulleys so as to provide an overall
   * workout in one machine. Commonly found in a gym, fitness center, fitness
   * room, or health club.
   *
   * @param bool $weightMachine
   */
  public function setWeightMachine($weightMachine)
  {
    $this->weightMachine = $weightMachine;
  }
  /**
   * @return bool
   */
  public function getWeightMachine()
  {
    return $this->weightMachine;
  }
  /**
   * Weight machine exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WEIGHT_MACHINE_EXCEPTION_* $weightMachineException
   */
  public function setWeightMachineException($weightMachineException)
  {
    $this->weightMachineException = $weightMachineException;
  }
  /**
   * @return self::WEIGHT_MACHINE_EXCEPTION_*
   */
  public function getWeightMachineException()
  {
    return $this->weightMachineException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Wellness::class, 'Google_Service_MyBusinessLodging_Wellness');
