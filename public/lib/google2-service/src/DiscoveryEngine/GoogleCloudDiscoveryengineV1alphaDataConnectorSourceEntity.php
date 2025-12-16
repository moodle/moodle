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

class GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity extends \Google\Model
{
  /**
   * Output only. The full resource name of the associated data store for the
   * source entity. Format: `projects/locations/collections/dataStores`. When
   * the connector is initialized by the DataConnectorService.SetUpDataConnector
   * method, a DataStore is automatically created for each source entity.
   *
   * @var string
   */
  public $dataStore;
  /**
   * The name of the entity. Supported values by data source: * Salesforce:
   * `Lead`, `Opportunity`, `Contact`, `Account`, `Case`, `Contract`, `Campaign`
   * * Jira: `Issue` * Confluence: `Content`, `Space`
   *
   * @var string
   */
  public $entityName;
  protected $healthcareFhirConfigType = GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig::class;
  protected $healthcareFhirConfigDataType = '';
  /**
   * The parameters for the entity to facilitate data ingestion in json string
   * format.
   *
   * @var string
   */
  public $jsonParams;
  /**
   * Attributes for indexing. Key: Field name. Value: The key property to map a
   * field to, such as `title`, and `description`. Supported key properties: *
   * `title`: The title for data record. This would be displayed on search
   * results. * `description`: The description for data record. This would be
   * displayed on search results.
   *
   * @var string[]
   */
  public $keyPropertyMappings;
  /**
   * The parameters for the entity to facilitate data ingestion in structured
   * json format.
   *
   * @var array[]
   */
  public $params;
  protected $startingSchemaType = GoogleCloudDiscoveryengineV1alphaSchema::class;
  protected $startingSchemaDataType = '';

  /**
   * Output only. The full resource name of the associated data store for the
   * source entity. Format: `projects/locations/collections/dataStores`. When
   * the connector is initialized by the DataConnectorService.SetUpDataConnector
   * method, a DataStore is automatically created for each source entity.
   *
   * @param string $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  /**
   * The name of the entity. Supported values by data source: * Salesforce:
   * `Lead`, `Opportunity`, `Contact`, `Account`, `Case`, `Contract`, `Campaign`
   * * Jira: `Issue` * Confluence: `Content`, `Space`
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
   * Optional. Configuration for `HEALTHCARE_FHIR` vertical.
   *
   * @param GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig $healthcareFhirConfig
   */
  public function setHealthcareFhirConfig(GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig $healthcareFhirConfig)
  {
    $this->healthcareFhirConfig = $healthcareFhirConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig
   */
  public function getHealthcareFhirConfig()
  {
    return $this->healthcareFhirConfig;
  }
  /**
   * The parameters for the entity to facilitate data ingestion in json string
   * format.
   *
   * @param string $jsonParams
   */
  public function setJsonParams($jsonParams)
  {
    $this->jsonParams = $jsonParams;
  }
  /**
   * @return string
   */
  public function getJsonParams()
  {
    return $this->jsonParams;
  }
  /**
   * Attributes for indexing. Key: Field name. Value: The key property to map a
   * field to, such as `title`, and `description`. Supported key properties: *
   * `title`: The title for data record. This would be displayed on search
   * results. * `description`: The description for data record. This would be
   * displayed on search results.
   *
   * @param string[] $keyPropertyMappings
   */
  public function setKeyPropertyMappings($keyPropertyMappings)
  {
    $this->keyPropertyMappings = $keyPropertyMappings;
  }
  /**
   * @return string[]
   */
  public function getKeyPropertyMappings()
  {
    return $this->keyPropertyMappings;
  }
  /**
   * The parameters for the entity to facilitate data ingestion in structured
   * json format.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional. The start schema to use for the DataStore created from this
   * SourceEntity. If unset, a default vertical specialized schema will be used.
   * This field is only used by SetUpDataConnector API, and will be ignored if
   * used in other APIs. This field will be omitted from all API responses
   * including GetDataConnector API. To retrieve a schema of a DataStore, use
   * SchemaService.GetSchema API instead. The provided schema will be validated
   * against certain rules on schema. Learn more from [this
   * doc](https://cloud.google.com/generative-ai-app-builder/docs/provide-
   * schema).
   *
   * @param GoogleCloudDiscoveryengineV1alphaSchema $startingSchema
   */
  public function setStartingSchema(GoogleCloudDiscoveryengineV1alphaSchema $startingSchema)
  {
    $this->startingSchema = $startingSchema;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSchema
   */
  public function getStartingSchema()
  {
    return $this->startingSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity');
