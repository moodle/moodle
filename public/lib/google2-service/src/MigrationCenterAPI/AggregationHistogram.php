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

namespace Google\Service\MigrationCenterAPI;

class AggregationHistogram extends \Google\Collection
{
  protected $collection_key = 'lowerBounds';
  /**
   * Lower bounds of buckets. The response will contain `n+1` buckets for `n`
   * bounds. The first bucket will count all assets for which the field value is
   * smaller than the first bound. Subsequent buckets will count assets for
   * which the field value is greater or equal to a lower bound and smaller than
   * the next one. The last bucket will count assets for which the field value
   * is greater or equal to the final lower bound. You can define up to 20 lower
   * bounds.
   *
   * @var []
   */
  public $lowerBounds;

  public function setLowerBounds($lowerBounds)
  {
    $this->lowerBounds = $lowerBounds;
  }
  public function getLowerBounds()
  {
    return $this->lowerBounds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationHistogram::class, 'Google_Service_MigrationCenterAPI_AggregationHistogram');
