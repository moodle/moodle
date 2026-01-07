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

namespace Google\Service\SearchConsole;

class SearchAnalyticsQueryResponse extends \Google\Collection
{
  public const RESPONSE_AGGREGATION_TYPE_AUTO = 'AUTO';
  public const RESPONSE_AGGREGATION_TYPE_BY_PROPERTY = 'BY_PROPERTY';
  public const RESPONSE_AGGREGATION_TYPE_BY_PAGE = 'BY_PAGE';
  public const RESPONSE_AGGREGATION_TYPE_BY_NEWS_SHOWCASE_PANEL = 'BY_NEWS_SHOWCASE_PANEL';
  protected $collection_key = 'rows';
  protected $metadataType = Metadata::class;
  protected $metadataDataType = '';
  /**
   * How the results were aggregated.
   *
   * @var string
   */
  public $responseAggregationType;
  protected $rowsType = ApiDataRow::class;
  protected $rowsDataType = 'array';

  /**
   * An object that may be returned with your query results, providing context
   * about the state of the data. See details in Metadata object documentation.
   *
   * @param Metadata $metadata
   */
  public function setMetadata(Metadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return Metadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * How the results were aggregated.
   *
   * Accepted values: AUTO, BY_PROPERTY, BY_PAGE, BY_NEWS_SHOWCASE_PANEL
   *
   * @param self::RESPONSE_AGGREGATION_TYPE_* $responseAggregationType
   */
  public function setResponseAggregationType($responseAggregationType)
  {
    $this->responseAggregationType = $responseAggregationType;
  }
  /**
   * @return self::RESPONSE_AGGREGATION_TYPE_*
   */
  public function getResponseAggregationType()
  {
    return $this->responseAggregationType;
  }
  /**
   * A list of rows grouped by the key values in the order given in the query.
   *
   * @param ApiDataRow[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return ApiDataRow[]
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchAnalyticsQueryResponse::class, 'Google_Service_SearchConsole_SearchAnalyticsQueryResponse');
