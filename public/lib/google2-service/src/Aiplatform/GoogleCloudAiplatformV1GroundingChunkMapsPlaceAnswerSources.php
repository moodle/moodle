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

class GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources extends \Google\Collection
{
  protected $collection_key = 'reviewSnippets';
  protected $reviewSnippetsType = GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet::class;
  protected $reviewSnippetsDataType = 'array';

  /**
   * Snippets of reviews that were used to generate the answer.
   *
   * @param GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet[] $reviewSnippets
   */
  public function setReviewSnippets($reviewSnippets)
  {
    $this->reviewSnippets = $reviewSnippets;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet[]
   */
  public function getReviewSnippets()
  {
    return $this->reviewSnippets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources');
