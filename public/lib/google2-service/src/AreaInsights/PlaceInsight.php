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

class PlaceInsight extends \Google\Model
{
  /**
   * The unique identifier of the place. This resource name can be used to
   * retrieve details about the place using the [Places
   * API](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places/get).
   *
   * @var string
   */
  public $place;

  /**
   * The unique identifier of the place. This resource name can be used to
   * retrieve details about the place using the [Places
   * API](https://developers.google.com/maps/documentation/places/web-
   * service/reference/rest/v1/places/get).
   *
   * @param string $place
   */
  public function setPlace($place)
  {
    $this->place = $place;
  }
  /**
   * @return string
   */
  public function getPlace()
  {
    return $this->place;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaceInsight::class, 'Google_Service_AreaInsights_PlaceInsight');
