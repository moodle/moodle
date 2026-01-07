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

class GoogleCloudDiscoveryengineV1ControlFilterAction extends \Google\Model
{
  /**
   * Required. Specifies which data store's documents can be filtered by this
   * control. Full data store name e.g. projects/123/locations/global/collection
   * s/default_collection/dataStores/default_data_store
   *
   * @var string
   */
  public $dataStore;
  /**
   * Required. A filter to apply on the matching condition results. Required
   * Syntax documentation: https://cloud.google.com/retail/docs/filter-and-order
   * Maximum length is 5000 characters. Otherwise an INVALID ARGUMENT error is
   * thrown.
   *
   * @var string
   */
  public $filter;

  /**
   * Required. Specifies which data store's documents can be filtered by this
   * control. Full data store name e.g. projects/123/locations/global/collection
   * s/default_collection/dataStores/default_data_store
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
   * Required. A filter to apply on the matching condition results. Required
   * Syntax documentation: https://cloud.google.com/retail/docs/filter-and-order
   * Maximum length is 5000 characters. Otherwise an INVALID ARGUMENT error is
   * thrown.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ControlFilterAction::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ControlFilterAction');
