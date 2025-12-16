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

class Families extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BABYSITTING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BABYSITTING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BABYSITTING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BABYSITTING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KIDS_ACTIVITIES_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KIDS_ACTIVITIES_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KIDS_ACTIVITIES_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KIDS_ACTIVITIES_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KIDS_CLUB_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KIDS_CLUB_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KIDS_CLUB_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KIDS_CLUB_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KIDS_FRIENDLY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KIDS_FRIENDLY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KIDS_FRIENDLY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KIDS_FRIENDLY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Babysitting. Child care that is offered by hotel staffers or coordinated by
   * hotel staffers with local child care professionals. Can be free or for a
   * fee.
   *
   * @var bool
   */
  public $babysitting;
  /**
   * Babysitting exception.
   *
   * @var string
   */
  public $babysittingException;
  /**
   * Kids activities. Recreational options such as sports, films, crafts and
   * games designed for the enjoyment of children and offered at the hotel. May
   * or may not be supervised. May or may not be at a designated time or place.
   * Cab be free or for a fee.
   *
   * @var bool
   */
  public $kidsActivities;
  /**
   * Kids activities exception.
   *
   * @var string
   */
  public $kidsActivitiesException;
  /**
   * Kids club. An organized program of group activities held at the hotel and
   * designed for the enjoyment of children. Facilitated by hotel staff (or
   * staff procured by the hotel) in an area(s) designated for the purpose of
   * entertaining children without their parents. May include games, outings,
   * water sports, team sports, arts and crafts, and films. Usually has set
   * hours. Can be free or for a fee. Also known as Kids Camp or Kids program.
   *
   * @var bool
   */
  public $kidsClub;
  /**
   * Kids club exception.
   *
   * @var string
   */
  public $kidsClubException;
  /**
   * Kids friendly. The hotel has one or more special features for families with
   * children, such as reduced rates, child-sized beds, kids' club, babysitting
   * service, or suitable place to play on premises.
   *
   * @var bool
   */
  public $kidsFriendly;
  /**
   * Kids friendly exception.
   *
   * @var string
   */
  public $kidsFriendlyException;

  /**
   * Babysitting. Child care that is offered by hotel staffers or coordinated by
   * hotel staffers with local child care professionals. Can be free or for a
   * fee.
   *
   * @param bool $babysitting
   */
  public function setBabysitting($babysitting)
  {
    $this->babysitting = $babysitting;
  }
  /**
   * @return bool
   */
  public function getBabysitting()
  {
    return $this->babysitting;
  }
  /**
   * Babysitting exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BABYSITTING_EXCEPTION_* $babysittingException
   */
  public function setBabysittingException($babysittingException)
  {
    $this->babysittingException = $babysittingException;
  }
  /**
   * @return self::BABYSITTING_EXCEPTION_*
   */
  public function getBabysittingException()
  {
    return $this->babysittingException;
  }
  /**
   * Kids activities. Recreational options such as sports, films, crafts and
   * games designed for the enjoyment of children and offered at the hotel. May
   * or may not be supervised. May or may not be at a designated time or place.
   * Cab be free or for a fee.
   *
   * @param bool $kidsActivities
   */
  public function setKidsActivities($kidsActivities)
  {
    $this->kidsActivities = $kidsActivities;
  }
  /**
   * @return bool
   */
  public function getKidsActivities()
  {
    return $this->kidsActivities;
  }
  /**
   * Kids activities exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KIDS_ACTIVITIES_EXCEPTION_* $kidsActivitiesException
   */
  public function setKidsActivitiesException($kidsActivitiesException)
  {
    $this->kidsActivitiesException = $kidsActivitiesException;
  }
  /**
   * @return self::KIDS_ACTIVITIES_EXCEPTION_*
   */
  public function getKidsActivitiesException()
  {
    return $this->kidsActivitiesException;
  }
  /**
   * Kids club. An organized program of group activities held at the hotel and
   * designed for the enjoyment of children. Facilitated by hotel staff (or
   * staff procured by the hotel) in an area(s) designated for the purpose of
   * entertaining children without their parents. May include games, outings,
   * water sports, team sports, arts and crafts, and films. Usually has set
   * hours. Can be free or for a fee. Also known as Kids Camp or Kids program.
   *
   * @param bool $kidsClub
   */
  public function setKidsClub($kidsClub)
  {
    $this->kidsClub = $kidsClub;
  }
  /**
   * @return bool
   */
  public function getKidsClub()
  {
    return $this->kidsClub;
  }
  /**
   * Kids club exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KIDS_CLUB_EXCEPTION_* $kidsClubException
   */
  public function setKidsClubException($kidsClubException)
  {
    $this->kidsClubException = $kidsClubException;
  }
  /**
   * @return self::KIDS_CLUB_EXCEPTION_*
   */
  public function getKidsClubException()
  {
    return $this->kidsClubException;
  }
  /**
   * Kids friendly. The hotel has one or more special features for families with
   * children, such as reduced rates, child-sized beds, kids' club, babysitting
   * service, or suitable place to play on premises.
   *
   * @param bool $kidsFriendly
   */
  public function setKidsFriendly($kidsFriendly)
  {
    $this->kidsFriendly = $kidsFriendly;
  }
  /**
   * @return bool
   */
  public function getKidsFriendly()
  {
    return $this->kidsFriendly;
  }
  /**
   * Kids friendly exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KIDS_FRIENDLY_EXCEPTION_* $kidsFriendlyException
   */
  public function setKidsFriendlyException($kidsFriendlyException)
  {
    $this->kidsFriendlyException = $kidsFriendlyException;
  }
  /**
   * @return self::KIDS_FRIENDLY_EXCEPTION_*
   */
  public function getKidsFriendlyException()
  {
    return $this->kidsFriendlyException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Families::class, 'Google_Service_MyBusinessLodging_Families');
