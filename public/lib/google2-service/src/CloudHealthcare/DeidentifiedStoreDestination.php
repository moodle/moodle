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

class DeidentifiedStoreDestination extends \Google\Model
{
  protected $configType = DeidentifyConfig::class;
  protected $configDataType = '';
  /**
   * Optional. The full resource name of a Cloud Healthcare FHIR store, for
   * example, `projects/{project_id}/locations/{location_id}/datasets/{dataset_i
   * d}/fhirStores/{fhir_store_id}`.
   *
   * @var string
   */
  public $store;

  /**
   * Optional. The configuration to use when de-identifying resources that are
   * added to this store.
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
   * Optional. The full resource name of a Cloud Healthcare FHIR store, for
   * example, `projects/{project_id}/locations/{location_id}/datasets/{dataset_i
   * d}/fhirStores/{fhir_store_id}`.
   *
   * @param string $store
   */
  public function setStore($store)
  {
    $this->store = $store;
  }
  /**
   * @return string
   */
  public function getStore()
  {
    return $this->store;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeidentifiedStoreDestination::class, 'Google_Service_CloudHealthcare_DeidentifiedStoreDestination');
