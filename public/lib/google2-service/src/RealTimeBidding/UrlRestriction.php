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

namespace Google\Service\RealTimeBidding;

class UrlRestriction extends \Google\Model
{
  /**
   * Default value that should never be used.
   */
  public const RESTRICTION_TYPE_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * The tag URL (as recorded by the pixel callback) contains the specified URL.
   */
  public const RESTRICTION_TYPE_CONTAINS = 'CONTAINS';
  /**
   * The tag URL (as recorded by the pixel callback) exactly matches the
   * specified URL.
   */
  public const RESTRICTION_TYPE_EQUALS = 'EQUALS';
  /**
   * The tag URL (as recorded by the pixel callback) starts with the specified
   * URL.
   */
  public const RESTRICTION_TYPE_STARTS_WITH = 'STARTS_WITH';
  /**
   * The tag URL (as recorded by the pixel callback) ends with the specified
   * URL.
   */
  public const RESTRICTION_TYPE_ENDS_WITH = 'ENDS_WITH';
  /**
   * The tag URL (as recorded by the pixel callback) does not equal the
   * specified URL.
   */
  public const RESTRICTION_TYPE_DOES_NOT_EQUAL = 'DOES_NOT_EQUAL';
  /**
   * The tag URL (as recorded by the pixel callback) does not contain the
   * specified URL.
   */
  public const RESTRICTION_TYPE_DOES_NOT_CONTAIN = 'DOES_NOT_CONTAIN';
  /**
   * The tag URL (as recorded by the pixel callback) does not start with the
   * specified URL.
   */
  public const RESTRICTION_TYPE_DOES_NOT_START_WITH = 'DOES_NOT_START_WITH';
  /**
   * The tag URL (as recorded by the pixel callback) does not end with the
   * specified URL.
   */
  public const RESTRICTION_TYPE_DOES_NOT_END_WITH = 'DOES_NOT_END_WITH';
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  /**
   * The restriction type for the specified URL.
   *
   * @var string
   */
  public $restrictionType;
  protected $startDateType = Date::class;
  protected $startDateDataType = '';
  /**
   * Required. The URL to use for applying the restriction on the user list.
   *
   * @var string
   */
  public $url;

  /**
   * End date (if specified) of the URL restriction. End date should be later
   * than the start date for the date range to be valid.
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * The restriction type for the specified URL.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, CONTAINS, EQUALS,
   * STARTS_WITH, ENDS_WITH, DOES_NOT_EQUAL, DOES_NOT_CONTAIN,
   * DOES_NOT_START_WITH, DOES_NOT_END_WITH
   *
   * @param self::RESTRICTION_TYPE_* $restrictionType
   */
  public function setRestrictionType($restrictionType)
  {
    $this->restrictionType = $restrictionType;
  }
  /**
   * @return self::RESTRICTION_TYPE_*
   */
  public function getRestrictionType()
  {
    return $this->restrictionType;
  }
  /**
   * Start date (if specified) of the URL restriction.
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Required. The URL to use for applying the restriction on the user list.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlRestriction::class, 'Google_Service_RealTimeBidding_UrlRestriction');
