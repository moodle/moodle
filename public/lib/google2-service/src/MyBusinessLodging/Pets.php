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

class Pets extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CATS_ALLOWED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CATS_ALLOWED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CATS_ALLOWED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CATS_ALLOWED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DOGS_ALLOWED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DOGS_ALLOWED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DOGS_ALLOWED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DOGS_ALLOWED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PETS_ALLOWED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PETS_ALLOWED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PETS_ALLOWED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PETS_ALLOWED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PETS_ALLOWED_FREE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PETS_ALLOWED_FREE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PETS_ALLOWED_FREE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PETS_ALLOWED_FREE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Cats allowed. Domesticated felines are permitted at the property and
   * allowed to stay in the guest room of their owner. May or may not require a
   * fee.
   *
   * @var bool
   */
  public $catsAllowed;
  /**
   * Cats allowed exception.
   *
   * @var string
   */
  public $catsAllowedException;
  /**
   * Dogs allowed. Domesticated canines are permitted at the property and
   * allowed to stay in the guest room of their owner. May or may not require a
   * fee.
   *
   * @var bool
   */
  public $dogsAllowed;
  /**
   * Dogs allowed exception.
   *
   * @var string
   */
  public $dogsAllowedException;
  /**
   * Pets allowed. Household animals are allowed at the property and in the
   * specific guest room of their owner. May or may not include dogs, cats,
   * reptiles and/or fish. May or may not require a fee. Service animals are not
   * considered to be pets, so not governed by this policy.
   *
   * @var bool
   */
  public $petsAllowed;
  /**
   * Pets allowed exception.
   *
   * @var string
   */
  public $petsAllowedException;
  /**
   * Pets allowed free. Household animals are allowed at the property and in the
   * specific guest room of their owner for free. May or may not include dogs,
   * cats, reptiles, and/or fish.
   *
   * @var bool
   */
  public $petsAllowedFree;
  /**
   * Pets allowed free exception.
   *
   * @var string
   */
  public $petsAllowedFreeException;

  /**
   * Cats allowed. Domesticated felines are permitted at the property and
   * allowed to stay in the guest room of their owner. May or may not require a
   * fee.
   *
   * @param bool $catsAllowed
   */
  public function setCatsAllowed($catsAllowed)
  {
    $this->catsAllowed = $catsAllowed;
  }
  /**
   * @return bool
   */
  public function getCatsAllowed()
  {
    return $this->catsAllowed;
  }
  /**
   * Cats allowed exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CATS_ALLOWED_EXCEPTION_* $catsAllowedException
   */
  public function setCatsAllowedException($catsAllowedException)
  {
    $this->catsAllowedException = $catsAllowedException;
  }
  /**
   * @return self::CATS_ALLOWED_EXCEPTION_*
   */
  public function getCatsAllowedException()
  {
    return $this->catsAllowedException;
  }
  /**
   * Dogs allowed. Domesticated canines are permitted at the property and
   * allowed to stay in the guest room of their owner. May or may not require a
   * fee.
   *
   * @param bool $dogsAllowed
   */
  public function setDogsAllowed($dogsAllowed)
  {
    $this->dogsAllowed = $dogsAllowed;
  }
  /**
   * @return bool
   */
  public function getDogsAllowed()
  {
    return $this->dogsAllowed;
  }
  /**
   * Dogs allowed exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DOGS_ALLOWED_EXCEPTION_* $dogsAllowedException
   */
  public function setDogsAllowedException($dogsAllowedException)
  {
    $this->dogsAllowedException = $dogsAllowedException;
  }
  /**
   * @return self::DOGS_ALLOWED_EXCEPTION_*
   */
  public function getDogsAllowedException()
  {
    return $this->dogsAllowedException;
  }
  /**
   * Pets allowed. Household animals are allowed at the property and in the
   * specific guest room of their owner. May or may not include dogs, cats,
   * reptiles and/or fish. May or may not require a fee. Service animals are not
   * considered to be pets, so not governed by this policy.
   *
   * @param bool $petsAllowed
   */
  public function setPetsAllowed($petsAllowed)
  {
    $this->petsAllowed = $petsAllowed;
  }
  /**
   * @return bool
   */
  public function getPetsAllowed()
  {
    return $this->petsAllowed;
  }
  /**
   * Pets allowed exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PETS_ALLOWED_EXCEPTION_* $petsAllowedException
   */
  public function setPetsAllowedException($petsAllowedException)
  {
    $this->petsAllowedException = $petsAllowedException;
  }
  /**
   * @return self::PETS_ALLOWED_EXCEPTION_*
   */
  public function getPetsAllowedException()
  {
    return $this->petsAllowedException;
  }
  /**
   * Pets allowed free. Household animals are allowed at the property and in the
   * specific guest room of their owner for free. May or may not include dogs,
   * cats, reptiles, and/or fish.
   *
   * @param bool $petsAllowedFree
   */
  public function setPetsAllowedFree($petsAllowedFree)
  {
    $this->petsAllowedFree = $petsAllowedFree;
  }
  /**
   * @return bool
   */
  public function getPetsAllowedFree()
  {
    return $this->petsAllowedFree;
  }
  /**
   * Pets allowed free exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PETS_ALLOWED_FREE_EXCEPTION_* $petsAllowedFreeException
   */
  public function setPetsAllowedFreeException($petsAllowedFreeException)
  {
    $this->petsAllowedFreeException = $petsAllowedFreeException;
  }
  /**
   * @return self::PETS_ALLOWED_FREE_EXCEPTION_*
   */
  public function getPetsAllowedFreeException()
  {
    return $this->petsAllowedFreeException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pets::class, 'Google_Service_MyBusinessLodging_Pets');
