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

class GooglePrivacyDlpV2TimePartConfig extends \Google\Model
{
  /**
   * Unused
   */
  public const PART_TO_EXTRACT_TIME_PART_UNSPECIFIED = 'TIME_PART_UNSPECIFIED';
  /**
   * [0-9999]
   */
  public const PART_TO_EXTRACT_YEAR = 'YEAR';
  /**
   * [1-12]
   */
  public const PART_TO_EXTRACT_MONTH = 'MONTH';
  /**
   * [1-31]
   */
  public const PART_TO_EXTRACT_DAY_OF_MONTH = 'DAY_OF_MONTH';
  /**
   * [1-7]
   */
  public const PART_TO_EXTRACT_DAY_OF_WEEK = 'DAY_OF_WEEK';
  /**
   * [1-53]
   */
  public const PART_TO_EXTRACT_WEEK_OF_YEAR = 'WEEK_OF_YEAR';
  /**
   * [0-23]
   */
  public const PART_TO_EXTRACT_HOUR_OF_DAY = 'HOUR_OF_DAY';
  /**
   * The part of the time to keep.
   *
   * @var string
   */
  public $partToExtract;

  /**
   * The part of the time to keep.
   *
   * Accepted values: TIME_PART_UNSPECIFIED, YEAR, MONTH, DAY_OF_MONTH,
   * DAY_OF_WEEK, WEEK_OF_YEAR, HOUR_OF_DAY
   *
   * @param self::PART_TO_EXTRACT_* $partToExtract
   */
  public function setPartToExtract($partToExtract)
  {
    $this->partToExtract = $partToExtract;
  }
  /**
   * @return self::PART_TO_EXTRACT_*
   */
  public function getPartToExtract()
  {
    return $this->partToExtract;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TimePartConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2TimePartConfig');
