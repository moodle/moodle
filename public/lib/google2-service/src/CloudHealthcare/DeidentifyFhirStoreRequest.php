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

namespace Google\Service\CloudHealthcare;

class DeidentifyFhirStoreRequest extends \Google\Model
{
  protected $configType = DeidentifyConfig::class;
  protected $configDataType = '';
  /**
   * Required. The name of the FHIR store to create and write the redacted data
   * to. For example, `projects/{project_id}/locations/{location_id}/datasets/{d
   * ataset_id}/fhirStores/{fhir_store_id}`. * The destination dataset must
   * exist. * The source dataset and destination dataset must both reside in the
   * same location. De-identifying data across multiple locations is not
   * supported. * The destination FHIR store must exist. * The caller must have
   * the healthcare.fhirResources.update permission to write to the destination
   * FHIR store.
   *
   * @var string
   */
  public $destinationStore;
  /**
   * Cloud Storage location to read the JSON
   * cloud.healthcare.deidentify.DeidentifyConfig from, overriding the default
   * config. Must be of the form `gs://{bucket_id}/path/to/object`. The Cloud
   * Storage location must grant the Cloud IAM role `roles/storage.objectViewer`
   * to the project's Cloud Healthcare Service Agent service account. Only one
   * of `config` and `gcs_config_uri` can be specified.
   *
   * @var string
   */
  public $gcsConfigUri;
  protected $resourceFilterType = FhirFilter::class;
  protected $resourceFilterDataType = '';
  /**
   * If true, skips resources that are created or modified after the de-identify
   * operation is created.
   *
   * @var bool
   */
  public $skipModifiedResources;

  /**
   * Deidentify configuration. Only one of `config` and `gcs_config_uri` can be
   * specified.
   *
   * @param DeidentifyConfig $config
   */
  public function setConfig(DeidentifyConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return DeidentifyConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Required. The name of the FHIR store to create and write the redacted data
   * to. For example, `projects/{project_id}/locations/{location_id}/datasets/{d
   * ataset_id}/fhirStores/{fhir_store_id}`. * The destination dataset must
   * exist. * The source dataset and destination dataset must both reside in the
   * same location. De-identifying data across multiple locations is not
   * supported. * The destination FHIR store must exist. * The caller must have
   * the healthcare.fhirResources.update permission to write to the destination
   * FHIR store.
   *
   * @param string $destinationStore
   */
  public function setDestinationStore($destinationStore)
  {
    $this->destinationStore = $destinationStore;
  }
  /**
   * @return string
   */
  public function getDestinationStore()
  {
    return $this->destinationStore;
  }
  /**
   * Cloud Storage location to read the JSON
   * cloud.healthcare.deidentify.DeidentifyConfig from, overriding the default
   * config. Must be of the form `gs://{bucket_id}/path/to/object`. The Cloud
   * Storage location must grant the Cloud IAM role `roles/storage.objectViewer`
   * to the project's Cloud Healthcare Service Agent service account. Only one
   * of `config` and `gcs_config_uri` can be specified.
   *
   * @param string $gcsConfigUri
   */
  public function setGcsConfigUri($gcsConfigUri)
  {
    $this->gcsConfigUri = $gcsConfigUri;
  }
  /**
   * @return string
   */
  public function getGcsConfigUri()
  {
    return $this->gcsConfigUri;
  }
  /**
   * A filter specifying the resources to include in the output. If not
   * specified, all resources are included in the output.
   *
   * @param FhirFilter $resourceFilter
   */
  public function setResourceFilter(FhirFilter $resourceFilter)
  {
    $this->resourceFilter = $resourceFilter;
  }
  /**
   * @return FhirFilter
   */
  public function getResourceFilter()
  {
    return $this->resourceFilter;
  }
  /**
   * If true, skips resources that are created or modified after the de-identify
   * operation is created.
   *
   * @param bool $skipModifiedResources
   */
  public function setSkipModifiedResources($skipModifiedResources)
  {
    $this->skipModifiedResources = $skipModifiedResources;
  }
  /**
   * @return bool
   */
  public function getSkipModifiedResources()
  {
    return $this->skipModifiedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeidentifyFhirStoreRequest::class, 'Google_Service_CloudHealthcare_DeidentifyFhirStoreRequest');
