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

class GoogleCloudDiscoveryengineV1FhirStoreSource extends \Google\Collection
{
  protected $collection_key = 'resourceTypes';
  /**
   * Required. The full resource name of the FHIR store to import data from, in
   * the format of `projects/{project}/locations/{location}/datasets/{dataset}/f
   * hirStores/{fhir_store}`.
   *
   * @var string
   */
  public $fhirStore;
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * FhirStore export to a specific Cloud Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  /**
   * The FHIR resource types to import. The resource types should be a subset of
   * all [supported FHIR resource types](https://cloud.google.com/generative-ai-
   * app-builder/docs/fhir-schema-reference#resource-level-specification).
   * Default to all supported FHIR resource types if empty.
   *
   * @var string[]
   */
  public $resourceTypes;
  /**
   * Optional. Whether to update the DataStore schema to the latest predefined
   * schema. If true, the DataStore schema will be updated to include any FHIR
   * fields or resource types that have been added since the last import and
   * corresponding FHIR resources will be imported from the FHIR store. Note
   * this field cannot be used in conjunction with `resource_types`. It should
   * be used after initial import.
   *
   * @var bool
   */
  public $updateFromLatestPredefinedSchema;

  /**
   * Required. The full resource name of the FHIR store to import data from, in
   * the format of `projects/{project}/locations/{location}/datasets/{dataset}/f
   * hirStores/{fhir_store}`.
   *
   * @param string $fhirStore
   */
  public function setFhirStore($fhirStore)
  {
    $this->fhirStore = $fhirStore;
  }
  /**
   * @return string
   */
  public function getFhirStore()
  {
    return $this->fhirStore;
  }
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * FhirStore export to a specific Cloud Storage directory.
   *
   * @param string $gcsStagingDir
   */
  public function setGcsStagingDir($gcsStagingDir)
  {
    $this->gcsStagingDir = $gcsStagingDir;
  }
  /**
   * @return string
   */
  public function getGcsStagingDir()
  {
    return $this->gcsStagingDir;
  }
  /**
   * The FHIR resource types to import. The resource types should be a subset of
   * all [supported FHIR resource types](https://cloud.google.com/generative-ai-
   * app-builder/docs/fhir-schema-reference#resource-level-specification).
   * Default to all supported FHIR resource types if empty.
   *
   * @param string[] $resourceTypes
   */
  public function setResourceTypes($resourceTypes)
  {
    $this->resourceTypes = $resourceTypes;
  }
  /**
   * @return string[]
   */
  public function getResourceTypes()
  {
    return $this->resourceTypes;
  }
  /**
   * Optional. Whether to update the DataStore schema to the latest predefined
   * schema. If true, the DataStore schema will be updated to include any FHIR
   * fields or resource types that have been added since the last import and
   * corresponding FHIR resources will be imported from the FHIR store. Note
   * this field cannot be used in conjunction with `resource_types`. It should
   * be used after initial import.
   *
   * @param bool $updateFromLatestPredefinedSchema
   */
  public function setUpdateFromLatestPredefinedSchema($updateFromLatestPredefinedSchema)
  {
    $this->updateFromLatestPredefinedSchema = $updateFromLatestPredefinedSchema;
  }
  /**
   * @return bool
   */
  public function getUpdateFromLatestPredefinedSchema()
  {
    return $this->updateFromLatestPredefinedSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1FhirStoreSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1FhirStoreSource');
