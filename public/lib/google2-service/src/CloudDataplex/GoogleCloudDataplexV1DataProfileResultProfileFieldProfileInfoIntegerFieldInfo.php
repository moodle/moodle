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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo extends \Google\Collection
{
  protected $collection_key = 'quartiles';
  /**
   * Output only. Average of non-null values in the scanned data. NaN, if the
   * field has a NaN.
   *
   * @var 
   */
  public $average;
  /**
   * Output only. Maximum of non-null values in the scanned data. NaN, if the
   * field has a NaN.
   *
   * @var string
   */
  public $max;
  /**
   * Output only. Minimum of non-null values in the scanned data. NaN, if the
   * field has a NaN.
   *
   * @var string
   */
  public $min;
  /**
   * Output only. A quartile divides the number of data points into four parts,
   * or quarters, of more-or-less equal size. Three main quartiles used are: The
   * first quartile (Q1) splits off the lowest 25% of data from the highest 75%.
   * It is also known as the lower or 25th empirical quartile, as 25% of the
   * data is below this point. The second quartile (Q2) is the median of a data
   * set. So, 50% of the data lies below this point. The third quartile (Q3)
   * splits off the highest 25% of data from the lowest 75%. It is known as the
   * upper or 75th empirical quartile, as 75% of the data lies below this point.
   * Here, the quartiles is provided as an ordered list of approximate quartile
   * values for the scanned data, occurring in order Q1, median, Q3.
   *
   * @var string[]
   */
  public $quartiles;
  /**
   * Output only. Standard deviation of non-null values in the scanned data.
   * NaN, if the field has a NaN.
   *
   * @var 
   */
  public $standardDeviation;

  public function setAverage($average)
  {
    $this->average = $average;
  }
  public function getAverage()
  {
    return $this->average;
  }
  /**
   * Output only. Maximum of non-null values in the scanned data. NaN, if the
   * field has a NaN.
   *
   * @param string $max
   */
  public function setMax($max)
  {
    $this->max = $max;
  }
  /**
   * @return string
   */
  public function getMax()
  {
    return $this->max;
  }
  /**
   * Output only. Minimum of non-null values in the scanned data. NaN, if the
   * field has a NaN.
   *
   * @param string $min
   */
  public function setMin($min)
  {
    $this->min = $min;
  }
  /**
   * @return string
   */
  public function getMin()
  {
    return $this->min;
  }
  /**
   * Output only. A quartile divides the number of data points into four parts,
   * or quarters, of more-or-less equal size. Three main quartiles used are: The
   * first quartile (Q1) splits off the lowest 25% of data from the highest 75%.
   * It is also known as the lower or 25th empirical quartile, as 25% of the
   * data is below this point. The second quartile (Q2) is the median of a data
   * set. So, 50% of the data lies below this point. The third quartile (Q3)
   * splits off the highest 25% of data from the lowest 75%. It is known as the
   * upper or 75th empirical quartile, as 75% of the data lies below this point.
   * Here, the quartiles is provided as an ordered list of approximate quartile
   * values for the scanned data, occurring in order Q1, median, Q3.
   *
   * @param string[] $quartiles
   */
  public function setQuartiles($quartiles)
  {
    $this->quartiles = $quartiles;
  }
  /**
   * @return string[]
   */
  public function getQuartiles()
  {
    return $this->quartiles;
  }
  public function setStandardDeviation($standardDeviation)
  {
    $this->standardDeviation = $standardDeviation;
  }
  public function getStandardDeviation()
  {
    return $this->standardDeviation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoIntegerFieldInfo');
