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

namespace Google\Service\CloudTalentSolution;

class SearchJobsResponse extends \Google\Collection
{
  protected $collection_key = 'matchingJobs';
  /**
   * If query broadening is enabled, we may append additional results from the
   * broadened query. This number indicates how many of the jobs returned in the
   * jobs field are from the broadened query. These results are always at the
   * end of the jobs list. In particular, a value of 0, or if the field isn't
   * set, all the jobs in the jobs list are from the original (without
   * broadening) query. If this field is non-zero, subsequent requests with
   * offset after this result set should contain all broadened results.
   *
   * @var int
   */
  public $broadenedQueryJobsCount;
  protected $histogramQueryResultsType = HistogramQueryResult::class;
  protected $histogramQueryResultsDataType = 'array';
  protected $locationFiltersType = Location::class;
  protected $locationFiltersDataType = 'array';
  protected $matchingJobsType = MatchingJob::class;
  protected $matchingJobsDataType = 'array';
  protected $metadataType = ResponseMetadata::class;
  protected $metadataDataType = '';
  /**
   * The token that specifies the starting position of the next page of results.
   * This field is empty if there are no more results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $spellCorrectionType = SpellingCorrection::class;
  protected $spellCorrectionDataType = '';
  /**
   * Number of jobs that match the specified query. Note: This size is precise
   * only if the total is less than 100,000.
   *
   * @var int
   */
  public $totalSize;

  /**
   * If query broadening is enabled, we may append additional results from the
   * broadened query. This number indicates how many of the jobs returned in the
   * jobs field are from the broadened query. These results are always at the
   * end of the jobs list. In particular, a value of 0, or if the field isn't
   * set, all the jobs in the jobs list are from the original (without
   * broadening) query. If this field is non-zero, subsequent requests with
   * offset after this result set should contain all broadened results.
   *
   * @param int $broadenedQueryJobsCount
   */
  public function setBroadenedQueryJobsCount($broadenedQueryJobsCount)
  {
    $this->broadenedQueryJobsCount = $broadenedQueryJobsCount;
  }
  /**
   * @return int
   */
  public function getBroadenedQueryJobsCount()
  {
    return $this->broadenedQueryJobsCount;
  }
  /**
   * The histogram results that match with specified
   * SearchJobsRequest.histogram_queries.
   *
   * @param HistogramQueryResult[] $histogramQueryResults
   */
  public function setHistogramQueryResults($histogramQueryResults)
  {
    $this->histogramQueryResults = $histogramQueryResults;
  }
  /**
   * @return HistogramQueryResult[]
   */
  public function getHistogramQueryResults()
  {
    return $this->histogramQueryResults;
  }
  /**
   * The location filters that the service applied to the specified query. If
   * any filters are lat-lng based, the Location.location_type is
   * Location.LocationType.LOCATION_TYPE_UNSPECIFIED.
   *
   * @param Location[] $locationFilters
   */
  public function setLocationFilters($locationFilters)
  {
    $this->locationFilters = $locationFilters;
  }
  /**
   * @return Location[]
   */
  public function getLocationFilters()
  {
    return $this->locationFilters;
  }
  /**
   * The Job entities that match the specified SearchJobsRequest.
   *
   * @param MatchingJob[] $matchingJobs
   */
  public function setMatchingJobs($matchingJobs)
  {
    $this->matchingJobs = $matchingJobs;
  }
  /**
   * @return MatchingJob[]
   */
  public function getMatchingJobs()
  {
    return $this->matchingJobs;
  }
  /**
   * Additional information for the API invocation, such as the request tracking
   * id.
   *
   * @param ResponseMetadata $metadata
   */
  public function setMetadata(ResponseMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ResponseMetadata
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
   * The spell checking result, and correction.
   *
   * @param SpellingCorrection $spellCorrection
   */
  public function setSpellCorrection(SpellingCorrection $spellCorrection)
  {
    $this->spellCorrection = $spellCorrection;
  }
  /**
   * @return SpellingCorrection
   */
  public function getSpellCorrection()
  {
    return $this->spellCorrection;
  }
  /**
   * Number of jobs that match the specified query. Note: This size is precise
   * only if the total is less than 100,000.
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
class_alias(SearchJobsResponse::class, 'Google_Service_CloudTalentSolution_SearchJobsResponse');
