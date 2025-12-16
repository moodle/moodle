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

namespace Google\Service\CloudSearch;

class ObjectPropertyOptions extends \Google\Collection
{
  protected $collection_key = 'subobjectProperties';
  protected $subobjectPropertiesType = PropertyDefinition::class;
  protected $subobjectPropertiesDataType = 'array';

  /**
   * The properties of the sub-object. These properties represent a nested
   * object. For example, if this property represents a postal address, the
   * subobjectProperties might be named *street*, *city*, and *state*. The
   * maximum number of elements is 1000.
   *
   * @param PropertyDefinition[] $subobjectProperties
   */
  public function setSubobjectProperties($subobjectProperties)
  {
    $this->subobjectProperties = $subobjectProperties;
  }
  /**
   * @return PropertyDefinition[]
   */
  public function getSubobjectProperties()
  {
    return $this->subobjectProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectPropertyOptions::class, 'Google_Service_CloudSearch_ObjectPropertyOptions');
