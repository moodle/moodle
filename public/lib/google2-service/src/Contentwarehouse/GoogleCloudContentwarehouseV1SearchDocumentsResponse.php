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

class GoogleCloudContentwarehouseV1SearchDocumentsResponse extends \Google\Collection
{
  protected $collection_key = 'matchingDocuments';
  protected $histogramQueryResultsType = GoogleCloudContentwarehouseV1HistogramQueryResult::class;
  protected $histogramQueryResultsDataType = 'array';
  protected $matchingDocumentsType = GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument::class;
  protected $matchingDocumentsDataType = 'array';
  protected $metadataType = GoogleCloudContentwarehouseV1ResponseMetadata::class;
  protected $metadataDataType = '';
  /**
   * The token that specifies the starting position of the next page of results.
   * This field is empty if there are no more results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Experimental. Question answer from the query against the document.
   *
   * @var string
   */
  public $questionAnswer;
  /**
   * The total number of matched documents which is available only if the client
   * set SearchDocumentsRequest.require_total_size to `true` or set
   * SearchDocumentsRequest.total_result_size to `ESTIMATED_SIZE` or
   * `ACTUAL_SIZE`. Otherwise, the value will be `-1`. Typically a UI would
   * handle this condition by displaying "of many", for example: "Displaying 10
   * of many".
   *
   * @var int
   */
  public $totalSize;

  /**
   * The histogram results that match with the specified
   * SearchDocumentsRequest.histogram_queries.
   *
   * @param GoogleCloudContentwarehouseV1HistogramQueryResult[] $histogramQueryResults
   */
  public function setHistogramQueryResults($histogramQueryResults)
  {
    $this->histogramQueryResults = $histogramQueryResults;
  }
  /**
   * @return GoogleCloudContentwarehouseV1HistogramQueryResult[]
   */
  public function getHistogramQueryResults()
  {
    return $this->histogramQueryResults;
  }
  /**
   * The document entities that match the specified SearchDocumentsRequest.
   *
   * @param GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument[] $matchingDocuments
   */
  public function setMatchingDocuments($matchingDocuments)
  {
    $this->matchingDocuments = $matchingDocuments;
  }
  /**
   * @return GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument[]
   */
  public function getMatchingDocuments()
  {
    return $this->matchingDocuments;
  }
  /**
   * Additional information for the API invocation, such as the request tracking
   * id.
   *
   * @param GoogleCloudContentwarehouseV1ResponseMetadata $metadata
   */
  public function setMetadata(GoogleCloudContentwarehouseV1ResponseMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ResponseMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The token that specifies the starting position of the next page of results.
   * This field is empty if there are no more results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Experimental. Question answer from the query against the document.
   *
   * @param string $questionAnswer
   */
  public function setQuestionAnswer($questionAnswer)
  {
    $this->questionAnswer = $questionAnswer;
  }
  /**
   * @return string
   */
  public function getQuestionAnswer()
  {
    return $this->questionAnswer;
  }
  /**
   * The total number of matched documents which is available only if the client
   * set SearchDocumentsRequest.require_total_size to `true` or set
   * SearchDocumentsRequest.total_result_size to `ESTIMATED_SIZE` or
   * `ACTUAL_SIZE`. Otherwise, the value will be `-1`. Typically a UI would
   * handle this condition by displaying "of many", for example: "Displaying 10
   * of many".
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1SearchDocumentsResponse::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1SearchDocumentsResponse');
