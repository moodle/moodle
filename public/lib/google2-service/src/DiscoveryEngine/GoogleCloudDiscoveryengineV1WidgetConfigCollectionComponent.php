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

class GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent extends \Google\Collection
{
  protected $collection_key = 'dataStoreComponents';
  /**
   * Output only. The icon link of the connector source.
   *
   * @var string
   */
  public $connectorIconLink;
  /**
   * The name of the data source, retrieved from
   * `Collection.data_connector.data_source`.
   *
   * @var string
   */
  public $dataSource;
  /**
   * Output only. The display name of the data source.
   *
   * @var string
   */
  public $dataSourceDisplayName;
  protected $dataStoreComponentsType = GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent::class;
  protected $dataStoreComponentsDataType = 'array';
  /**
   * The display name of the collection.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. the identifier of the collection, used for widget service. For
   * now it refers to collection_id, in the future we will migrate the field to
   * encrypted collection name UUID.
   *
   * @var string
   */
  public $id;
  /**
   * The name of the collection. It should be collection resource name. Format:
   * `projects/{project}/locations/{location}/collections/{collection_id}`. For
   * APIs under WidgetService, such as WidgetService.LookUpWidgetConfig, the
   * project number and location part is erased in this field.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The icon link of the connector source.
   *
   * @param string $connectorIconLink
   */
  public function setConnectorIconLink($connectorIconLink)
  {
    $this->connectorIconLink = $connectorIconLink;
  }
  /**
   * @return string
   */
  public function getConnectorIconLink()
  {
    return $this->connectorIconLink;
  }
  /**
   * The name of the data source, retrieved from
   * `Collection.data_connector.data_source`.
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Output only. The display name of the data source.
   *
   * @param string $dataSourceDisplayName
   */
  public function setDataSourceDisplayName($dataSourceDisplayName)
  {
    $this->dataSourceDisplayName = $dataSourceDisplayName;
  }
  /**
   * @return string
   */
  public function getDataSourceDisplayName()
  {
    return $this->dataSourceDisplayName;
  }
  /**
   * For the data store collection, list of the children data stores.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent[] $dataStoreComponents
   */
  public function setDataStoreComponents($dataStoreComponents)
  {
    $this->dataStoreComponents = $dataStoreComponents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent[]
   */
  public function getDataStoreComponents()
  {
    return $this->dataStoreComponents;
  }
  /**
   * The display name of the collection.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. the identifier of the collection, used for widget service. For
   * now it refers to collection_id, in the future we will migrate the field to
   * encrypted collection name UUID.
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
   * The name of the collection. It should be collection resource name. Format:
   * `projects/{project}/locations/{location}/collections/{collection_id}`. For
   * APIs under WidgetService, such as WidgetService.LookUpWidgetConfig, the
   * project number and location part is erased in this field.
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
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent');
