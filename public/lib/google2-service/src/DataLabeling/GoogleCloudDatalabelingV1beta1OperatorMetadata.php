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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1OperatorMetadata extends \Google\Collection
{
  protected $collection_key = 'comments';
  /**
   * Comments from contributors.
   *
   * @var string[]
   */
  public $comments;
  /**
   * The total number of contributors that choose this label.
   *
   * @var int
   */
  public $labelVotes;
  /**
   * Confidence score corresponding to a label. For examle, if 3 contributors
   * have answered the question and 2 of them agree on the final label, the
   * confidence score will be 0.67 (2/3).
   *
   * @var float
   */
  public $score;
  /**
   * The total number of contributors that answer this question.
   *
   * @var int
   */
  public $totalVotes;

  /**
   * Comments from contributors.
   *
   * @param string[] $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string[]
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * The total number of contributors that choose this label.
   *
   * @param int $labelVotes
   */
  public function setLabelVotes($labelVotes)
  {
    $this->labelVotes = $labelVotes;
  }
  /**
   * @return int
   */
  public function getLabelVotes()
  {
    return $this->labelVotes;
  }
  /**
   * Confidence score corresponding to a label. For examle, if 3 contributors
   * have answered the question and 2 of them agree on the final label, the
   * confidence score will be 0.67 (2/3).
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The total number of contributors that answer this question.
   *
   * @param int $totalVotes
   */
  public function setTotalVotes($totalVotes)
  {
    $this->totalVotes = $totalVotes;
  }
  /**
   * @return int
   */
  public function getTotalVotes()
  {
    return $this->totalVotes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1OperatorMetadata::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1OperatorMetadata');
