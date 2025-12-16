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

class GoogleCloudRetailV2RuleForceReturnFacetAction extends \Google\Collection
{
  protected $collection_key = 'facetPositionAdjustments';
  protected $facetPositionAdjustmentsType = GoogleCloudRetailV2RuleForceReturnFacetActionFacetPositionAdjustment::class;
  protected $facetPositionAdjustmentsDataType = 'array';

  /**
   * Each instance corresponds to a force return attribute for the given
   * condition. There can't be more 15 instances here.
   *
   * @param GoogleCloudRetailV2RuleForceReturnFacetActionFacetPositionAdjustment[] $facetPositionAdjustments
   */
  public function setFacetPositionAdjustments($facetPositionAdjustments)
  {
    $this->facetPositionAdjustments = $facetPositionAdjustments;
  }
  /**
   * @return GoogleCloudRetailV2RuleForceReturnFacetActionFacetPositionAdjustment[]
   */
  public function getFacetPositionAdjustments()
  {
    return $this->facetPositionAdjustments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RuleForceReturnFacetAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RuleForceReturnFacetAction');
