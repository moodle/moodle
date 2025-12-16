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

namespace Google\Service\AccessApproval;

class AccessLocations extends \Google\Model
{
  /**
   * The "home office" location of the Google administrator. A two-letter
   * country code (ISO 3166-1 alpha-2), such as "US", "DE" or "GB" or a region
   * code. In some limited situations Google systems may refer refer to a region
   * code instead of a country code. Possible Region Codes: * ASI: Asia * EUR:
   * Europe * OCE: Oceania * AFR: Africa * NAM: North America * SAM: South
   * America * ANT: Antarctica * ANY: Any location
   *
   * @var string
   */
  public $principalOfficeCountry;
  /**
   * Physical location of the Google administrator at the time of the access. A
   * two-letter country code (ISO 3166-1 alpha-2), such as "US", "DE" or "GB" or
   * a region code. In some limited situations Google systems may refer refer to
   * a region code instead of a country code. Possible Region Codes: * ASI: Asia
   * * EUR: Europe * OCE: Oceania * AFR: Africa * NAM: North America * SAM:
   * South America * ANT: Antarctica * ANY: Any location
   *
   * @var string
   */
  public $principalPhysicalLocationCountry;

  /**
   * The "home office" location of the Google administrator. A two-letter
   * country code (ISO 3166-1 alpha-2), such as "US", "DE" or "GB" or a region
   * code. In some limited situations Google systems may refer refer to a region
   * code instead of a country code. Possible Region Codes: * ASI: Asia * EUR:
   * Europe * OCE: Oceania * AFR: Africa * NAM: North America * SAM: South
   * America * ANT: Antarctica * ANY: Any location
   *
   * @param string $principalOfficeCountry
   */
  public function setPrincipalOfficeCountry($principalOfficeCountry)
  {
    $this->principalOfficeCountry = $principalOfficeCountry;
  }
  /**
   * @return string
   */
  public function getPrincipalOfficeCountry()
  {
    return $this->principalOfficeCountry;
  }
  /**
   * Physical location of the Google administrator at the time of the access. A
   * two-letter country code (ISO 3166-1 alpha-2), such as "US", "DE" or "GB" or
   * a region code. In some limited situations Google systems may refer refer to
   * a region code instead of a country code. Possible Region Codes: * ASI: Asia
   * * EUR: Europe * OCE: Oceania * AFR: Africa * NAM: North America * SAM:
   * South America * ANT: Antarctica * ANY: Any location
   *
   * @param string $principalPhysicalLocationCountry
   */
  public function setPrincipalPhysicalLocationCountry($principalPhysicalLocationCountry)
  {
    $this->principalPhysicalLocationCountry = $principalPhysicalLocationCountry;
  }
  /**
   * @return string
   */
  public function getPrincipalPhysicalLocationCountry()
  {
    return $this->principalPhysicalLocationCountry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessLocations::class, 'Google_Service_AccessApproval_AccessLocations');
