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

class GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecExtractiveContentSpec extends \Google\Model
{
  /**
   * The maximum number of extractive answers returned in each search result. An
   * extractive answer is a verbatim answer extracted from the original
   * document, which provides a precise and contextually relevant answer to the
   * search query. If the number of matching answers is less than the
   * `max_extractive_answer_count`, return all of the answers. Otherwise, return
   * the `max_extractive_answer_count`. At most five answers are returned for
   * each SearchResult.
   *
   * @var int
   */
  public $maxExtractiveAnswerCount;
  /**
   * The max number of extractive segments returned in each search result. Only
   * applied if the DataStore is set to DataStore.ContentConfig.CONTENT_REQUIRED
   * or DataStore.solution_types is SOLUTION_TYPE_CHAT. An extractive segment is
   * a text segment extracted from the original document that is relevant to the
   * search query, and, in general, more verbose than an extractive answer. The
   * segment could then be used as input for LLMs to generate summaries and
   * answers. If the number of matching segments is less than
   * `max_extractive_segment_count`, return all of the segments. Otherwise,
   * return the `max_extractive_segment_count`.
   *
   * @var int
   */
  public $maxExtractiveSegmentCount;
  /**
   * Return at most `num_next_segments` segments after each selected segments.
   *
   * @var int
   */
  public $numNextSegments;
  /**
   * Specifies whether to also include the adjacent from each selected segments.
   * Return at most `num_previous_segments` segments before each selected
   * segments.
   *
   * @var int
   */
  public $numPreviousSegments;
  /**
   * Specifies whether to return the confidence score from the extractive
   * segments in each search result. This feature is available only for new or
   * allowlisted data stores. To allowlist your data store, contact your
   * Customer Engineer. The default value is `false`.
   *
   * @var bool
   */
  public $returnExtractiveSegmentScore;

  /**
   * The maximum number of extractive answers returned in each search result. An
   * extractive answer is a verbatim answer extracted from the original
   * document, which provides a precise and contextually relevant answer to the
   * search query. If the number of matching answers is less than the
   * `max_extractive_answer_count`, return all of the answers. Otherwise, return
   * the `max_extractive_answer_count`. At most five answers are returned for
   * each SearchResult.
   *
   * @param int $maxExtractiveAnswerCount
   */
  public function setMaxExtractiveAnswerCount($maxExtractiveAnswerCount)
  {
    $this->maxExtractiveAnswerCount = $maxExtractiveAnswerCount;
  }
  /**
   * @return int
   */
  public function getMaxExtractiveAnswerCount()
  {
    return $this->maxExtractiveAnswerCount;
  }
  /**
   * The max number of extractive segments returned in each search result. Only
   * applied if the DataStore is set to DataStore.ContentConfig.CONTENT_REQUIRED
   * or DataStore.solution_types is SOLUTION_TYPE_CHAT. An extractive segment is
   * a text segment extracted from the original document that is relevant to the
   * search query, and, in general, more verbose than an extractive answer. The
   * segment could then be used as input for LLMs to generate summaries and
   * answers. If the number of matching segments is less than
   * `max_extractive_segment_count`, return all of the segments. Otherwise,
   * return the `max_extractive_segment_count`.
   *
   * @param int $maxExtractiveSegmentCount
   */
  public function setMaxExtractiveSegmentCount($maxExtractiveSegmentCount)
  {
    $this->maxExtractiveSegmentCount = $maxExtractiveSegmentCount;
  }
  /**
   * @return int
   */
  public function getMaxExtractiveSegmentCount()
  {
    return $this->maxExtractiveSegmentCount;
  }
  /**
   * Return at most `num_next_segments` segments after each selected segments.
   *
   * @param int $numNextSegments
   */
  public function setNumNextSegments($numNextSegments)
  {
    $this->numNextSegments = $numNextSegments;
  }
  /**
   * @return int
   */
  public function getNumNextSegments()
  {
    return $this->numNextSegments;
  }
  /**
   * Specifies whether to also include the adjacent from each selected segments.
   * Return at most `num_previous_segments` segments before each selected
   * segments.
   *
   * @param int $numPreviousSegments
   */
  public function setNumPreviousSegments($numPreviousSegments)
  {
    $this->numPreviousSegments = $numPreviousSegments;
  }
  /**
   * @return int
   */
  public function getNumPreviousSegments()
  {
    return $this->numPreviousSegments;
  }
  /**
   * Specifies whether to return the confidence score from the extractive
   * segments in each search result. This feature is available only for new or
   * allowlisted data stores. To allowlist your data store, contact your
   * Customer Engineer. The default value is `false`.
   *
   * @param bool $returnExtractiveSegmentScore
   */
  public function setReturnExtractiveSegmentScore($returnExtractiveSegmentScore)
  {
    $this->returnExtractiveSegmentScore = $returnExtractiveSegmentScore;
  }
  /**
   * @return bool
   */
  public function getReturnExtractiveSegmentScore()
  {
    return $this->returnExtractiveSegmentScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecExtractiveContentSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecExtractiveContentSpec');
