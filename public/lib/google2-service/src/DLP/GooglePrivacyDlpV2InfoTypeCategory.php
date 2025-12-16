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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2InfoTypeCategory extends \Google\Model
{
  /**
   * Unused industry
   */
  public const INDUSTRY_CATEGORY_INDUSTRY_UNSPECIFIED = 'INDUSTRY_UNSPECIFIED';
  /**
   * The infoType is typically used in the finance industry.
   */
  public const INDUSTRY_CATEGORY_FINANCE = 'FINANCE';
  /**
   * The infoType is typically used in the health industry.
   */
  public const INDUSTRY_CATEGORY_HEALTH = 'HEALTH';
  /**
   * The infoType is typically used in the telecommunications industry.
   */
  public const INDUSTRY_CATEGORY_TELECOMMUNICATIONS = 'TELECOMMUNICATIONS';
  /**
   * Unused location
   */
  public const LOCATION_CATEGORY_LOCATION_UNSPECIFIED = 'LOCATION_UNSPECIFIED';
  /**
   * The infoType is not issued by or tied to a specific region, but is used
   * almost everywhere.
   */
  public const LOCATION_CATEGORY_GLOBAL = 'GLOBAL';
  /**
   * The infoType is typically used in Argentina.
   */
  public const LOCATION_CATEGORY_ARGENTINA = 'ARGENTINA';
  /**
   * The infoType is typically used in Armenia.
   */
  public const LOCATION_CATEGORY_ARMENIA = 'ARMENIA';
  /**
   * The infoType is typically used in Australia.
   */
  public const LOCATION_CATEGORY_AUSTRALIA = 'AUSTRALIA';
  /**
   * The infoType is typically used in Austria.
   */
  public const LOCATION_CATEGORY_AUSTRIA = 'AUSTRIA';
  /**
   * The infoType is typically used in Azerbaijan.
   */
  public const LOCATION_CATEGORY_AZERBAIJAN = 'AZERBAIJAN';
  /**
   * The infoType is typically used in Belarus.
   */
  public const LOCATION_CATEGORY_BELARUS = 'BELARUS';
  /**
   * The infoType is typically used in Belgium.
   */
  public const LOCATION_CATEGORY_BELGIUM = 'BELGIUM';
  /**
   * The infoType is typically used in Brazil.
   */
  public const LOCATION_CATEGORY_BRAZIL = 'BRAZIL';
  /**
   * The infoType is typically used in Canada.
   */
  public const LOCATION_CATEGORY_CANADA = 'CANADA';
  /**
   * The infoType is typically used in Chile.
   */
  public const LOCATION_CATEGORY_CHILE = 'CHILE';
  /**
   * The infoType is typically used in China.
   */
  public const LOCATION_CATEGORY_CHINA = 'CHINA';
  /**
   * The infoType is typically used in Colombia.
   */
  public const LOCATION_CATEGORY_COLOMBIA = 'COLOMBIA';
  /**
   * The infoType is typically used in Croatia.
   */
  public const LOCATION_CATEGORY_CROATIA = 'CROATIA';
  /**
   * The infoType is typically used in Czechia.
   */
  public const LOCATION_CATEGORY_CZECHIA = 'CZECHIA';
  /**
   * The infoType is typically used in Denmark.
   */
  public const LOCATION_CATEGORY_DENMARK = 'DENMARK';
  /**
   * The infoType is typically used in France.
   */
  public const LOCATION_CATEGORY_FRANCE = 'FRANCE';
  /**
   * The infoType is typically used in Finland.
   */
  public const LOCATION_CATEGORY_FINLAND = 'FINLAND';
  /**
   * The infoType is typically used in Germany.
   */
  public const LOCATION_CATEGORY_GERMANY = 'GERMANY';
  /**
   * The infoType is typically used in Hong Kong.
   */
  public const LOCATION_CATEGORY_HONG_KONG = 'HONG_KONG';
  /**
   * The infoType is typically used in India.
   */
  public const LOCATION_CATEGORY_INDIA = 'INDIA';
  /**
   * The infoType is typically used in Indonesia.
   */
  public const LOCATION_CATEGORY_INDONESIA = 'INDONESIA';
  /**
   * The infoType is typically used in Ireland.
   */
  public const LOCATION_CATEGORY_IRELAND = 'IRELAND';
  /**
   * The infoType is typically used in Israel.
   */
  public const LOCATION_CATEGORY_ISRAEL = 'ISRAEL';
  /**
   * The infoType is typically used in Italy.
   */
  public const LOCATION_CATEGORY_ITALY = 'ITALY';
  /**
   * The infoType is typically used in Japan.
   */
  public const LOCATION_CATEGORY_JAPAN = 'JAPAN';
  /**
   * The infoType is typically used in Kazakhstan.
   */
  public const LOCATION_CATEGORY_KAZAKHSTAN = 'KAZAKHSTAN';
  /**
   * The infoType is typically used in Korea.
   */
  public const LOCATION_CATEGORY_KOREA = 'KOREA';
  /**
   * The infoType is typically used in Mexico.
   */
  public const LOCATION_CATEGORY_MEXICO = 'MEXICO';
  /**
   * The infoType is typically used in the Netherlands.
   */
  public const LOCATION_CATEGORY_THE_NETHERLANDS = 'THE_NETHERLANDS';
  /**
   * The infoType is typically used in New Zealand.
   */
  public const LOCATION_CATEGORY_NEW_ZEALAND = 'NEW_ZEALAND';
  /**
   * The infoType is typically used in Norway.
   */
  public const LOCATION_CATEGORY_NORWAY = 'NORWAY';
  /**
   * The infoType is typically used in Paraguay.
   */
  public const LOCATION_CATEGORY_PARAGUAY = 'PARAGUAY';
  /**
   * The infoType is typically used in Peru.
   */
  public const LOCATION_CATEGORY_PERU = 'PERU';
  /**
   * The infoType is typically used in Poland.
   */
  public const LOCATION_CATEGORY_POLAND = 'POLAND';
  /**
   * The infoType is typically used in Portugal.
   */
  public const LOCATION_CATEGORY_PORTUGAL = 'PORTUGAL';
  /**
   * The infoType is typically used in Russia.
   */
  public const LOCATION_CATEGORY_RUSSIA = 'RUSSIA';
  /**
   * The infoType is typically used in Singapore.
   */
  public const LOCATION_CATEGORY_SINGAPORE = 'SINGAPORE';
  /**
   * The infoType is typically used in South Africa.
   */
  public const LOCATION_CATEGORY_SOUTH_AFRICA = 'SOUTH_AFRICA';
  /**
   * The infoType is typically used in Spain.
   */
  public const LOCATION_CATEGORY_SPAIN = 'SPAIN';
  /**
   * The infoType is typically used in Sweden.
   */
  public const LOCATION_CATEGORY_SWEDEN = 'SWEDEN';
  /**
   * The infoType is typically used in Switzerland.
   */
  public const LOCATION_CATEGORY_SWITZERLAND = 'SWITZERLAND';
  /**
   * The infoType is typically used in Taiwan.
   */
  public const LOCATION_CATEGORY_TAIWAN = 'TAIWAN';
  /**
   * The infoType is typically used in Thailand.
   */
  public const LOCATION_CATEGORY_THAILAND = 'THAILAND';
  /**
   * The infoType is typically used in Turkey.
   */
  public const LOCATION_CATEGORY_TURKEY = 'TURKEY';
  /**
   * The infoType is typically used in Ukraine.
   */
  public const LOCATION_CATEGORY_UKRAINE = 'UKRAINE';
  /**
   * The infoType is typically used in the United Kingdom.
   */
  public const LOCATION_CATEGORY_UNITED_KINGDOM = 'UNITED_KINGDOM';
  /**
   * The infoType is typically used in the United States.
   */
  public const LOCATION_CATEGORY_UNITED_STATES = 'UNITED_STATES';
  /**
   * The infoType is typically used in Uruguay.
   */
  public const LOCATION_CATEGORY_URUGUAY = 'URUGUAY';
  /**
   * The infoType is typically used in Uzbekistan.
   */
  public const LOCATION_CATEGORY_UZBEKISTAN = 'UZBEKISTAN';
  /**
   * The infoType is typically used in Venezuela.
   */
  public const LOCATION_CATEGORY_VENEZUELA = 'VENEZUELA';
  /**
   * The infoType is typically used in Google internally.
   */
  public const LOCATION_CATEGORY_INTERNAL = 'INTERNAL';
  /**
   * Unused type
   */
  public const TYPE_CATEGORY_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Personally identifiable information, for example, a name or phone number
   */
  public const TYPE_CATEGORY_PII = 'PII';
  /**
   * Personally identifiable information that is especially sensitive, for
   * example, a passport number.
   */
  public const TYPE_CATEGORY_SPII = 'SPII';
  /**
   * Attributes that can partially identify someone, especially in combination
   * with other attributes, like age, height, and gender.
   */
  public const TYPE_CATEGORY_DEMOGRAPHIC = 'DEMOGRAPHIC';
  /**
   * Confidential or secret information, for example, a password.
   */
  public const TYPE_CATEGORY_CREDENTIAL = 'CREDENTIAL';
  /**
   * An identification document issued by a government.
   */
  public const TYPE_CATEGORY_GOVERNMENT_ID = 'GOVERNMENT_ID';
  /**
   * A document, for example, a resume or source code.
   */
  public const TYPE_CATEGORY_DOCUMENT = 'DOCUMENT';
  /**
   * Information that is not sensitive on its own, but provides details about
   * the circumstances surrounding an entity or an event.
   */
  public const TYPE_CATEGORY_CONTEXTUAL_INFORMATION = 'CONTEXTUAL_INFORMATION';
  /**
   * Category for `CustomInfoType` types.
   */
  public const TYPE_CATEGORY_CUSTOM = 'CUSTOM';
  /**
   * The group of relevant businesses where this infoType is commonly used
   *
   * @var string
   */
  public $industryCategory;
  /**
   * The region or country that issued the ID or document represented by the
   * infoType.
   *
   * @var string
   */
  public $locationCategory;
  /**
   * The class of identifiers where this infoType belongs
   *
   * @var string
   */
  public $typeCategory;

  /**
   * The group of relevant businesses where this infoType is commonly used
   *
   * Accepted values: INDUSTRY_UNSPECIFIED, FINANCE, HEALTH, TELECOMMUNICATIONS
   *
   * @param self::INDUSTRY_CATEGORY_* $industryCategory
   */
  public function setIndustryCategory($industryCategory)
  {
    $this->industryCategory = $industryCategory;
  }
  /**
   * @return self::INDUSTRY_CATEGORY_*
   */
  public function getIndustryCategory()
  {
    return $this->industryCategory;
  }
  /**
   * The region or country that issued the ID or document represented by the
   * infoType.
   *
   * Accepted values: LOCATION_UNSPECIFIED, GLOBAL, ARGENTINA, ARMENIA,
   * AUSTRALIA, AUSTRIA, AZERBAIJAN, BELARUS, BELGIUM, BRAZIL, CANADA, CHILE,
   * CHINA, COLOMBIA, CROATIA, CZECHIA, DENMARK, FRANCE, FINLAND, GERMANY,
   * HONG_KONG, INDIA, INDONESIA, IRELAND, ISRAEL, ITALY, JAPAN, KAZAKHSTAN,
   * KOREA, MEXICO, THE_NETHERLANDS, NEW_ZEALAND, NORWAY, PARAGUAY, PERU,
   * POLAND, PORTUGAL, RUSSIA, SINGAPORE, SOUTH_AFRICA, SPAIN, SWEDEN,
   * SWITZERLAND, TAIWAN, THAILAND, TURKEY, UKRAINE, UNITED_KINGDOM,
   * UNITED_STATES, URUGUAY, UZBEKISTAN, VENEZUELA, INTERNAL
   *
   * @param self::LOCATION_CATEGORY_* $locationCategory
   */
  public function setLocationCategory($locationCategory)
  {
    $this->locationCategory = $locationCategory;
  }
  /**
   * @return self::LOCATION_CATEGORY_*
   */
  public function getLocationCategory()
  {
    return $this->locationCategory;
  }
  /**
   * The class of identifiers where this infoType belongs
   *
   * Accepted values: TYPE_UNSPECIFIED, PII, SPII, DEMOGRAPHIC, CREDENTIAL,
   * GOVERNMENT_ID, DOCUMENT, CONTEXTUAL_INFORMATION, CUSTOM
   *
   * @param self::TYPE_CATEGORY_* $typeCategory
   */
  public function setTypeCategory($typeCategory)
  {
    $this->typeCategory = $typeCategory;
  }
  /**
   * @return self::TYPE_CATEGORY_*
   */
  public function getTypeCategory()
  {
    return $this->typeCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2InfoTypeCategory::class, 'Google_Service_DLP_GooglePrivacyDlpV2InfoTypeCategory');
