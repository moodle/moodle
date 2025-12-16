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

class GoogleCloudDataplexV1DataAttributeBindingPath extends \Google\Collection
{
  protected $collection_key = 'attributes';
  /**
   * Optional. List of attributes to be associated with the path of the
   * resource, provided in the form: projects/{project}/locations/{location}/dat
   * aTaxonomies/{dataTaxonomy}/attributes/{data_attribute_id}
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Required. The name identifier of the path. Nested columns should be of the
   * form: 'address.city'.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. List of attributes to be associated with the path of the
   * resource, provided in the form: projects/{project}/locations/{location}/dat
   * aTaxonomies/{dataTaxonomy}/attributes/{data_attribute_id}
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Required. The name identifier of the path. Nested columns should be of the
   * form: 'address.city'.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataAttributeBindingPath::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataAttributeBindingPath');
