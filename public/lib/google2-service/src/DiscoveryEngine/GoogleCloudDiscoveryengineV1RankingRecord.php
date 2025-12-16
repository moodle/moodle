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

class GoogleCloudDiscoveryengineV1RankingRecord extends \Google\Model
{
  /**
   * The content of the record. Empty by default. At least one of title or
   * content should be set otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @var string
   */
  public $content;
  /**
   * The unique ID to represent the record.
   *
   * @var string
   */
  public $id;
  /**
   * The score of this record based on the given query and selected model. The
   * score will be rounded to 2 decimal places. If the score is close to 0, it
   * will be rounded to 0.0001 to avoid returning unset.
   *
   * @var float
   */
  public $score;
  /**
   * The title of the record. Empty by default. At least one of title or content
   * should be set otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @var string
   */
  public $title;

  /**
   * The content of the record. Empty by default. At least one of title or
   * content should be set otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The unique ID to represent the record.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The score of this record based on the given query and selected model. The
   * score will be rounded to 2 decimal places. If the score is close to 0, it
   * will be rounded to 0.0001 to avoid returning unset.
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
   * The title of the record. Empty by default. At least one of title or content
   * should be set otherwise an INVALID_ARGUMENT error is thrown.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1RankingRecord::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1RankingRecord');
