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

namespace Google\Service\MyBusinessBusinessInformation;

class OpenInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const STATUS_OPEN_FOR_BUSINESS_UNSPECIFIED = 'OPEN_FOR_BUSINESS_UNSPECIFIED';
  /**
   * Indicates that the location is open.
   */
  public const STATUS_OPEN = 'OPEN';
  /**
   * Indicates that the location has been permanently closed.
   */
  public const STATUS_CLOSED_PERMANENTLY = 'CLOSED_PERMANENTLY';
  /**
   * Indicates that the location has been temporarily closed.
   */
  public const STATUS_CLOSED_TEMPORARILY = 'CLOSED_TEMPORARILY';
  /**
   * Output only. Indicates whether this business is eligible for re-open.
   *
   * @var bool
   */
  public $canReopen;
  protected $openingDateType = Date::class;
  protected $openingDateDataType = '';
  /**
   * Required. Indicates whether or not the Location is currently open for
   * business. All locations are open by default, unless updated to be closed.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Indicates whether this business is eligible for re-open.
   *
   * @param bool $canReopen
   */
  public function setCanReopen($canReopen)
  {
    $this->canReopen = $canReopen;
  }
  /**
   * @return bool
   */
  public function getCanReopen()
  {
    return $this->canReopen;
  }
  /**
   * Optional. The date on which the location first opened. If the exact day is
   * not known, month and year only can be provided. The date must be in the
   * past or be no more than one year in the future.
   *
   * @param Date $openingDate
   */
  public function setOpeningDate(Date $openingDate)
  {
    $this->openingDate = $openingDate;
  }
  /**
   * @return Date
   */
  public function getOpeningDate()
  {
    return $this->openingDate;
  }
  /**
   * Required. Indicates whether or not the Location is currently open for
   * business. All locations are open by default, unless updated to be closed.
   *
   * Accepted values: OPEN_FOR_BUSINESS_UNSPECIFIED, OPEN, CLOSED_PERMANENTLY,
   * CLOSED_TEMPORARILY
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OpenInfo::class, 'Google_Service_MyBusinessBusinessInformation_OpenInfo');
