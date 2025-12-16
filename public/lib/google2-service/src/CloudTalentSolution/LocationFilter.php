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

namespace Google\Service\CloudTalentSolution;

class LocationFilter extends \Google\Model
{
  /**
   * Default value if the telecommute preference isn't specified.
   */
  public const TELECOMMUTE_PREFERENCE_TELECOMMUTE_PREFERENCE_UNSPECIFIED = 'TELECOMMUTE_PREFERENCE_UNSPECIFIED';
  /**
   * Deprecated: Ignore telecommute status of jobs. Use
   * TELECOMMUTE_JOBS_EXCLUDED if want to exclude telecommute jobs.
   *
   * @deprecated
   */
  public const TELECOMMUTE_PREFERENCE_TELECOMMUTE_EXCLUDED = 'TELECOMMUTE_EXCLUDED';
  /**
   * Allow telecommute jobs.
   */
  public const TELECOMMUTE_PREFERENCE_TELECOMMUTE_ALLOWED = 'TELECOMMUTE_ALLOWED';
  /**
   * Exclude telecommute jobs.
   */
  public const TELECOMMUTE_PREFERENCE_TELECOMMUTE_JOBS_EXCLUDED = 'TELECOMMUTE_JOBS_EXCLUDED';
  /**
   * The address name, such as "Mountain View" or "Bay Area".
   *
   * @var string
   */
  public $address;
  /**
   * The distance_in_miles is applied when the location being searched for is
   * identified as a city or smaller. This field is ignored if the location
   * being searched for is a state or larger.
   *
   * @var 
   */
  public $distanceInMiles;
  protected $latLngType = LatLng::class;
  protected $latLngDataType = '';
  /**
   * CLDR region code of the country/region. This field may be used in two ways:
   * 1) If telecommute preference is not set, this field is used address
   * ambiguity of the user-input address. For example, "Liverpool" may refer to
   * "Liverpool, NY, US" or "Liverpool, UK". This region code biases the address
   * resolution toward a specific country or territory. If this field is not
   * set, address resolution is biased toward the United States by default. 2)
   * If telecommute preference is set to TELECOMMUTE_ALLOWED, the telecommute
   * location filter will be limited to the region specified in this field. If
   * this field is not set, the telecommute job locations will not be See
   * https://unicode-org.github.io/cldr-
   * staging/charts/latest/supplemental/territory_information.html for details.
   * Example: "CH" for Switzerland.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Allows the client to return jobs without a set location, specifically,
   * telecommuting jobs (telecommuting is considered by the service as a special
   * location). Job.posting_region indicates if a job permits telecommuting. If
   * this field is set to TelecommutePreference.TELECOMMUTE_ALLOWED,
   * telecommuting jobs are searched, and address and lat_lng are ignored. If
   * not set or set to TelecommutePreference.TELECOMMUTE_EXCLUDED, the
   * telecommute status of the jobs is ignored. Jobs that have
   * PostingRegion.TELECOMMUTE and have additional Job.addresses may still be
   * matched based on other location filters using address or lat_lng. This
   * filter can be used by itself to search exclusively for telecommuting jobs,
   * or it can be combined with another location filter to search for a
   * combination of job locations, such as "Mountain View" or "telecommuting"
   * jobs. However, when used in combination with other location filters,
   * telecommuting jobs can be treated as less relevant than other jobs in the
   * search response. This field is only used for job search requests.
   *
   * @var string
   */
  public $telecommutePreference;

  /**
   * The address name, such as "Mountain View" or "Bay Area".
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  public function setDistanceInMiles($distanceInMiles)
  {
    $this->distanceInMiles = $distanceInMiles;
  }
  public function getDistanceInMiles()
  {
    return $this->distanceInMiles;
  }
  /**
   * The latitude and longitude of the geographic center to search from. This
   * field is ignored if `address` is provided.
   *
   * @param LatLng $latLng
   */
  public function setLatLng(LatLng $latLng)
  {
    $this->latLng = $latLng;
  }
  /**
   * @return LatLng
   */
  public function getLatLng()
  {
    return $this->latLng;
  }
  /**
   * CLDR region code of the country/region. This field may be used in two ways:
   * 1) If telecommute preference is not set, this field is used address
   * ambiguity of the user-input address. For example, "Liverpool" may refer to
   * "Liverpool, NY, US" or "Liverpool, UK". This region code biases the address
   * resolution toward a specific country or territory. If this field is not
   * set, address resolution is biased toward the United States by default. 2)
   * If telecommute preference is set to TELECOMMUTE_ALLOWED, the telecommute
   * location filter will be limited to the region specified in this field. If
   * this field is not set, the telecommute job locations will not be See
   * https://unicode-org.github.io/cldr-
   * staging/charts/latest/supplemental/territory_information.html for details.
   * Example: "CH" for Switzerland.
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
  /**
   * Allows the client to return jobs without a set location, specifically,
   * telecommuting jobs (telecommuting is considered by the service as a special
   * location). Job.posting_region indicates if a job permits telecommuting. If
   * this field is set to TelecommutePreference.TELECOMMUTE_ALLOWED,
   * telecommuting jobs are searched, and address and lat_lng are ignored. If
   * not set or set to TelecommutePreference.TELECOMMUTE_EXCLUDED, the
   * telecommute status of the jobs is ignored. Jobs that have
   * PostingRegion.TELECOMMUTE and have additional Job.addresses may still be
   * matched based on other location filters using address or lat_lng. This
   * filter can be used by itself to search exclusively for telecommuting jobs,
   * or it can be combined with another location filter to search for a
   * combination of job locations, such as "Mountain View" or "telecommuting"
   * jobs. However, when used in combination with other location filters,
   * telecommuting jobs can be treated as less relevant than other jobs in the
   * search response. This field is only used for job search requests.
   *
   * Accepted values: TELECOMMUTE_PREFERENCE_UNSPECIFIED, TELECOMMUTE_EXCLUDED,
   * TELECOMMUTE_ALLOWED, TELECOMMUTE_JOBS_EXCLUDED
   *
   * @param self::TELECOMMUTE_PREFERENCE_* $telecommutePreference
   */
  public function setTelecommutePreference($telecommutePreference)
  {
    $this->telecommutePreference = $telecommutePreference;
  }
  /**
   * @return self::TELECOMMUTE_PREFERENCE_*
   */
  public function getTelecommutePreference()
  {
    return $this->telecommutePreference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationFilter::class, 'Google_Service_CloudTalentSolution_LocationFilter');
