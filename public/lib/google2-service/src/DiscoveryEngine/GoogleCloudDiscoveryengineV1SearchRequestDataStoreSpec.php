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

class GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec extends \Google\Model
{
  protected $boostSpecType = GoogleCloudDiscoveryengineV1SearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * Optional. Custom search operators which if specified will be used to filter
   * results from workspace data stores. For more information on custom search
   * operators, see
   * [SearchOperators](https://support.google.com/cloudsearch/answer/6172299).
   *
   * @var string
   */
  public $customSearchOperators;
  /**
   * Required. Full resource name of DataStore, such as `projects/{project}/loca
   * tions/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
   * The path must include the project number, project id is not supported for
   * this field.
   *
   * @var string
   */
  public $dataStore;
  /**
   * Optional. Filter specification to filter documents in the data store
   * specified by data_store field. For more information on filtering, see
   * [Filtering](https://cloud.google.com/generative-ai-app-builder/docs/filter-
   * search-metadata)
   *
   * @var string
   */
  public $filter;

  /**
   * Optional. Boost specification to boost certain documents. For more
   * information on boosting, see
   * [Boosting](https://cloud.google.com/generative-ai-app-builder/docs/boost-
   * search-results)
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * Optional. Custom search operators which if specified will be used to filter
   * results from workspace data stores. For more information on custom search
   * operators, see
   * [SearchOperators](https://support.google.com/cloudsearch/answer/6172299).
   *
   * @param string $customSearchOperators
   */
  public function setCustomSearchOperators($customSearchOperators)
  {
    $this->customSearchOperators = $customSearchOperators;
  }
  /**
   * @return string
   */
  public function getCustomSearchOperators()
  {
    return $this->customSearchOperators;
  }
  /**
   * Required. Full resource name of DataStore, such as `projects/{project}/loca
   * tions/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
   * The path must include the project number, project id is not supported for
   * this field.
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
   * Optional. Filter specification to filter documents in the data store
   * specified by data_store field. For more information on filtering, see
   * [Filtering](https://cloud.google.com/generative-ai-app-builder/docs/filter-
   * search-metadata)
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
class_alias(GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec');
