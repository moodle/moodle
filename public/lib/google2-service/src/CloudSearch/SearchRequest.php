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

namespace Google\Service\CloudSearch;

class SearchRequest extends \Google\Collection
{
  protected $collection_key = 'facetOptions';
  protected $contextAttributesType = ContextAttribute::class;
  protected $contextAttributesDataType = 'array';
  protected $dataSourceRestrictionsType = DataSourceRestriction::class;
  protected $dataSourceRestrictionsDataType = 'array';
  protected $facetOptionsType = FacetOptions::class;
  protected $facetOptionsDataType = 'array';
  /**
   * Maximum number of search results to return in one page. Valid values are
   * between 1 and 100, inclusive. Default value is 10. Minimum value is 50 when
   * results beyond 2000 are requested.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The raw query string. See supported search operators in the [Narrow your
   * search with
   * operators](https://support.google.com/cloudsearch/answer/6172299)
   *
   * @var string
   */
  public $query;
  protected $queryInterpretationOptionsType = QueryInterpretationOptions::class;
  protected $queryInterpretationOptionsDataType = '';
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  protected $sortOptionsType = SortOptions::class;
  protected $sortOptionsDataType = '';
  /**
   * Starting index of the results.
   *
   * @var int
   */
  public $start;

  /**
   * Context attributes for the request which will be used to adjust ranking of
   * search results. The maximum number of elements is 10.
   *
   * @param ContextAttribute[] $contextAttributes
   */
  public function setContextAttributes($contextAttributes)
  {
    $this->contextAttributes = $contextAttributes;
  }
  /**
   * @return ContextAttribute[]
   */
  public function getContextAttributes()
  {
    return $this->contextAttributes;
  }
  /**
   * The sources to use for querying. If not specified, all data sources from
   * the current search application are used.
   *
   * @param DataSourceRestriction[] $dataSourceRestrictions
   */
  public function setDataSourceRestrictions($dataSourceRestrictions)
  {
    $this->dataSourceRestrictions = $dataSourceRestrictions;
  }
  /**
   * @return DataSourceRestriction[]
   */
  public function getDataSourceRestrictions()
  {
    return $this->dataSourceRestrictions;
  }
  /**
   * @param FacetOptions[] $facetOptions
   */
  public function setFacetOptions($facetOptions)
  {
    $this->facetOptions = $facetOptions;
  }
  /**
   * @return FacetOptions[]
   */
  public function getFacetOptions()
  {
    return $this->facetOptions;
  }
  /**
   * Maximum number of search results to return in one page. Valid values are
   * between 1 and 100, inclusive. Default value is 10. Minimum value is 50 when
   * results beyond 2000 are requested.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The raw query string. See supported search operators in the [Narrow your
   * search with
   * operators](https://support.google.com/cloudsearch/answer/6172299)
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Options to interpret the user query.
   *
   * @param QueryInterpretationOptions $queryInterpretationOptions
   */
  public function setQueryInterpretationOptions(QueryInterpretationOptions $queryInterpretationOptions)
  {
    $this->queryInterpretationOptions = $queryInterpretationOptions;
  }
  /**
   * @return QueryInterpretationOptions
   */
  public function getQueryInterpretationOptions()
  {
    return $this->queryInterpretationOptions;
  }
  /**
   * Request options, such as the search application and user timezone.
   *
   * @param RequestOptions $requestOptions
   */
  public function setRequestOptions(RequestOptions $requestOptions)
  {
    $this->requestOptions = $requestOptions;
  }
  /**
   * @return RequestOptions
   */
  public function getRequestOptions()
  {
    return $this->requestOptions;
  }
  /**
   * The options for sorting the search results
   *
   * @param SortOptions $sortOptions
   */
  public function setSortOptions(SortOptions $sortOptions)
  {
    $this->sortOptions = $sortOptions;
  }
  /**
   * @return SortOptions
   */
  public function getSortOptions()
  {
    return $this->sortOptions;
  }
  /**
   * Starting index of the results.
   *
   * @param int $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return int
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchRequest::class, 'Google_Service_CloudSearch_SearchRequest');
