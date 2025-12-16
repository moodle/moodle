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

class EnhancedCleaning extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Commercial-grade disinfectant used to clean the property.
   *
   * @var bool
   */
  public $commercialGradeDisinfectantCleaning;
  /**
   * Commercial grade disinfectant cleaning exception.
   *
   * @var string
   */
  public $commercialGradeDisinfectantCleaningException;
  /**
   * Enhanced cleaning of common areas.
   *
   * @var bool
   */
  public $commonAreasEnhancedCleaning;
  /**
   * Common areas enhanced cleaning exception.
   *
   * @var string
   */
  public $commonAreasEnhancedCleaningException;
  /**
   * Employees trained in COVID-19 cleaning procedures.
   *
   * @var bool
   */
  public $employeesTrainedCleaningProcedures;
  /**
   * Employees trained cleaning procedures exception.
   *
   * @var string
   */
  public $employeesTrainedCleaningProceduresException;
  /**
   * Employees trained in thorough hand-washing.
   *
   * @var bool
   */
  public $employeesTrainedThoroughHandWashing;
  /**
   * Employees trained thorough hand washing exception.
   *
   * @var string
   */
  public $employeesTrainedThoroughHandWashingException;
  /**
   * Employees wear masks, face shields, and/or gloves.
   *
   * @var bool
   */
  public $employeesWearProtectiveEquipment;
  /**
   * Employees wear protective equipment exception.
   *
   * @var string
   */
  public $employeesWearProtectiveEquipmentException;
  /**
   * Enhanced cleaning of guest rooms.
   *
   * @var bool
   */
  public $guestRoomsEnhancedCleaning;
  /**
   * Guest rooms enhanced cleaning exception.
   *
   * @var string
   */
  public $guestRoomsEnhancedCleaningException;

  /**
   * Commercial-grade disinfectant used to clean the property.
   *
   * @param bool $commercialGradeDisinfectantCleaning
   */
  public function setCommercialGradeDisinfectantCleaning($commercialGradeDisinfectantCleaning)
  {
    $this->commercialGradeDisinfectantCleaning = $commercialGradeDisinfectantCleaning;
  }
  /**
   * @return bool
   */
  public function getCommercialGradeDisinfectantCleaning()
  {
    return $this->commercialGradeDisinfectantCleaning;
  }
  /**
   * Commercial grade disinfectant cleaning exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_* $commercialGradeDisinfectantCleaningException
   */
  public function setCommercialGradeDisinfectantCleaningException($commercialGradeDisinfectantCleaningException)
  {
    $this->commercialGradeDisinfectantCleaningException = $commercialGradeDisinfectantCleaningException;
  }
  /**
   * @return self::COMMERCIAL_GRADE_DISINFECTANT_CLEANING_EXCEPTION_*
   */
  public function getCommercialGradeDisinfectantCleaningException()
  {
    return $this->commercialGradeDisinfectantCleaningException;
  }
  /**
   * Enhanced cleaning of common areas.
   *
   * @param bool $commonAreasEnhancedCleaning
   */
  public function setCommonAreasEnhancedCleaning($commonAreasEnhancedCleaning)
  {
    $this->commonAreasEnhancedCleaning = $commonAreasEnhancedCleaning;
  }
  /**
   * @return bool
   */
  public function getCommonAreasEnhancedCleaning()
  {
    return $this->commonAreasEnhancedCleaning;
  }
  /**
   * Common areas enhanced cleaning exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_* $commonAreasEnhancedCleaningException
   */
  public function setCommonAreasEnhancedCleaningException($commonAreasEnhancedCleaningException)
  {
    $this->commonAreasEnhancedCleaningException = $commonAreasEnhancedCleaningException;
  }
  /**
   * @return self::COMMON_AREAS_ENHANCED_CLEANING_EXCEPTION_*
   */
  public function getCommonAreasEnhancedCleaningException()
  {
    return $this->commonAreasEnhancedCleaningException;
  }
  /**
   * Employees trained in COVID-19 cleaning procedures.
   *
   * @param bool $employeesTrainedCleaningProcedures
   */
  public function setEmployeesTrainedCleaningProcedures($employeesTrainedCleaningProcedures)
  {
    $this->employeesTrainedCleaningProcedures = $employeesTrainedCleaningProcedures;
  }
  /**
   * @return bool
   */
  public function getEmployeesTrainedCleaningProcedures()
  {
    return $this->employeesTrainedCleaningProcedures;
  }
  /**
   * Employees trained cleaning procedures exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_* $employeesTrainedCleaningProceduresException
   */
  public function setEmployeesTrainedCleaningProceduresException($employeesTrainedCleaningProceduresException)
  {
    $this->employeesTrainedCleaningProceduresException = $employeesTrainedCleaningProceduresException;
  }
  /**
   * @return self::EMPLOYEES_TRAINED_CLEANING_PROCEDURES_EXCEPTION_*
   */
  public function getEmployeesTrainedCleaningProceduresException()
  {
    return $this->employeesTrainedCleaningProceduresException;
  }
  /**
   * Employees trained in thorough hand-washing.
   *
   * @param bool $employeesTrainedThoroughHandWashing
   */
  public function setEmployeesTrainedThoroughHandWashing($employeesTrainedThoroughHandWashing)
  {
    $this->employeesTrainedThoroughHandWashing = $employeesTrainedThoroughHandWashing;
  }
  /**
   * @return bool
   */
  public function getEmployeesTrainedThoroughHandWashing()
  {
    return $this->employeesTrainedThoroughHandWashing;
  }
  /**
   * Employees trained thorough hand washing exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_* $employeesTrainedThoroughHandWashingException
   */
  public function setEmployeesTrainedThoroughHandWashingException($employeesTrainedThoroughHandWashingException)
  {
    $this->employeesTrainedThoroughHandWashingException = $employeesTrainedThoroughHandWashingException;
  }
  /**
   * @return self::EMPLOYEES_TRAINED_THOROUGH_HAND_WASHING_EXCEPTION_*
   */
  public function getEmployeesTrainedThoroughHandWashingException()
  {
    return $this->employeesTrainedThoroughHandWashingException;
  }
  /**
   * Employees wear masks, face shields, and/or gloves.
   *
   * @param bool $employeesWearProtectiveEquipment
   */
  public function setEmployeesWearProtectiveEquipment($employeesWearProtectiveEquipment)
  {
    $this->employeesWearProtectiveEquipment = $employeesWearProtectiveEquipment;
  }
  /**
   * @return bool
   */
  public function getEmployeesWearProtectiveEquipment()
  {
    return $this->employeesWearProtectiveEquipment;
  }
  /**
   * Employees wear protective equipment exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_* $employeesWearProtectiveEquipmentException
   */
  public function setEmployeesWearProtectiveEquipmentException($employeesWearProtectiveEquipmentException)
  {
    $this->employeesWearProtectiveEquipmentException = $employeesWearProtectiveEquipmentException;
  }
  /**
   * @return self::EMPLOYEES_WEAR_PROTECTIVE_EQUIPMENT_EXCEPTION_*
   */
  public function getEmployeesWearProtectiveEquipmentException()
  {
    return $this->employeesWearProtectiveEquipmentException;
  }
  /**
   * Enhanced cleaning of guest rooms.
   *
   * @param bool $guestRoomsEnhancedCleaning
   */
  public function setGuestRoomsEnhancedCleaning($guestRoomsEnhancedCleaning)
  {
    $this->guestRoomsEnhancedCleaning = $guestRoomsEnhancedCleaning;
  }
  /**
   * @return bool
   */
  public function getGuestRoomsEnhancedCleaning()
  {
    return $this->guestRoomsEnhancedCleaning;
  }
  /**
   * Guest rooms enhanced cleaning exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_* $guestRoomsEnhancedCleaningException
   */
  public function setGuestRoomsEnhancedCleaningException($guestRoomsEnhancedCleaningException)
  {
    $this->guestRoomsEnhancedCleaningException = $guestRoomsEnhancedCleaningException;
  }
  /**
   * @return self::GUEST_ROOMS_ENHANCED_CLEANING_EXCEPTION_*
   */
  public function getGuestRoomsEnhancedCleaningException()
  {
    return $this->guestRoomsEnhancedCleaningException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnhancedCleaning::class, 'Google_Service_MyBusinessLodging_EnhancedCleaning');
