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

namespace Google\Service\Monitoring;

class RequestBasedSli extends \Google\Model
{
  protected $distributionCutType = DistributionCut::class;
  protected $distributionCutDataType = '';
  protected $goodTotalRatioType = TimeSeriesRatio::class;
  protected $goodTotalRatioDataType = '';

  /**
   * distribution_cut is used when good_service is a count of values aggregated
   * in a Distribution that fall into a good range. The total_service is the
   * total count of all values aggregated in the Distribution.
   *
   * @param DistributionCut $distributionCut
   */
  public function setDistributionCut(DistributionCut $distributionCut)
  {
    $this->distributionCut = $distributionCut;
  }
  /**
   * @return DistributionCut
   */
  public function getDistributionCut()
  {
    return $this->distributionCut;
  }
  /**
   * good_total_ratio is used when the ratio of good_service to total_service is
   * computed from two TimeSeries.
   *
   * @param TimeSeriesRatio $goodTotalRatio
   */
  public function setGoodTotalRatio(TimeSeriesRatio $goodTotalRatio)
  {
    $this->goodTotalRatio = $goodTotalRatio;
  }
  /**
   * @return TimeSeriesRatio
   */
  public function getGoodTotalRatio()
  {
    return $this->goodTotalRatio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestBasedSli::class, 'Google_Service_Monitoring_RequestBasedSli');
