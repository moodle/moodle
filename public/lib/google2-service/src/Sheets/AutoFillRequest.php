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

namespace Google\Service\Sheets;

class AutoFillRequest extends \Google\Model
{
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  protected $sourceAndDestinationType = SourceAndDestination::class;
  protected $sourceAndDestinationDataType = '';
  /**
   * True if we should generate data with the "alternate" series. This differs
   * based on the type and amount of source data.
   *
   * @var bool
   */
  public $useAlternateSeries;

  /**
   * The range to autofill. This will examine the range and detect the location
   * that has data and automatically fill that data in to the rest of the range.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The source and destination areas to autofill. This explicitly lists the
   * source of the autofill and where to extend that data.
   *
   * @param SourceAndDestination $sourceAndDestination
   */
  public function setSourceAndDestination(SourceAndDestination $sourceAndDestination)
  {
    $this->sourceAndDestination = $sourceAndDestination;
  }
  /**
   * @return SourceAndDestination
   */
  public function getSourceAndDestination()
  {
    return $this->sourceAndDestination;
  }
  /**
   * True if we should generate data with the "alternate" series. This differs
   * based on the type and amount of source data.
   *
   * @param bool $useAlternateSeries
   */
  public function setUseAlternateSeries($useAlternateSeries)
  {
    $this->useAlternateSeries = $useAlternateSeries;
  }
  /**
   * @return bool
   */
  public function getUseAlternateSeries()
  {
    return $this->useAlternateSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoFillRequest::class, 'Google_Service_Sheets_AutoFillRequest');
