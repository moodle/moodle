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

class GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment extends \Google\Collection
{
  protected $collection_key = 'referenceIndices';
  /**
   * End of the segment, exclusive.
   *
   * @var string
   */
  public $endIndex;
  /**
   * Score for the segment.
   *
   * @var float
   */
  public $groundingScore;
  /**
   * References for the segment.
   *
   * @var int[]
   */
  public $referenceIndices;
  /**
   * Zero-based index indicating the start of the segment, measured in bytes of
   * a UTF-8 string (i.e. characters encoded on multiple bytes have a length of
   * more than one).
   *
   * @var string
   */
  public $startIndex;
  /**
   * The text segment itself.
   *
   * @var string
   */
  public $text;

  /**
   * End of the segment, exclusive.
   *
   * @param string $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return string
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * Score for the segment.
   *
   * @param float $groundingScore
   */
  public function setGroundingScore($groundingScore)
  {
    $this->groundingScore = $groundingScore;
  }
  /**
   * @return float
   */
  public function getGroundingScore()
  {
    return $this->groundingScore;
  }
  /**
   * References for the segment.
   *
   * @param int[] $referenceIndices
   */
  public function setReferenceIndices($referenceIndices)
  {
    $this->referenceIndices = $referenceIndices;
  }
  /**
   * @return int[]
   */
  public function getReferenceIndices()
  {
    return $this->referenceIndices;
  }
  /**
   * Zero-based index indicating the start of the segment, measured in bytes of
   * a UTF-8 string (i.e. characters encoded on multiple bytes have a length of
   * more than one).
   *
   * @param string $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return string
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The text segment itself.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment');
