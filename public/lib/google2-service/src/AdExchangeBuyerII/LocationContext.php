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

class LocationContext extends \Google\Collection
{
  protected $collection_key = 'geoCriteriaIds';
  /**
   * IDs representing the geo location for this context. Refer to the [geo-
   * table.csv](https://storage.googleapis.com/adx-rtb-dictionaries/geo-
   * table.csv) file for different geo criteria IDs.
   *
   * @var int[]
   */
  public $geoCriteriaIds;

  /**
   * IDs representing the geo location for this context. Refer to the [geo-
   * table.csv](https://storage.googleapis.com/adx-rtb-dictionaries/geo-
   * table.csv) file for different geo criteria IDs.
   *
   * @param int[] $geoCriteriaIds
   */
  public function setGeoCriteriaIds($geoCriteriaIds)
  {
    $this->geoCriteriaIds = $geoCriteriaIds;
  }
  /**
   * @return int[]
   */
  public function getGeoCriteriaIds()
  {
    return $this->geoCriteriaIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationContext::class, 'Google_Service_AdExchangeBuyerII_LocationContext');
