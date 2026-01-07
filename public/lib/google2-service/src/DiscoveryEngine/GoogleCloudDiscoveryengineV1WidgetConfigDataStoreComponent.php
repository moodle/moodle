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

class GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const DATA_STORE_CONFIG_TYPE_DATA_STORE_CONFIG_TYPE_UNSPECIFIED = 'DATA_STORE_CONFIG_TYPE_UNSPECIFIED';
  /**
   * The data store is connected to AlloyDB
   */
  public const DATA_STORE_CONFIG_TYPE_ALLOW_DB_CONFIG = 'ALLOW_DB_CONFIG';
  /**
   * The data store is a connected to a third party data source.
   */
  public const DATA_STORE_CONFIG_TYPE_THIRD_PARTY_OAUTH_CONFIG = 'THIRD_PARTY_OAUTH_CONFIG';
  /**
   * The data store is a connected to NotebookLM Enterprise.
   */
  public const DATA_STORE_CONFIG_TYPE_NOTEBOOKLM_CONFIG = 'NOTEBOOKLM_CONFIG';
  /**
   * Output only. The type of the data store config.
   *
   * @var string
   */
  public $dataStoreConfigType;
  /**
   * The display name of the data store.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the entity, retrieved from
   * `Collection.data_connector.entities.entityName`.
   *
   * @var string
   */
  public $entityName;
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
   * Output only. The type of the data store config.
   *
   * Accepted values: DATA_STORE_CONFIG_TYPE_UNSPECIFIED, ALLOW_DB_CONFIG,
   * THIRD_PARTY_OAUTH_CONFIG, NOTEBOOKLM_CONFIG
   *
   * @param self::DATA_STORE_CONFIG_TYPE_* $dataStoreConfigType
   */
  public function setDataStoreConfigType($dataStoreConfigType)
  {
    $this->dataStoreConfigType = $dataStoreConfigType;
  }
  /**
   * @return self::DATA_STORE_CONFIG_TYPE_*
   */
  public function getDataStoreConfigType()
  {
    return $this->dataStoreConfigType;
  }
  /**
   * The display name of the data store.
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
   * The name of the entity, retrieved from
   * `Collection.data_connector.entities.entityName`.
   *
   * @param string $entityName
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;
  }
  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
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
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigDataStoreComponent');
