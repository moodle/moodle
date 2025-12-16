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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1EduData extends \Google\Model
{
  /**
   * Not used.
   */
  public const INSTITUTE_SIZE_INSTITUTE_SIZE_UNSPECIFIED = 'INSTITUTE_SIZE_UNSPECIFIED';
  /**
   * 1 - 100
   */
  public const INSTITUTE_SIZE_SIZE_1_100 = 'SIZE_1_100';
  /**
   * 101 - 500
   */
  public const INSTITUTE_SIZE_SIZE_101_500 = 'SIZE_101_500';
  /**
   * 501 - 1,000
   */
  public const INSTITUTE_SIZE_SIZE_501_1000 = 'SIZE_501_1000';
  /**
   * 1,001 - 2,000
   */
  public const INSTITUTE_SIZE_SIZE_1001_2000 = 'SIZE_1001_2000';
  /**
   * 2,001 - 5,000
   */
  public const INSTITUTE_SIZE_SIZE_2001_5000 = 'SIZE_2001_5000';
  /**
   * 5,001 - 10,000
   */
  public const INSTITUTE_SIZE_SIZE_5001_10000 = 'SIZE_5001_10000';
  /**
   * 10,001 +
   */
  public const INSTITUTE_SIZE_SIZE_10001_OR_MORE = 'SIZE_10001_OR_MORE';
  /**
   * Not used.
   */
  public const INSTITUTE_TYPE_INSTITUTE_TYPE_UNSPECIFIED = 'INSTITUTE_TYPE_UNSPECIFIED';
  /**
   * Elementary/Secondary Schools & Districts
   */
  public const INSTITUTE_TYPE_K12 = 'K12';
  /**
   * Higher Education Universities & Colleges
   */
  public const INSTITUTE_TYPE_UNIVERSITY = 'UNIVERSITY';
  /**
   * Size of the institute.
   *
   * @var string
   */
  public $instituteSize;
  /**
   * Designated institute type of customer.
   *
   * @var string
   */
  public $instituteType;
  /**
   * Web address for the edu customer's institution.
   *
   * @var string
   */
  public $website;

  /**
   * Size of the institute.
   *
   * Accepted values: INSTITUTE_SIZE_UNSPECIFIED, SIZE_1_100, SIZE_101_500,
   * SIZE_501_1000, SIZE_1001_2000, SIZE_2001_5000, SIZE_5001_10000,
   * SIZE_10001_OR_MORE
   *
   * @param self::INSTITUTE_SIZE_* $instituteSize
   */
  public function setInstituteSize($instituteSize)
  {
    $this->instituteSize = $instituteSize;
  }
  /**
   * @return self::INSTITUTE_SIZE_*
   */
  public function getInstituteSize()
  {
    return $this->instituteSize;
  }
  /**
   * Designated institute type of customer.
   *
   * Accepted values: INSTITUTE_TYPE_UNSPECIFIED, K12, UNIVERSITY
   *
   * @param self::INSTITUTE_TYPE_* $instituteType
   */
  public function setInstituteType($instituteType)
  {
    $this->instituteType = $instituteType;
  }
  /**
   * @return self::INSTITUTE_TYPE_*
   */
  public function getInstituteType()
  {
    return $this->instituteType;
  }
  /**
   * Web address for the edu customer's institution.
   *
   * @param string $website
   */
  public function setWebsite($website)
  {
    $this->website = $website;
  }
  /**
   * @return string
   */
  public function getWebsite()
  {
    return $this->website;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1EduData::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1EduData');
