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

namespace Google\Service\Spanner;

class SingleRegionQuorum extends \Google\Model
{
  /**
   * Required. The location of the serving region, for example, "us-central1".
   * The location must be one of the regions within the dual-region instance
   * configuration of your database. The list of valid locations is available
   * using the GetInstanceConfig API. This should only be used if you plan to
   * change quorum to the single-region quorum type.
   *
   * @var string
   */
  public $servingLocation;

  /**
   * Required. The location of the serving region, for example, "us-central1".
   * The location must be one of the regions within the dual-region instance
   * configuration of your database. The list of valid locations is available
   * using the GetInstanceConfig API. This should only be used if you plan to
   * change quorum to the single-region quorum type.
   *
   * @param string $servingLocation
   */
  public function setServingLocation($servingLocation)
  {
    $this->servingLocation = $servingLocation;
  }
  /**
   * @return string
   */
  public function getServingLocation()
  {
    return $this->servingLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SingleRegionQuorum::class, 'Google_Service_Spanner_SingleRegionQuorum');
