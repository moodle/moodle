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

class GoogleCloudDataplexV1ZoneResourceSpec extends \Google\Model
{
  /**
   * Unspecified location type.
   */
  public const LOCATION_TYPE_LOCATION_TYPE_UNSPECIFIED = 'LOCATION_TYPE_UNSPECIFIED';
  /**
   * Resources that are associated with a single region.
   */
  public const LOCATION_TYPE_SINGLE_REGION = 'SINGLE_REGION';
  /**
   * Resources that are associated with a multi-region location.
   */
  public const LOCATION_TYPE_MULTI_REGION = 'MULTI_REGION';
  /**
   * Required. Immutable. The location type of the resources that are allowed to
   * be attached to the assets within this zone.
   *
   * @var string
   */
  public $locationType;

  /**
   * Required. Immutable. The location type of the resources that are allowed to
   * be attached to the assets within this zone.
   *
   * Accepted values: LOCATION_TYPE_UNSPECIFIED, SINGLE_REGION, MULTI_REGION
   *
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ZoneResourceSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ZoneResourceSpec');
