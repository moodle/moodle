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

class GoogleCloudAiplatformV1RagChunk extends \Google\Model
{
  protected $pageSpanType = GoogleCloudAiplatformV1RagChunkPageSpan::class;
  protected $pageSpanDataType = '';
  /**
   * The content of the chunk.
   *
   * @var string
   */
  public $text;

  /**
   * If populated, represents where the chunk starts and ends in the document.
   *
   * @param GoogleCloudAiplatformV1RagChunkPageSpan $pageSpan
   */
  public function setPageSpan(GoogleCloudAiplatformV1RagChunkPageSpan $pageSpan)
  {
    $this->pageSpan = $pageSpan;
  }
  /**
   * @return GoogleCloudAiplatformV1RagChunkPageSpan
   */
  public function getPageSpan()
  {
    return $this->pageSpan;
  }
  /**
   * The content of the chunk.
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
class_alias(GoogleCloudAiplatformV1RagChunk::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagChunk');
