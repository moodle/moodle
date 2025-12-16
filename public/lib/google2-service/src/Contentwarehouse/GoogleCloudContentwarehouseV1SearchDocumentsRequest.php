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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1SearchDocumentsRequest extends \Google\Collection
{
  /**
   * Total number calculation will be skipped.
   */
  public const TOTAL_RESULT_SIZE_TOTAL_RESULT_SIZE_UNSPECIFIED = 'TOTAL_RESULT_SIZE_UNSPECIFIED';
  /**
   * Estimate total number. The total result size will be accurated up to
   * 10,000. This option will add cost and latency to your request.
   */
  public const TOTAL_RESULT_SIZE_ESTIMATED_SIZE = 'ESTIMATED_SIZE';
  /**
   * It may adversely impact performance. The limit is 1000,000.
   */
  public const TOTAL_RESULT_SIZE_ACTUAL_SIZE = 'ACTUAL_SIZE';
  protected $collection_key = 'histogramQueries';
  protected $documentQueryType = GoogleCloudContentwarehouseV1DocumentQuery::class;
  protected $documentQueryDataType = '';
  protected $histogramQueriesType = GoogleCloudContentwarehouseV1HistogramQuery::class;
  protected $histogramQueriesDataType = 'array';
  /**
   * An integer that specifies the current offset (that is, starting result
   * location, amongst the documents deemed by the API as relevant) in search
   * results. This field is only considered if page_token is unset. The maximum
   * allowed value is 5000. Otherwise an error is thrown. For example, 0 means
   * to return results starting from the first matching document, and 10 means
   * to return from the 11th document. This can be used for pagination, (for
   * example, pageSize = 10 and offset = 10 means to return from the second
   * page).
   *
   * @var int
   */
  public $offset;
  /**
   * The criteria determining how search results are sorted. For non-empty
   * query, default is `"relevance desc"`. For empty query, default is
   * `"upload_date desc"`. Supported options are: * `"relevance desc"`: By
   * relevance descending, as determined by the API algorithms. * `"upload_date
   * desc"`: By upload date descending. * `"upload_date"`: By upload date
   * ascending. * `"update_date desc"`: By last updated date descending. *
   * `"update_date"`: By last updated date ascending. * `"retrieval_importance
   * desc"`: By retrieval importance of properties descending. This feature is
   * still under development, please do not use unless otherwise instructed to
   * do so.
   *
   * @var string
   */
  public $orderBy;
  /**
   * A limit on the number of documents returned in the search results.
   * Increasing this value above the default value of 10 can increase search
   * response time. The value can be between 1 and 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The token specifying the current offset within search results. See
   * SearchDocumentsResponse.next_page_token for an explanation of how to obtain
   * the next set of query results.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Experimental, do not use. The limit on the number of documents returned for
   * the question-answering feature. To enable the question-answering feature,
   * set [DocumentQuery].is_nl_query to true.
   *
   * @var int
   */
  public $qaSizeLimit;
  protected $requestMetadataType = GoogleCloudContentwarehouseV1RequestMetadata::class;
  protected $requestMetadataDataType = '';
  /**
   * Controls if the search document request requires the return of a total size
   * of matched documents. See SearchDocumentsResponse.total_size. Enabling this
   * flag may adversely impact performance. Hint: If this is used with
   * pagination, set this flag on the initial query but set this to false on
   * subsequent page calls (keep the total count locally). Defaults to false.
   *
   * @var bool
   */
  public $requireTotalSize;
  /**
   * Controls if the search document request requires the return of a total size
   * of matched documents. See SearchDocumentsResponse.total_size.
   *
   * @var string
   */
  public $totalResultSize;

  /**
   * Query used to search against documents (keyword, filters, etc.).
   *
   * @param GoogleCloudContentwarehouseV1DocumentQuery $documentQuery
   */
  public function setDocumentQuery(GoogleCloudContentwarehouseV1DocumentQuery $documentQuery)
  {
    $this->documentQuery = $documentQuery;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DocumentQuery
   */
  public function getDocumentQuery()
  {
    return $this->documentQuery;
  }
  /**
   * An expression specifying a histogram request against matching documents.
   * Expression syntax is an aggregation function call with histogram facets and
   * other options. The following aggregation functions are supported: *
   * `count(string_histogram_facet)`: Count the number of matching entities for
   * each distinct attribute value. Data types: * Histogram facet (aka
   * filterable properties): Facet names with format .. Facets will have the
   * format of: `a-zA-Z`. If the facet is a child facet, then the parent
   * hierarchy needs to be specified separated by dots in the prefix after the
   * schema id. Thus, the format for a multi- level facet is: .. . Example:
   * schema123.root_parent_facet.middle_facet.child_facet * DocumentSchemaId:
   * (with no schema id prefix) to get histograms for each document type
   * (returns the schema id path, e.g. projects/12345/locations/us-
   * west/documentSchemas/abc123). Example expression: * Document type counts:
   * count('DocumentSchemaId') * For schema id, abc123, get the counts for
   * MORTGAGE_TYPE: count('abc123.MORTGAGE_TYPE')
   *
   * @param GoogleCloudContentwarehouseV1HistogramQuery[] $histogramQueries
   */
  public function setHistogramQueries($histogramQueries)
  {
    $this->histogramQueries = $histogramQueries;
  }
  /**
   * @return GoogleCloudContentwarehouseV1HistogramQuery[]
   */
  public function getHistogramQueries()
  {
    return $this->histogramQueries;
  }
  /**
   * An integer that specifies the current offset (that is, starting result
   * location, amongst the documents deemed by the API as relevant) in search
   * results. This field is only considered if page_token is unset. The maximum
   * allowed value is 5000. Otherwise an error is thrown. For example, 0 means
   * to return results starting from the first matching document, and 10 means
   * to return from the 11th document. This can be used for pagination, (for
   * example, pageSize = 10 and offset = 10 means to return from the second
   * page).
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The criteria determining how search results are sorted. For non-empty
   * query, default is `"relevance desc"`. For empty query, default is
   * `"upload_date desc"`. Supported options are: * `"relevance desc"`: By
   * relevance descending, as determined by the API algorithms. * `"upload_date
   * desc"`: By upload date descending. * `"upload_date"`: By upload date
   * ascending. * `"update_date desc"`: By last updated date descending. *
   * `"update_date"`: By last updated date ascending. * `"retrieval_importance
   * desc"`: By retrieval importance of properties descending. This feature is
   * still under development, please do not use unless otherwise instructed to
   * do so.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * A limit on the number of documents returned in the search results.
   * Increasing this value above the default value of 10 can increase search
   * response time. The value can be between 1 and 100.
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
   * The token specifying the current offset within search results. See
   * SearchDocumentsResponse.next_page_token for an explanation of how to obtain
   * the next set of query results.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Experimental, do not use. The limit on the number of documents returned for
   * the question-answering feature. To enable the question-answering feature,
   * set [DocumentQuery].is_nl_query to true.
   *
   * @param int $qaSizeLimit
   */
  public function setQaSizeLimit($qaSizeLimit)
  {
    $this->qaSizeLimit = $qaSizeLimit;
  }
  /**
   * @return int
   */
  public function getQaSizeLimit()
  {
    return $this->qaSizeLimit;
  }
  /**
   * The meta information collected about the end user, used to enforce access
   * control and improve the search quality of the service.
   *
   * @param GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
  /**
   * Controls if the search document request requires the return of a total size
   * of matched documents. See SearchDocumentsResponse.total_size. Enabling this
   * flag may adversely impact performance. Hint: If this is used with
   * pagination, set this flag on the initial query but set this to false on
   * subsequent page calls (keep the total count locally). Defaults to false.
   *
   * @param bool $requireTotalSize
   */
  public function setRequireTotalSize($requireTotalSize)
  {
    $this->requireTotalSize = $requireTotalSize;
  }
  /**
   * @return bool
   */
  public function getRequireTotalSize()
  {
    return $this->requireTotalSize;
  }
  /**
   * Controls if the search document request requires the return of a total size
   * of matched documents. See SearchDocumentsResponse.total_size.
   *
   * Accepted values: TOTAL_RESULT_SIZE_UNSPECIFIED, ESTIMATED_SIZE, ACTUAL_SIZE
   *
   * @param self::TOTAL_RESULT_SIZE_* $totalResultSize
   */
  public function setTotalResultSize($totalResultSize)
  {
    $this->totalResultSize = $totalResultSize;
  }
  /**
   * @return self::TOTAL_RESULT_SIZE_*
   */
  public function getTotalResultSize()
  {
    return $this->totalResultSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1SearchDocumentsRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1SearchDocumentsRequest');
