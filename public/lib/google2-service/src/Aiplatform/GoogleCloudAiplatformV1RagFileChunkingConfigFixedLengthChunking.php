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

class GoogleCloudAiplatformV1RagFileChunkingConfigFixedLengthChunking extends \Google\Model
{
  /**
   * The overlap between chunks.
   *
   * @var int
   */
  public $chunkOverlap;
  /**
   * The size of the chunks.
   *
   * @var int
   */
  public $chunkSize;

  /**
   * The overlap between chunks.
   *
   * @param int $chunkOverlap
   */
  public function setChunkOverlap($chunkOverlap)
  {
    $this->chunkOverlap = $chunkOverlap;
  }
  /**
   * @return int
   */
  public function getChunkOverlap()
  {
    return $this->chunkOverlap;
  }
  /**
   * The size of the chunks.
   *
   * @param int $chunkSize
   */
  public function setChunkSize($chunkSize)
  {
    $this->chunkSize = $chunkSize;
  }
  /**
   * @return int
   */
  public function getChunkSize()
  {
    return $this->chunkSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagFileChunkingConfigFixedLengthChunking::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagFileChunkingConfigFixedLengthChunking');
