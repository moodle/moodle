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

class GoogleCloudAiplatformV1RagContextsContext extends \Google\Model
{
  protected $chunkType = GoogleCloudAiplatformV1RagChunk::class;
  protected $chunkDataType = '';
  /**
   * According to the underlying Vector DB and the selected metric type, the
   * score can be either the distance or the similarity between the query and
   * the context and its range depends on the metric type. For example, if the
   * metric type is COSINE_DISTANCE, it represents the distance between the
   * query and the context. The larger the distance, the less relevant the
   * context is to the query. The range is [0, 2], while 0 means the most
   * relevant and 2 means the least relevant.
   *
   * @var 
   */
  public $score;
  /**
   * The file display name.
   *
   * @var string
   */
  public $sourceDisplayName;
  /**
   * If the file is imported from Cloud Storage or Google Drive, source_uri will
   * be original file URI in Cloud Storage or Google Drive; if file is uploaded,
   * source_uri will be file display name.
   *
   * @var string
   */
  public $sourceUri;
  /**
   * The text chunk.
   *
   * @var string
   */
  public $text;

  /**
   * Context of the retrieved chunk.
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
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The file display name.
   *
   * @param string $sourceDisplayName
   */
  public function setSourceDisplayName($sourceDisplayName)
  {
    $this->sourceDisplayName = $sourceDisplayName;
  }
  /**
   * @return string
   */
  public function getSourceDisplayName()
  {
    return $this->sourceDisplayName;
  }
  /**
   * If the file is imported from Cloud Storage or Google Drive, source_uri will
   * be original file URI in Cloud Storage or Google Drive; if file is uploaded,
   * source_uri will be file display name.
   *
   * @param string $sourceUri
   */
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return string
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
  /**
   * The text chunk.
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
class_alias(GoogleCloudAiplatformV1RagContextsContext::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagContextsContext');
