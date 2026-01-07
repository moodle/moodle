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

namespace Google\Service\Walletobjects;

class RotatingBarcodeValues extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Required. The amount of time each barcode is valid for.
   *
   * @var string
   */
  public $periodMillis;
  /**
   * Required. The date/time the first barcode is valid from. Barcodes will be
   * rotated through using period_millis defined on the object's
   * RotatingBarcodeValueInfo. This is an ISO 8601 extended format date/time,
   * with an offset. Time may be specified up to nanosecond precision. Offsets
   * may be specified with seconds precision (even though offset seconds is not
   * part of ISO 8601). For example: `1985-04-12T23:20:50.52Z` would be 20
   * minutes and 50.52 seconds after the 23rd hour of April 12th, 1985 in UTC.
   * `1985-04-12T19:20:50.52-04:00` would be 20 minutes and 50.52 seconds after
   * the 19th hour of April 12th, 1985, 4 hours before UTC (same instant in time
   * as the above example). If the event were in New York, this would be the
   * equivalent of Eastern Daylight Time (EDT). Remember that offset varies in
   * regions that observe Daylight Saving Time (or Summer Time), depending on
   * the time of the year.
   *
   * @var string
   */
  public $startDateTime;
  /**
   * Required. The values to encode in the barcode. At least one value is
   * required.
   *
   * @var string[]
   */
  public $values;

  /**
   * Required. The amount of time each barcode is valid for.
   *
   * @param string $periodMillis
   */
  public function setPeriodMillis($periodMillis)
  {
    $this->periodMillis = $periodMillis;
  }
  /**
   * @return string
   */
  public function getPeriodMillis()
  {
    return $this->periodMillis;
  }
  /**
   * Required. The date/time the first barcode is valid from. Barcodes will be
   * rotated through using period_millis defined on the object's
   * RotatingBarcodeValueInfo. This is an ISO 8601 extended format date/time,
   * with an offset. Time may be specified up to nanosecond precision. Offsets
   * may be specified with seconds precision (even though offset seconds is not
   * part of ISO 8601). For example: `1985-04-12T23:20:50.52Z` would be 20
   * minutes and 50.52 seconds after the 23rd hour of April 12th, 1985 in UTC.
   * `1985-04-12T19:20:50.52-04:00` would be 20 minutes and 50.52 seconds after
   * the 19th hour of April 12th, 1985, 4 hours before UTC (same instant in time
   * as the above example). If the event were in New York, this would be the
   * equivalent of Eastern Daylight Time (EDT). Remember that offset varies in
   * regions that observe Daylight Saving Time (or Summer Time), depending on
   * the time of the year.
   *
   * @param string $startDateTime
   */
  public function setStartDateTime($startDateTime)
  {
    $this->startDateTime = $startDateTime;
  }
  /**
   * @return string
   */
  public function getStartDateTime()
  {
    return $this->startDateTime;
  }
  /**
   * Required. The values to encode in the barcode. At least one value is
   * required.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RotatingBarcodeValues::class, 'Google_Service_Walletobjects_RotatingBarcodeValues');
