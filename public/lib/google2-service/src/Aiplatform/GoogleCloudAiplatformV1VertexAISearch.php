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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1VertexAISearch extends \Google\Collection
{
  protected $collection_key = 'dataStoreSpecs';
  protected $dataStoreSpecsType = GoogleCloudAiplatformV1VertexAISearchDataStoreSpec::class;
  protected $dataStoreSpecsDataType = 'array';
  /**
   * Optional. Fully-qualified Vertex AI Search data store resource ID. Format:
   * `projects/{project}/locations/{location}/collections/{collection}/dataStore
   * s/{dataStore}`
   *
   * @var string
   */
  public $datastore;
  /**
   * Optional. Fully-qualified Vertex AI Search engine resource ID. Format: `pro
   * jects/{project}/locations/{location}/collections/{collection}/engines/{engi
   * ne}`
   *
   * @var string
   */
  public $engine;
  /**
   * Optional. Filter strings to be passed to the search API.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. Number of search results to return per query. The default value
   * is 10. The maximumm allowed value is 10.
   *
   * @var int
   */
  public $maxResults;

  /**
   * Specifications that define the specific DataStores to be searched, along
   * with configurations for those data stores. This is only considered for
   * Engines with multiple data stores. It should only be set if engine is used.
   *
   * @param GoogleCloudAiplatformV1VertexAISearchDataStoreSpec[] $dataStoreSpecs
   */
  public function setDataStoreSpecs($dataStoreSpecs)
  {
    $this->dataStoreSpecs = $dataStoreSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1VertexAISearchDataStoreSpec[]
   */
  public function getDataStoreSpecs()
  {
    return $this->dataStoreSpecs;
  }
  /**
   * Optional. Fully-qualified Vertex AI Search data store resource ID. Format:
   * `projects/{project}/locations/{location}/collections/{collection}/dataStore
   * s/{dataStore}`
   *
   * @param string $datastore
   */
  public function setDatastore($datastore)
  {
    $this->datastore = $datastore;
  }
  /**
   * @return string
   */
  public function getDatastore()
  {
    return $this->datastore;
  }
  /**
   * Optional. Fully-qualified Vertex AI Search engine resource ID. Format: `pro
   * jects/{project}/locations/{location}/collections/{collection}/engines/{engi
   * ne}`
   *
   * @param string $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return string
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Optional. Filter strings to be passed to the search API.
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
  /**
   * Optional. Number of search results to return per query. The default value
   * is 10. The maximumm allowed value is 10.
   *
   * @param int $maxResults
   */
  public function setMaxResults($maxResults)
  {
    $this->maxResults = $maxResults;
  }
  /**
   * @return int
   */
  public function getMaxResults()
  {
    return $this->maxResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1VertexAISearch::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1VertexAISearch');
