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

class EcoCertification extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const AWARDED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const AWARDED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const AWARDED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const AWARDED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default EcoCertificate. Do not use.
   */
  public const ECO_CERTIFICATE_ECO_CERTIFICATE_UNSPECIFIED = 'ECO_CERTIFICATE_UNSPECIFIED';
  /**
   * ISO14001.
   */
  public const ECO_CERTIFICATE_ISO14001 = 'ISO14001';
  /**
   * ISO50001.
   */
  public const ECO_CERTIFICATE_ISO50001 = 'ISO50001';
  /**
   * Asian Ecotourism Standard for Accommodations (AESA).
   */
  public const ECO_CERTIFICATE_ASIAN_ECOTOURISM = 'ASIAN_ECOTOURISM';
  /**
   * Biosphere Responsible Tourism Standard.
   */
  public const ECO_CERTIFICATE_BIOSPHERE_RESPOSNIBLE_TOURISM = 'BIOSPHERE_RESPOSNIBLE_TOURISM';
  /**
   * Bureau Veritas.
   */
  public const ECO_CERTIFICATE_BUREAU_VERITAS = 'BUREAU_VERITAS';
  /**
   * Control Union.
   */
  public const ECO_CERTIFICATE_CONTROL_UNION = 'CONTROL_UNION';
  /**
   * EarthCheck.
   */
  public const ECO_CERTIFICATE_EARTHCHECK = 'EARTHCHECK';
  /**
   * Eco-Certification Malta Standard.
   */
  public const ECO_CERTIFICATE_ECO_CERTIFICATION_MALTA = 'ECO_CERTIFICATION_MALTA';
  /**
   * Ecotourism Australia's ECO Certification Standard.
   */
  public const ECO_CERTIFICATE_ECOTOURISM_AUSTRALIAS_ECO = 'ECOTOURISM_AUSTRALIAS_ECO';
  /**
   * GREAT Green Deal Certification.
   */
  public const ECO_CERTIFICATE_GREAT_GREEN_DEAL = 'GREAT_GREEN_DEAL';
  /**
   * Green Globe.
   */
  public const ECO_CERTIFICATE_GREEN_GLOBE = 'GREEN_GLOBE';
  /**
   * Green Growth 2050 Standard.
   */
  public const ECO_CERTIFICATE_GREEN_GROWTH2050 = 'GREEN_GROWTH2050';
  /**
   * Green Key.
   */
  public const ECO_CERTIFICATE_GREEN_KEY = 'GREEN_KEY';
  /**
   * Geen Key Eco Rating.
   */
  public const ECO_CERTIFICATE_GREEN_KEY_ECO_RATING = 'GREEN_KEY_ECO_RATING';
  /**
   * Green Seal.
   */
  public const ECO_CERTIFICATE_GREEN_SEAL = 'GREEN_SEAL';
  /**
   * Green Star Hotel Standard.
   */
  public const ECO_CERTIFICATE_GREEN_STAR = 'GREEN_STAR';
  /**
   * Green Tourism Active Standard.
   */
  public const ECO_CERTIFICATE_GREEN_TOURISM_ACTIVE = 'GREEN_TOURISM_ACTIVE';
  /**
   * Hilton LightStay.
   */
  public const ECO_CERTIFICATE_HILTON_LIGHTSTAY = 'HILTON_LIGHTSTAY';
  /**
   * Hostelling International's Quality and Sustainability Standard.
   */
  public const ECO_CERTIFICATE_HOSTELLING_INTERNATIONALS_QUALITY_AND_SUSTAINABILITY = 'HOSTELLING_INTERNATIONALS_QUALITY_AND_SUSTAINABILITY';
  /**
   * Hoteles más Verdes (AHT) Standard.
   */
  public const ECO_CERTIFICATE_HOTELES_MAS_VERDES = 'HOTELES_MAS_VERDES';
  /**
   * Nordic Swan Ecolabel.
   */
  public const ECO_CERTIFICATE_NORDIC_SWAN_ECOLABEL = 'NORDIC_SWAN_ECOLABEL';
  /**
   * Preferred by Nature Sustainable Tourism Standard for Accommodation.
   */
  public const ECO_CERTIFICATE_PREFERRED_BY_NATURE_SUSTAINABLE_TOURISM = 'PREFERRED_BY_NATURE_SUSTAINABLE_TOURISM';
  /**
   * Sustainable Travel Ireland – GSTC Industry Criteria.
   */
  public const ECO_CERTIFICATE_SUSTAINABLE_TRAVEL_IRELAND = 'SUSTAINABLE_TRAVEL_IRELAND';
  /**
   * TOFTigers Initiative's Pug Standard.
   */
  public const ECO_CERTIFICATE_TOF_TIGERS_INITITIVES_PUG = 'TOF_TIGERS_INITITIVES_PUG';
  /**
   * Travelife Standard for Hotels & Accommodations.
   */
  public const ECO_CERTIFICATE_TRAVELIFE = 'TRAVELIFE';
  /**
   * United Certification Systems Limited.
   */
  public const ECO_CERTIFICATE_UNITED_CERTIFICATION_SYSTEMS_LIMITED = 'UNITED_CERTIFICATION_SYSTEMS_LIMITED';
  /**
   * Vireo Srl.
   */
  public const ECO_CERTIFICATE_VIREO_SRL = 'VIREO_SRL';
  /**
   * Whether the eco certificate was awarded or not.
   *
   * @var bool
   */
  public $awarded;
  /**
   * Awarded exception.
   *
   * @var string
   */
  public $awardedException;
  /**
   * Required. The eco certificate.
   *
   * @var string
   */
  public $ecoCertificate;

  /**
   * Whether the eco certificate was awarded or not.
   *
   * @param bool $awarded
   */
  public function setAwarded($awarded)
  {
    $this->awarded = $awarded;
  }
  /**
   * @return bool
   */
  public function getAwarded()
  {
    return $this->awarded;
  }
  /**
   * Awarded exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::AWARDED_EXCEPTION_* $awardedException
   */
  public function setAwardedException($awardedException)
  {
    $this->awardedException = $awardedException;
  }
  /**
   * @return self::AWARDED_EXCEPTION_*
   */
  public function getAwardedException()
  {
    return $this->awardedException;
  }
  /**
   * Required. The eco certificate.
   *
   * Accepted values: ECO_CERTIFICATE_UNSPECIFIED, ISO14001, ISO50001,
   * ASIAN_ECOTOURISM, BIOSPHERE_RESPOSNIBLE_TOURISM, BUREAU_VERITAS,
   * CONTROL_UNION, EARTHCHECK, ECO_CERTIFICATION_MALTA,
   * ECOTOURISM_AUSTRALIAS_ECO, GREAT_GREEN_DEAL, GREEN_GLOBE, GREEN_GROWTH2050,
   * GREEN_KEY, GREEN_KEY_ECO_RATING, GREEN_SEAL, GREEN_STAR,
   * GREEN_TOURISM_ACTIVE, HILTON_LIGHTSTAY,
   * HOSTELLING_INTERNATIONALS_QUALITY_AND_SUSTAINABILITY, HOTELES_MAS_VERDES,
   * NORDIC_SWAN_ECOLABEL, PREFERRED_BY_NATURE_SUSTAINABLE_TOURISM,
   * SUSTAINABLE_TRAVEL_IRELAND, TOF_TIGERS_INITITIVES_PUG, TRAVELIFE,
   * UNITED_CERTIFICATION_SYSTEMS_LIMITED, VIREO_SRL
   *
   * @param self::ECO_CERTIFICATE_* $ecoCertificate
   */
  public function setEcoCertificate($ecoCertificate)
  {
    $this->ecoCertificate = $ecoCertificate;
  }
  /**
   * @return self::ECO_CERTIFICATE_*
   */
  public function getEcoCertificate()
  {
    return $this->ecoCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EcoCertification::class, 'Google_Service_MyBusinessLodging_EcoCertification');
