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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1GraphSpecGraphElementTableLabelAndProperties extends \Google\Collection
{
  protected $collection_key = 'properties';
  /**
   * Required. The name of the label.
   *
   * @var string
   */
  public $label;
  protected $propertiesType = GoogleCloudDatacatalogV1GraphSpecGraphElementTableProperty::class;
  protected $propertiesDataType = 'array';

  /**
   * Required. The name of the label.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Optional. The properties associated with the label.
   *
   * @param GoogleCloudDatacatalogV1GraphSpecGraphElementTableProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudDatacatalogV1GraphSpecGraphElementTableProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1GraphSpecGraphElementTableLabelAndProperties::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1GraphSpecGraphElementTableLabelAndProperties');
