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

namespace Google\Service\AdExchangeBuyerII;

class RowDimensions extends \Google\Model
{
  /**
   * The publisher identifier for this row, if a breakdown by [BreakdownDimensio
   * n.PUBLISHER_IDENTIFIER](https://developers.google.com/authorized-buyers/api
   * s/reference/rest/v2beta1/bidders.accounts.filterSets#FilterSet.BreakdownDim
   * ension) was requested.
   *
   * @var string
   */
  public $publisherIdentifier;
  protected $timeIntervalType = TimeInterval::class;
  protected $timeIntervalDataType = '';

  /**
   * The publisher identifier for this row, if a breakdown by [BreakdownDimensio
   * n.PUBLISHER_IDENTIFIER](https://developers.google.com/authorized-buyers/api
   * s/reference/rest/v2beta1/bidders.accounts.filterSets#FilterSet.BreakdownDim
   * ension) was requested.
   *
   * @param string $publisherIdentifier
   */
  public function setPublisherIdentifier($publisherIdentifier)
  {
    $this->publisherIdentifier = $publisherIdentifier;
  }
  /**
   * @return string
   */
  public function getPublisherIdentifier()
  {
    return $this->publisherIdentifier;
  }
  /**
   * The time interval that this row represents.
   *
   * @param TimeInterval $timeInterval
   */
  public function setTimeInterval(TimeInterval $timeInterval)
  {
    $this->timeInterval = $timeInterval;
  }
  /**
   * @return TimeInterval
   */
  public function getTimeInterval()
  {
    return $this->timeInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RowDimensions::class, 'Google_Service_AdExchangeBuyerII_RowDimensions');
