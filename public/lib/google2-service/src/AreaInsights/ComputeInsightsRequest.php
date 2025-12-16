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

namespace Google\Service\AreaInsights;

class ComputeInsightsRequest extends \Google\Collection
{
  protected $collection_key = 'insights';
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  /**
   * Required. Insights to compute. Currently only INSIGHT_COUNT and
   * INSIGHT_PLACES are supported.
   *
   * @var string[]
   */
  public $insights;

  /**
   * Required. Insight filter.
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. Insights to compute. Currently only INSIGHT_COUNT and
   * INSIGHT_PLACES are supported.
   *
   * @param string[] $insights
   */
  public function setInsights($insights)
  {
    $this->insights = $insights;
  }
  /**
   * @return string[]
   */
  public function getInsights()
  {
    return $this->insights;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeInsightsRequest::class, 'Google_Service_AreaInsights_ComputeInsightsRequest');
