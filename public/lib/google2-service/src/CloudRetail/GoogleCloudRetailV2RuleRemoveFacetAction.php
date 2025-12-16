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

class GoogleCloudRetailV2RuleRemoveFacetAction extends \Google\Collection
{
  protected $collection_key = 'attributeNames';
  /**
   * The attribute names (i.e. facet keys) to remove from the dynamic facets (if
   * present in the request). There can't be more 3 attribute names. Each
   * attribute name should be a valid attribute name, be non-empty and contain
   * at most 80 characters.
   *
   * @var string[]
   */
  public $attributeNames;

  /**
   * The attribute names (i.e. facet keys) to remove from the dynamic facets (if
   * present in the request). There can't be more 3 attribute names. Each
   * attribute name should be a valid attribute name, be non-empty and contain
   * at most 80 characters.
   *
   * @param string[] $attributeNames
   */
  public function setAttributeNames($attributeNames)
  {
    $this->attributeNames = $attributeNames;
  }
  /**
   * @return string[]
   */
  public function getAttributeNames()
  {
    return $this->attributeNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RuleRemoveFacetAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RuleRemoveFacetAction');
