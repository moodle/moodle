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

class GoogleCloudAiplatformV1EmbedContentResponse extends \Google\Model
{
  protected $embeddingType = GoogleCloudAiplatformV1EmbedContentResponseEmbedding::class;
  protected $embeddingDataType = '';
  /**
   * Whether the input content was truncated before generating the embedding.
   *
   * @var bool
   */
  public $truncated;
  protected $usageMetadataType = GoogleCloudAiplatformV1UsageMetadata::class;
  protected $usageMetadataDataType = '';

  /**
   * The embedding generated from the input content.
   *
   * @param GoogleCloudAiplatformV1EmbedContentResponseEmbedding $embedding
   */
  public function setEmbedding(GoogleCloudAiplatformV1EmbedContentResponseEmbedding $embedding)
  {
    $this->embedding = $embedding;
  }
  /**
   * @return GoogleCloudAiplatformV1EmbedContentResponseEmbedding
   */
  public function getEmbedding()
  {
    return $this->embedding;
  }
  /**
   * Whether the input content was truncated before generating the embedding.
   *
   * @param bool $truncated
   */
  public function setTruncated($truncated)
  {
    $this->truncated = $truncated;
  }
  /**
   * @return bool
   */
  public function getTruncated()
  {
    return $this->truncated;
  }
  /**
   * Metadata about the response(s).
   *
   * @param GoogleCloudAiplatformV1UsageMetadata $usageMetadata
   */
  public function setUsageMetadata(GoogleCloudAiplatformV1UsageMetadata $usageMetadata)
  {
    $this->usageMetadata = $usageMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1UsageMetadata
   */
  public function getUsageMetadata()
  {
    return $this->usageMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EmbedContentResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EmbedContentResponse');
