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

class Metric extends \Google\Collection
{
  protected $collection_key = 'histogram';
  /**
   * For enum metrics, provides fractions which add up to approximately 1.0.
   *
   * @var []
   */
  public $fractions;
  protected $histogramType = Bin::class;
  protected $histogramDataType = 'array';
  protected $percentilesType = Percentiles::class;
  protected $percentilesDataType = '';

  public function setFractions($fractions)
  {
    $this->fractions = $fractions;
  }
  public function getFractions()
  {
    return $this->fractions;
  }
  /**
   * The histogram of user experiences for a metric. The histogram will have at
   * least one bin and the densities of all bins will add up to ~1.
   *
   * @param Bin[] $histogram
   */
  public function setHistogram($histogram)
  {
    $this->histogram = $histogram;
  }
  /**
   * @return Bin[]
   */
  public function getHistogram()
  {
    return $this->histogram;
  }
  /**
   * Commonly useful percentiles of the Metric. The value type for the
   * percentiles will be the same as the value types given for the Histogram
   * bins.
   *
   * @param Percentiles $percentiles
   */
  public function setPercentiles(Percentiles $percentiles)
  {
    $this->percentiles = $percentiles;
  }
  /**
   * @return Percentiles
   */
  public function getPercentiles()
  {
    return $this->percentiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metric::class, 'Google_Service_ChromeUXReport_Metric');
