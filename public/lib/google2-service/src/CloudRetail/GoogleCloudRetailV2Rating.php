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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2Rating extends \Google\Collection
{
  protected $collection_key = 'ratingHistogram';
  /**
   * The average rating of the Product. The rating is scaled at 1-5. Otherwise,
   * an INVALID_ARGUMENT error is returned.
   *
   * @var float
   */
  public $averageRating;
  /**
   * The total number of ratings. This value is independent of the value of
   * rating_histogram. This value must be nonnegative. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var int
   */
  public $ratingCount;
  /**
   * List of rating counts per rating value (index = rating - 1). The list is
   * empty if there is no rating. If the list is non-empty, its size is always
   * 5. Otherwise, an INVALID_ARGUMENT error is returned. For example, [41, 14,
   * 13, 47, 303]. It means that the Product got 41 ratings with 1 star, 14
   * ratings with 2 star, and so on.
   *
   * @var int[]
   */
  public $ratingHistogram;

  /**
   * The average rating of the Product. The rating is scaled at 1-5. Otherwise,
   * an INVALID_ARGUMENT error is returned.
   *
   * @param float $averageRating
   */
  public function setAverageRating($averageRating)
  {
    $this->averageRating = $averageRating;
  }
  /**
   * @return float
   */
  public function getAverageRating()
  {
    return $this->averageRating;
  }
  /**
   * The total number of ratings. This value is independent of the value of
   * rating_histogram. This value must be nonnegative. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @param int $ratingCount
   */
  public function setRatingCount($ratingCount)
  {
    $this->ratingCount = $ratingCount;
  }
  /**
   * @return int
   */
  public function getRatingCount()
  {
    return $this->ratingCount;
  }
  /**
   * List of rating counts per rating value (index = rating - 1). The list is
   * empty if there is no rating. If the list is non-empty, its size is always
   * 5. Otherwise, an INVALID_ARGUMENT error is returned. For example, [41, 14,
   * 13, 47, 303]. It means that the Product got 41 ratings with 1 star, 14
   * ratings with 2 star, and so on.
   *
   * @param int[] $ratingHistogram
   */
  public function setRatingHistogram($ratingHistogram)
  {
    $this->ratingHistogram = $ratingHistogram;
  }
  /**
   * @return int[]
   */
  public function getRatingHistogram()
  {
    return $this->ratingHistogram;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Rating::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Rating');
