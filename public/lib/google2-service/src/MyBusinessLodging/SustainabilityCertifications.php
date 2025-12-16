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

class SustainabilityCertifications extends \Google\Collection
{
  /**
   * Default BreeamCertification. Do not use.
   */
  public const BREEAM_CERTIFICATION_BREEAM_CERTIFICATION_UNSPECIFIED = 'BREEAM_CERTIFICATION_UNSPECIFIED';
  /**
   * Not certified.
   */
  public const BREEAM_CERTIFICATION_NO_BREEAM_CERTIFICATION = 'NO_BREEAM_CERTIFICATION';
  /**
   * BREEAM Pass.
   */
  public const BREEAM_CERTIFICATION_BREEAM_PASS = 'BREEAM_PASS';
  /**
   * BREEAM Good.
   */
  public const BREEAM_CERTIFICATION_BREEAM_GOOD = 'BREEAM_GOOD';
  /**
   * BREEAM Very Good.
   */
  public const BREEAM_CERTIFICATION_BREEAM_VERY_GOOD = 'BREEAM_VERY_GOOD';
  /**
   * BREEAM Excellent.
   */
  public const BREEAM_CERTIFICATION_BREEAM_EXCELLENT = 'BREEAM_EXCELLENT';
  /**
   * BREEAM Outstanding.
   */
  public const BREEAM_CERTIFICATION_BREEAM_OUTSTANDING = 'BREEAM_OUTSTANDING';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BREEAM_CERTIFICATION_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BREEAM_CERTIFICATION_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BREEAM_CERTIFICATION_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BREEAM_CERTIFICATION_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default LeedCertification. Do not use.
   */
  public const LEED_CERTIFICATION_LEED_CERTIFICATION_UNSPECIFIED = 'LEED_CERTIFICATION_UNSPECIFIED';
  /**
   * Not certified.
   */
  public const LEED_CERTIFICATION_NO_LEED_CERTIFICATION = 'NO_LEED_CERTIFICATION';
  /**
   * LEED Certified.
   */
  public const LEED_CERTIFICATION_LEED_CERTIFIED = 'LEED_CERTIFIED';
  /**
   * LEED Silver.
   */
  public const LEED_CERTIFICATION_LEED_SILVER = 'LEED_SILVER';
  /**
   * LEED Gold.
   */
  public const LEED_CERTIFICATION_LEED_GOLD = 'LEED_GOLD';
  /**
   * LEED Platinum.
   */
  public const LEED_CERTIFICATION_LEED_PLATINUM = 'LEED_PLATINUM';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LEED_CERTIFICATION_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LEED_CERTIFICATION_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LEED_CERTIFICATION_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LEED_CERTIFICATION_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  protected $collection_key = 'ecoCertifications';
  /**
   * BREEAM certification.
   *
   * @var string
   */
  public $breeamCertification;
  /**
   * BREEAM certification exception.
   *
   * @var string
   */
  public $breeamCertificationException;
  protected $ecoCertificationsType = EcoCertification::class;
  protected $ecoCertificationsDataType = 'array';
  /**
   * LEED certification.
   *
   * @var string
   */
  public $leedCertification;
  /**
   * LEED certification exception.
   *
   * @var string
   */
  public $leedCertificationException;

  /**
   * BREEAM certification.
   *
   * Accepted values: BREEAM_CERTIFICATION_UNSPECIFIED, NO_BREEAM_CERTIFICATION,
   * BREEAM_PASS, BREEAM_GOOD, BREEAM_VERY_GOOD, BREEAM_EXCELLENT,
   * BREEAM_OUTSTANDING
   *
   * @param self::BREEAM_CERTIFICATION_* $breeamCertification
   */
  public function setBreeamCertification($breeamCertification)
  {
    $this->breeamCertification = $breeamCertification;
  }
  /**
   * @return self::BREEAM_CERTIFICATION_*
   */
  public function getBreeamCertification()
  {
    return $this->breeamCertification;
  }
  /**
   * BREEAM certification exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BREEAM_CERTIFICATION_EXCEPTION_* $breeamCertificationException
   */
  public function setBreeamCertificationException($breeamCertificationException)
  {
    $this->breeamCertificationException = $breeamCertificationException;
  }
  /**
   * @return self::BREEAM_CERTIFICATION_EXCEPTION_*
   */
  public function getBreeamCertificationException()
  {
    return $this->breeamCertificationException;
  }
  /**
   * The eco certificates awarded to the hotel.
   *
   * @param EcoCertification[] $ecoCertifications
   */
  public function setEcoCertifications($ecoCertifications)
  {
    $this->ecoCertifications = $ecoCertifications;
  }
  /**
   * @return EcoCertification[]
   */
  public function getEcoCertifications()
  {
    return $this->ecoCertifications;
  }
  /**
   * LEED certification.
   *
   * Accepted values: LEED_CERTIFICATION_UNSPECIFIED, NO_LEED_CERTIFICATION,
   * LEED_CERTIFIED, LEED_SILVER, LEED_GOLD, LEED_PLATINUM
   *
   * @param self::LEED_CERTIFICATION_* $leedCertification
   */
  public function setLeedCertification($leedCertification)
  {
    $this->leedCertification = $leedCertification;
  }
  /**
   * @return self::LEED_CERTIFICATION_*
   */
  public function getLeedCertification()
  {
    return $this->leedCertification;
  }
  /**
   * LEED certification exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LEED_CERTIFICATION_EXCEPTION_* $leedCertificationException
   */
  public function setLeedCertificationException($leedCertificationException)
  {
    $this->leedCertificationException = $leedCertificationException;
  }
  /**
   * @return self::LEED_CERTIFICATION_EXCEPTION_*
   */
  public function getLeedCertificationException()
  {
    return $this->leedCertificationException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SustainabilityCertifications::class, 'Google_Service_MyBusinessLodging_SustainabilityCertifications');
