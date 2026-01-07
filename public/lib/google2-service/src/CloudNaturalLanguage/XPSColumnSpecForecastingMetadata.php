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

namespace Google\Service\CloudNaturalLanguage;

class XPSColumnSpecForecastingMetadata extends \Google\Model
{
  /**
   * An un-set value of this enum.
   */
  public const COLUMN_TYPE_COLUMN_TYPE_UNSPECIFIED = 'COLUMN_TYPE_UNSPECIFIED';
  /**
   * Key columns are used to identify timeseries.
   */
  public const COLUMN_TYPE_KEY = 'KEY';
  /**
   * This column contains information describing static properties of the
   * entities identified by the key column(s) (e.g. city's ZIP code).
   */
  public const COLUMN_TYPE_KEY_METADATA = 'KEY_METADATA';
  /**
   * This column contains information for the given entity, at any time poinrt,
   * they are only available in the time series before.
   */
  public const COLUMN_TYPE_TIME_SERIES_AVAILABLE_PAST_ONLY = 'TIME_SERIES_AVAILABLE_PAST_ONLY';
  /**
   * This column contains information for the given entity is known both for the
   * past and the sufficiently far future.
   */
  public const COLUMN_TYPE_TIME_SERIES_AVAILABLE_PAST_AND_FUTURE = 'TIME_SERIES_AVAILABLE_PAST_AND_FUTURE';
  /**
   * The type of the column for FORECASTING model training purposes.
   *
   * @var string
   */
  public $columnType;

  /**
   * The type of the column for FORECASTING model training purposes.
   *
   * Accepted values: COLUMN_TYPE_UNSPECIFIED, KEY, KEY_METADATA,
   * TIME_SERIES_AVAILABLE_PAST_ONLY, TIME_SERIES_AVAILABLE_PAST_AND_FUTURE
   *
   * @param self::COLUMN_TYPE_* $columnType
   */
  public function setColumnType($columnType)
  {
    $this->columnType = $columnType;
  }
  /**
   * @return self::COLUMN_TYPE_*
   */
  public function getColumnType()
  {
    return $this->columnType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSColumnSpecForecastingMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSColumnSpecForecastingMetadata');
