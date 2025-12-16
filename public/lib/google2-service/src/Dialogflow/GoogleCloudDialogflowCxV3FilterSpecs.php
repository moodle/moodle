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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3FilterSpecs extends \Google\Collection
{
  protected $collection_key = 'dataStores';
  /**
   * Optional. Data Stores where the boosting configuration is applied. The full
   * names of the referenced data stores. Formats: `projects/{project}/locations
   * /{location}/collections/{collection}/dataStores/{data_store}`
   * `projects/{project}/locations/{location}/dataStores/{data_store}`
   *
   * @var string[]
   */
  public $dataStores;
  /**
   * Optional. The filter expression to be applied. Expression syntax is
   * documented at https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata#filter-expression-syntax
   *
   * @var string
   */
  public $filter;

  /**
   * Optional. Data Stores where the boosting configuration is applied. The full
   * names of the referenced data stores. Formats: `projects/{project}/locations
   * /{location}/collections/{collection}/dataStores/{data_store}`
   * `projects/{project}/locations/{location}/dataStores/{data_store}`
   *
   * @param string[] $dataStores
   */
  public function setDataStores($dataStores)
  {
    $this->dataStores = $dataStores;
  }
  /**
   * @return string[]
   */
  public function getDataStores()
  {
    return $this->dataStores;
  }
  /**
   * Optional. The filter expression to be applied. Expression syntax is
   * documented at https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata#filter-expression-syntax
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
class_alias(GoogleCloudDialogflowCxV3FilterSpecs::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3FilterSpecs');
