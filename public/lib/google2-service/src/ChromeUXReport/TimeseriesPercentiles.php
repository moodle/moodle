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

namespace Google\Service\ChromeUXReport;

class TimeseriesPercentiles extends \Google\Collection
{
  protected $collection_key = 'p75s';
  /**
   * 75% of users experienced the given metric at or below this value. The
   * length of this list of densities is equal to the length of the
   * CollectionPeriods field in the HistoryRecord message, which describes when
   * the density was observed in the field.
   *
   * @var array[]
   */
  public $p75s;

  /**
   * 75% of users experienced the given metric at or below this value. The
   * length of this list of densities is equal to the length of the
   * CollectionPeriods field in the HistoryRecord message, which describes when
   * the density was observed in the field.
   *
   * @param array[] $p75s
   */
  public function setP75s($p75s)
  {
    $this->p75s = $p75s;
  }
  /**
   * @return array[]
   */
  public function getP75s()
  {
    return $this->p75s;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeseriesPercentiles::class, 'Google_Service_ChromeUXReport_TimeseriesPercentiles');
