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

namespace Google\Service\CustomSearchAPI;

class Search extends \Google\Collection
{
  protected $collection_key = 'promotions';
  /**
   * Metadata and refinements associated with the given search engine,
   * including: * The name of the search engine that was used for the query. * A
   * set of [facet objects](https://developers.google.com/custom-
   * search/docs/refinements#create) (refinements) you can use for refining a
   * search.
   *
   * @var array[]
   */
  public $context;
  protected $itemsType = Result::class;
  protected $itemsDataType = 'array';
  /**
   * Unique identifier for the type of current object. For this API, it is
   * customsearch#search.
   *
   * @var string
   */
  public $kind;
  protected $promotionsType = Promotion::class;
  protected $promotionsDataType = 'array';
  protected $queriesType = SearchQueries::class;
  protected $queriesDataType = '';
  protected $searchInformationType = SearchSearchInformation::class;
  protected $searchInformationDataType = '';
  protected $spellingType = SearchSpelling::class;
  protected $spellingDataType = '';
  protected $urlType = SearchUrl::class;
  protected $urlDataType = '';

  /**
   * Metadata and refinements associated with the given search engine,
   * including: * The name of the search engine that was used for the query. * A
   * set of [facet objects](https://developers.google.com/custom-
   * search/docs/refinements#create) (refinements) you can use for refining a
   * search.
   *
   * @param array[] $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return array[]
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * The current set of custom search results.
   *
   * @param Result[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Result[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Unique identifier for the type of current object. For this API, it is
   * customsearch#search.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The set of [promotions](https://developers.google.com/custom-
   * search/docs/promotions). Present only if the custom search engine's
   * configuration files define any promotions for the given query.
   *
   * @param Promotion[] $promotions
   */
  public function setPromotions($promotions)
  {
    $this->promotions = $promotions;
  }
  /**
   * @return Promotion[]
   */
  public function getPromotions()
  {
    return $this->promotions;
  }
  /**
   * Query metadata for the previous, current, and next pages of results.
   *
   * @param SearchQueries $queries
   */
  public function setQueries(SearchQueries $queries)
  {
    $this->queries = $queries;
  }
  /**
   * @return SearchQueries
   */
  public function getQueries()
  {
    return $this->queries;
  }
  /**
   * Metadata about a search operation.
   *
   * @param SearchSearchInformation $searchInformation
   */
  public function setSearchInformation(SearchSearchInformation $searchInformation)
  {
    $this->searchInformation = $searchInformation;
  }
  /**
   * @return SearchSearchInformation
   */
  public function getSearchInformation()
  {
    return $this->searchInformation;
  }
  /**
   * Spell correction information for a query.
   *
   * @param SearchSpelling $spelling
   */
  public function setSpelling(SearchSpelling $spelling)
  {
    $this->spelling = $spelling;
  }
  /**
   * @return SearchSpelling
   */
  public function getSpelling()
  {
    return $this->spelling;
  }
  /**
   * OpenSearch template and URL.
   *
   * @param SearchUrl $url
   */
  public function setUrl(SearchUrl $url)
  {
    $this->url = $url;
  }
  /**
   * @return SearchUrl
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Search::class, 'Google_Service_CustomSearchAPI_Search');
