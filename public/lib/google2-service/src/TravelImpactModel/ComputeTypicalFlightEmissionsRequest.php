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

namespace Google\Service\TravelImpactModel;

class ComputeTypicalFlightEmissionsRequest extends \Google\Collection
{
  protected $collection_key = 'markets';
  protected $marketsType = Market::class;
  protected $marketsDataType = 'array';

  /**
   * Required. Request the typical flight emissions estimates for this market
   * pair. A maximum of 1000 markets can be requested.
   *
   * @param Market[] $markets
   */
  public function setMarkets($markets)
  {
    $this->markets = $markets;
  }
  /**
   * @return Market[]
   */
  public function getMarkets()
  {
    return $this->markets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeTypicalFlightEmissionsRequest::class, 'Google_Service_TravelImpactModel_ComputeTypicalFlightEmissionsRequest');
