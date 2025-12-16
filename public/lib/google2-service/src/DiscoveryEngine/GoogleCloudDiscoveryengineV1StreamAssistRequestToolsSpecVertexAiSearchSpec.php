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

class GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpecVertexAiSearchSpec extends \Google\Collection
{
  protected $collection_key = 'dataStoreSpecs';
  protected $dataStoreSpecsType = GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec::class;
  protected $dataStoreSpecsDataType = 'array';
  /**
   * Optional. The filter syntax consists of an expression language for
   * constructing a predicate from one or more fields of the documents being
   * filtered. Filter expression is case-sensitive. If this field is
   * unrecognizable, an `INVALID_ARGUMENT` is returned. Filtering in Vertex AI
   * Search is done by mapping the LHS filter key to a key property defined in
   * the Vertex AI Search backend -- this mapping is defined by the customer in
   * their schema. For example a media customer might have a field 'name' in
   * their schema. In this case the filter would look like this: filter -->
   * name:'ANY("king kong")' For more information about filtering including
   * syntax and filter operators, see
   * [Filter](https://cloud.google.com/generative-ai-app-builder/docs/filter-
   * search-metadata)
   *
   * @var string
   */
  public $filter;

  /**
   * Optional. Specs defining DataStores to filter on in a search call and
   * configurations for those data stores. This is only considered for Engines
   * with multiple data stores.
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec[] $dataStoreSpecs
   */
  public function setDataStoreSpecs($dataStoreSpecs)
  {
    $this->dataStoreSpecs = $dataStoreSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestDataStoreSpec[]
   */
  public function getDataStoreSpecs()
  {
    return $this->dataStoreSpecs;
  }
  /**
   * Optional. The filter syntax consists of an expression language for
   * constructing a predicate from one or more fields of the documents being
   * filtered. Filter expression is case-sensitive. If this field is
   * unrecognizable, an `INVALID_ARGUMENT` is returned. Filtering in Vertex AI
   * Search is done by mapping the LHS filter key to a key property defined in
   * the Vertex AI Search backend -- this mapping is defined by the customer in
   * their schema. For example a media customer might have a field 'name' in
   * their schema. In this case the filter would look like this: filter -->
   * name:'ANY("king kong")' For more information about filtering including
   * syntax and filter operators, see
   * [Filter](https://cloud.google.com/generative-ai-app-builder/docs/filter-
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
class_alias(GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpecVertexAiSearchSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpecVertexAiSearchSpec');
