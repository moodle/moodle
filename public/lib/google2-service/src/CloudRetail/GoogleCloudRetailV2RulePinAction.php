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

class GoogleCloudRetailV2RulePinAction extends \Google\Model
{
  /**
   * Required. A map of positions to product_ids. Partial matches per action are
   * allowed, if a certain position in the map is already filled that
   * `[position, product_id]` pair will be ignored but the rest may still be
   * applied. This case will only occur if multiple pin actions are matched to a
   * single request, as the map guarantees that pin positions are unique within
   * the same action. Duplicate product_ids are not permitted within a single
   * pin map. The max size of this map is 120, equivalent to the max [request
   * page size](https://cloud.google.com/retail/docs/reference/rest/v2/projects.
   * locations.catalogs.placements/search#request-body).
   *
   * @var string[]
   */
  public $pinMap;

  /**
   * Required. A map of positions to product_ids. Partial matches per action are
   * allowed, if a certain position in the map is already filled that
   * `[position, product_id]` pair will be ignored but the rest may still be
   * applied. This case will only occur if multiple pin actions are matched to a
   * single request, as the map guarantees that pin positions are unique within
   * the same action. Duplicate product_ids are not permitted within a single
   * pin map. The max size of this map is 120, equivalent to the max [request
   * page size](https://cloud.google.com/retail/docs/reference/rest/v2/projects.
   * locations.catalogs.placements/search#request-body).
   *
   * @param string[] $pinMap
   */
  public function setPinMap($pinMap)
  {
    $this->pinMap = $pinMap;
  }
  /**
   * @return string[]
   */
  public function getPinMap()
  {
    return $this->pinMap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RulePinAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RulePinAction');
