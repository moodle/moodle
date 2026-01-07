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

class CustomArea extends \Google\Model
{
  protected $polygonType = Polygon::class;
  protected $polygonDataType = '';

  /**
   * Required. The custom area represented as a polygon
   *
   * @param Polygon $polygon
   */
  public function setPolygon(Polygon $polygon)
  {
    $this->polygon = $polygon;
  }
  /**
   * @return Polygon
   */
  public function getPolygon()
  {
    return $this->polygon;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomArea::class, 'Google_Service_AreaInsights_CustomArea');
