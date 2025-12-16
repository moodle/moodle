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

class GoogleCloudAiplatformV1Fact extends \Google\Model
{
  protected $chunkType = GoogleCloudAiplatformV1RagChunk::class;
  protected $chunkDataType = '';
  /**
   * Query that is used to retrieve this fact.
   *
   * @var string
   */
  public $query;
  /**
   * If present, according to the underlying Vector DB and the selected metric
   * type, the score can be either the distance or the similarity between the
   * query and the fact and its range depends on the metric type. For example,
   * if the metric type is COSINE_DISTANCE, it represents the distance between
   * the query and the fact. The larger the distance, the less relevant the fact
   * is to the query. The range is [0, 2], while 0 means the most relevant and 2
   * means the least relevant.
   *
   * @var 
   */
  public $score;
  /**
   * If present, the summary/snippet of the fact.
   *
   * @var string
   */
  public $summary;
  /**
   * If present, it refers to the title of this fact.
   *
   * @var string
   */
  public $title;
  /**
   * If present, this uri links to the source of the fact.
   *
   * @var string
   */
  public $uri;
  /**
   * If present, the distance between the query vector and this fact vector.
   *
   * @deprecated
   * @var 
   */
  public $vectorDistance;

  /**
   * If present, chunk properties.
   *
   * @param GoogleCloudAiplatformV1RagChunk $chunk
   */
  public function setChunk(GoogleCloudAiplatformV1RagChunk $chunk)
  {
    $this->chunk = $chunk;
  }
  /**
   * @return GoogleCloudAiplatformV1RagChunk
   */
  public function getChunk()
  {
    return $this->chunk;
  }
  /**
   * Query that is used to retrieve this fact.
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
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * If present, the summary/snippet of the fact.
   *
   * @param string $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * If present, it refers to the title of this fact.
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
  /**
   * If present, this uri links to the source of the fact.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  public function setVectorDistance($vectorDistance)
  {
    $this->vectorDistance = $vectorDistance;
  }
  public function getVectorDistance()
  {
    return $this->vectorDistance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Fact::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Fact');
