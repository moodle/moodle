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

namespace Google\Service\ShoppingContent;

class DeliveryArea extends \Google\Model
{
  /**
   * Required. The country that the product can be delivered to. Submit a
   * [unicode CLDR
   * region](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml)
   * such as `US` or `CH`.
   *
   * @var string
   */
  public $countryCode;
  protected $postalCodeRangeType = DeliveryAreaPostalCodeRange::class;
  protected $postalCodeRangeDataType = '';
  /**
   * A state, territory, or prefecture. This is supported for the United States,
   * Australia, and Japan. Provide a subdivision code from the ISO 3166-2 code
   * tables ([US](https://en.wikipedia.org/wiki/ISO_3166-2:US),
   * [AU](https://en.wikipedia.org/wiki/ISO_3166-2:AU), or
   * [JP](https://en.wikipedia.org/wiki/ISO_3166-2:JP)) without country prefix
   * (for example, `"NY"`, `"NSW"`, `"03"`).
   *
   * @var string
   */
  public $regionCode;

  /**
   * Required. The country that the product can be delivered to. Submit a
   * [unicode CLDR
   * region](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml)
   * such as `US` or `CH`.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * A postal code, postal code range or postal code prefix that defines this
   * area. Limited to US and AUS.
   *
   * @param DeliveryAreaPostalCodeRange $postalCodeRange
   */
  public function setPostalCodeRange(DeliveryAreaPostalCodeRange $postalCodeRange)
  {
    $this->postalCodeRange = $postalCodeRange;
  }
  /**
   * @return DeliveryAreaPostalCodeRange
   */
  public function getPostalCodeRange()
  {
    return $this->postalCodeRange;
  }
  /**
   * A state, territory, or prefecture. This is supported for the United States,
   * Australia, and Japan. Provide a subdivision code from the ISO 3166-2 code
   * tables ([US](https://en.wikipedia.org/wiki/ISO_3166-2:US),
   * [AU](https://en.wikipedia.org/wiki/ISO_3166-2:AU), or
   * [JP](https://en.wikipedia.org/wiki/ISO_3166-2:JP)) without country prefix
   * (for example, `"NY"`, `"NSW"`, `"03"`).
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryArea::class, 'Google_Service_ShoppingContent_DeliveryArea');
