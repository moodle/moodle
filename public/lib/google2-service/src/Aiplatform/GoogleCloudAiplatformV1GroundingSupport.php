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

class GoogleCloudAiplatformV1GroundingSupport extends \Google\Collection
{
  protected $collection_key = 'groundingChunkIndices';
  /**
   * The confidence scores for the support references. This list is parallel to
   * the `grounding_chunk_indices` list. A score is a value between 0.0 and 1.0,
   * with a higher score indicating a higher confidence that the reference
   * supports the claim. For Gemini 2.0 and before, this list has the same size
   * as `grounding_chunk_indices`. For Gemini 2.5 and later, this list is empty
   * and should be ignored.
   *
   * @var float[]
   */
  public $confidenceScores;
  /**
   * A list of indices into the `grounding_chunks` field of the
   * `GroundingMetadata` message. These indices specify which grounding chunks
   * support the claim made in the content segment. For example, if this field
   * has the values `[1, 3]`, it means that `grounding_chunks[1]` and
   * `grounding_chunks[3]` are the sources for the claim in the content segment.
   *
   * @var int[]
   */
  public $groundingChunkIndices;
  protected $segmentType = GoogleCloudAiplatformV1Segment::class;
  protected $segmentDataType = '';

  /**
   * The confidence scores for the support references. This list is parallel to
   * the `grounding_chunk_indices` list. A score is a value between 0.0 and 1.0,
   * with a higher score indicating a higher confidence that the reference
   * supports the claim. For Gemini 2.0 and before, this list has the same size
   * as `grounding_chunk_indices`. For Gemini 2.5 and later, this list is empty
   * and should be ignored.
   *
   * @param float[] $confidenceScores
   */
  public function setConfidenceScores($confidenceScores)
  {
    $this->confidenceScores = $confidenceScores;
  }
  /**
   * @return float[]
   */
  public function getConfidenceScores()
  {
    return $this->confidenceScores;
  }
  /**
   * A list of indices into the `grounding_chunks` field of the
   * `GroundingMetadata` message. These indices specify which grounding chunks
   * support the claim made in the content segment. For example, if this field
   * has the values `[1, 3]`, it means that `grounding_chunks[1]` and
   * `grounding_chunks[3]` are the sources for the claim in the content segment.
   *
   * @param int[] $groundingChunkIndices
   */
  public function setGroundingChunkIndices($groundingChunkIndices)
  {
    $this->groundingChunkIndices = $groundingChunkIndices;
  }
  /**
   * @return int[]
   */
  public function getGroundingChunkIndices()
  {
    return $this->groundingChunkIndices;
  }
  /**
   * The content segment that this support message applies to.
   *
   * @param GoogleCloudAiplatformV1Segment $segment
   */
  public function setSegment(GoogleCloudAiplatformV1Segment $segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return GoogleCloudAiplatformV1Segment
   */
  public function getSegment()
  {
    return $this->segment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingSupport::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingSupport');
