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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig extends \Google\Collection
{
  protected $collection_key = 'facetField';
  protected $facetFieldType = GoogleCloudDiscoveryengineV1WidgetConfigFacetField::class;
  protected $facetFieldDataType = 'array';
  protected $fieldsUiComponentsMapType = GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField::class;
  protected $fieldsUiComponentsMapDataType = 'map';
  /**
   * Output only. the identifier of the data store, used for widget service. For
   * now it refers to data_store_id, in the future we will migrate the field to
   * encrypted data store name UUID.
   *
   * @var string
   */
  public $id;
  /**
   * The name of the data store. It should be data store resource name Format: `
   * projects/{project}/locations/{location}/collections/{collection_id}/dataSto
   * res/{data_store_id}`. For APIs under WidgetService, such as
   * WidgetService.LookUpWidgetConfig, the project number and location part is
   * erased in this field.
   *
   * @var string
   */
  public $name;

  /**
   * Facet fields that store the mapping of fields to end user widget
   * appearance.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigFacetField[] $facetField
   */
  public function setFacetField($facetField)
  {
    $this->facetField = $facetField;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigFacetField[]
   */
  public function getFacetField()
  {
    return $this->facetField;
  }
  /**
   * The key is the UI component. Mock. Currently supported `title`,
   * `thumbnail`, `url`, `custom1`, `custom2`, `custom3`. The value is the name
   * of the field along with its device visibility. The 3 custom fields are
   * optional and can be added or removed. `title`, `thumbnail`, `url` are
   * required UI components that cannot be removed.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField[] $fieldsUiComponentsMap
   */
  public function setFieldsUiComponentsMap($fieldsUiComponentsMap)
  {
    $this->fieldsUiComponentsMap = $fieldsUiComponentsMap;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField[]
   */
  public function getFieldsUiComponentsMap()
  {
    return $this->fieldsUiComponentsMap;
  }
  /**
   * Output only. the identifier of the data store, used for widget service. For
   * now it refers to data_store_id, in the future we will migrate the field to
   * encrypted data store name UUID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The name of the data store. It should be data store resource name Format: `
   * projects/{project}/locations/{location}/collections/{collection_id}/dataSto
   * res/{data_store_id}`. For APIs under WidgetService, such as
   * WidgetService.LookUpWidgetConfig, the project number and location part is
   * erased in this field.
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
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig');
