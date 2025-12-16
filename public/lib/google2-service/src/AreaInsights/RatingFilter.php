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

class RatingFilter extends \Google\Model
{
  /**
   * Optional. Restricts results to places whose average user rating is strictly
   * less than or equal to max_rating. Values must be between 1.0 and 5.0.
   *
   * @var float
   */
  public $maxRating;
  /**
   * Optional. Restricts results to places whose average user rating is greater
   * than or equal to min_rating. Values must be between 1.0 and 5.0.
   *
   * @var float
   */
  public $minRating;

  /**
   * Optional. Restricts results to places whose average user rating is strictly
   * less than or equal to max_rating. Values must be between 1.0 and 5.0.
   *
   * @param float $maxRating
   */
  public function setMaxRating($maxRating)
  {
    $this->maxRating = $maxRating;
  }
  /**
   * @return float
   */
  public function getMaxRating()
  {
    return $this->maxRating;
  }
  /**
   * Optional. Restricts results to places whose average user rating is greater
   * than or equal to min_rating. Values must be between 1.0 and 5.0.
   *
   * @param float $minRating
   */
  public function setMinRating($minRating)
  {
    $this->minRating = $minRating;
  }
  /**
   * @return float
   */
  public function getMinRating()
  {
    return $this->minRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RatingFilter::class, 'Google_Service_AreaInsights_RatingFilter');
