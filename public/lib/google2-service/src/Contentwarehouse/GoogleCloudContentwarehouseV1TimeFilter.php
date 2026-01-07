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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1TimeFilter extends \Google\Model
{
  /**
   * Default value.
   */
  public const TIME_FIELD_TIME_FIELD_UNSPECIFIED = 'TIME_FIELD_UNSPECIFIED';
  /**
   * Earliest document create time.
   */
  public const TIME_FIELD_CREATE_TIME = 'CREATE_TIME';
  /**
   * Latest document update time.
   */
  public const TIME_FIELD_UPDATE_TIME = 'UPDATE_TIME';
  /**
   * Time when document becomes mutable again.
   */
  public const TIME_FIELD_DISPOSITION_TIME = 'DISPOSITION_TIME';
  /**
   * Specifies which time field to filter documents on. Defaults to
   * TimeField.UPLOAD_TIME.
   *
   * @var string
   */
  public $timeField;
  protected $timeRangeType = GoogleTypeInterval::class;
  protected $timeRangeDataType = '';

  /**
   * Specifies which time field to filter documents on. Defaults to
   * TimeField.UPLOAD_TIME.
   *
   * Accepted values: TIME_FIELD_UNSPECIFIED, CREATE_TIME, UPDATE_TIME,
   * DISPOSITION_TIME
   *
   * @param self::TIME_FIELD_* $timeField
   */
  public function setTimeField($timeField)
  {
    $this->timeField = $timeField;
  }
  /**
   * @return self::TIME_FIELD_*
   */
  public function getTimeField()
  {
    return $this->timeField;
  }
  /**
   * @param GoogleTypeInterval $timeRange
   */
  public function setTimeRange(GoogleTypeInterval $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return GoogleTypeInterval
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1TimeFilter::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1TimeFilter');
