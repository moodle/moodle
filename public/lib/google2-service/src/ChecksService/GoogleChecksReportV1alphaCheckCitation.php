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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaCheckCitation extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_CITATION_TYPE_UNSPECIFIED = 'CITATION_TYPE_UNSPECIFIED';
  /**
   * Children's Online Privacy Protection Act.
   */
  public const TYPE_COPPA = 'COPPA';
  /**
   * General Data Protection Regulation.
   */
  public const TYPE_GDPR = 'GDPR';
  /**
   * Family Educational Rights and Privacy Act.
   */
  public const TYPE_FERPA = 'FERPA';
  /**
   * The California Online Privacy Protection Act.
   */
  public const TYPE_CAL_OPPA = 'CAL_OPPA';
  /**
   * California Consumer Privacy Act.
   */
  public const TYPE_CCPA = 'CCPA';
  /**
   * Student Online Personal Information Protection Act.
   */
  public const TYPE_SOPIPA = 'SOPIPA';
  /**
   * Lei Geral de Proteção de Dados.
   */
  public const TYPE_LGPD = 'LGPD';
  /**
   * California Consumer Privacy Act.
   */
  public const TYPE_CPRA = 'CPRA';
  /**
   * Virginia Consumer Data Protection Act.
   */
  public const TYPE_VCDPA = 'VCDPA';
  /**
   * Google Play Policy.
   */
  public const TYPE_GOOGLE_PLAY_POLICY = 'GOOGLE_PLAY_POLICY';
  /**
   * App Store Policy.
   */
  public const TYPE_APP_STORE_POLICY = 'APP_STORE_POLICY';
  /**
   * Colorado Privacy Act.
   */
  public const TYPE_CPA = 'CPA';
  /**
   * Connecticut Data Privacy Act.
   */
  public const TYPE_CTDPA = 'CTDPA';
  /**
   * Utah Consumer Privacy Act.
   */
  public const TYPE_UCPA = 'UCPA';
  /**
   * Personal Information Protection and Electronic Documents Act.
   */
  public const TYPE_PIPEDA = 'PIPEDA';
  /**
   * Alberta (Canada) Personal Information Protection Act.
   */
  public const TYPE_ALBERTA_PIPA = 'ALBERTA_PIPA';
  /**
   * Quebec: Act Respecting the Protection of Personal Information in the
   * Private Sector.
   */
  public const TYPE_QUEBEC_ACT = 'QUEBEC_ACT';
  /**
   * Quebec Bill 64: An Act to Modernize Legislative Provisions as Regards the
   * Protection of Personal Information.
   */
  public const TYPE_QUEBEC_BILL_64 = 'QUEBEC_BILL_64';
  /**
   * China Personal Information Protection Law.
   */
  public const TYPE_CHINA_PIPL = 'CHINA_PIPL';
  /**
   * South Korea Personal Information Protection Act.
   */
  public const TYPE_SOUTH_KOREA_PIPA = 'SOUTH_KOREA_PIPA';
  /**
   * South Africa Protection of Personal Information Act.
   */
  public const TYPE_SOUTH_AFRICA_POPIA = 'SOUTH_AFRICA_POPIA';
  /**
   * Japan Act on the Protection of Personal Information.
   */
  public const TYPE_JAPAN_APPI = 'JAPAN_APPI';
  /**
   * India: The Digital Personal Data Protection Act, 2023.
   */
  public const TYPE_INDIA_DPDPA = 'INDIA_DPDPA';
  /**
   * Oregon Consumer Privacy Act.
   */
  public const TYPE_OCPA = 'OCPA';
  /**
   * Texas Data Privacy and Security Act.
   */
  public const TYPE_TDPSA = 'TDPSA';
  /**
   * Montana Consumer Data Privacy Act.
   */
  public const TYPE_MCDPA = 'MCDPA';
  /**
   * Citation type.
   *
   * @var string
   */
  public $type;

  /**
   * Citation type.
   *
   * Accepted values: CITATION_TYPE_UNSPECIFIED, COPPA, GDPR, FERPA, CAL_OPPA,
   * CCPA, SOPIPA, LGPD, CPRA, VCDPA, GOOGLE_PLAY_POLICY, APP_STORE_POLICY, CPA,
   * CTDPA, UCPA, PIPEDA, ALBERTA_PIPA, QUEBEC_ACT, QUEBEC_BILL_64, CHINA_PIPL,
   * SOUTH_KOREA_PIPA, SOUTH_AFRICA_POPIA, JAPAN_APPI, INDIA_DPDPA, OCPA, TDPSA,
   * MCDPA
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheckCitation::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheckCitation');
