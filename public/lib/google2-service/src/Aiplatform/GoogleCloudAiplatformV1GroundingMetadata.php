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

class GoogleCloudAiplatformV1GroundingMetadata extends \Google\Collection
{
  protected $collection_key = 'webSearchQueries';
  /**
   * Optional. Output only. A token that can be used to render a Google Maps
   * widget with the contextual data. This field is populated only when the
   * grounding source is Google Maps.
   *
   * @var string
   */
  public $googleMapsWidgetContextToken;
  protected $groundingChunksType = GoogleCloudAiplatformV1GroundingChunk::class;
  protected $groundingChunksDataType = 'array';
  protected $groundingSupportsType = GoogleCloudAiplatformV1GroundingSupport::class;
  protected $groundingSupportsDataType = 'array';
  protected $retrievalMetadataType = GoogleCloudAiplatformV1RetrievalMetadata::class;
  protected $retrievalMetadataDataType = '';
  protected $searchEntryPointType = GoogleCloudAiplatformV1SearchEntryPoint::class;
  protected $searchEntryPointDataType = '';
  protected $sourceFlaggingUrisType = GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri::class;
  protected $sourceFlaggingUrisDataType = 'array';
  /**
   * Optional. The web search queries that were used to generate the content.
   * This field is populated only when the grounding source is Google Search.
   *
   * @var string[]
   */
  public $webSearchQueries;

  /**
   * Optional. Output only. A token that can be used to render a Google Maps
   * widget with the contextual data. This field is populated only when the
   * grounding source is Google Maps.
   *
   * @param string $googleMapsWidgetContextToken
   */
  public function setGoogleMapsWidgetContextToken($googleMapsWidgetContextToken)
  {
    $this->googleMapsWidgetContextToken = $googleMapsWidgetContextToken;
  }
  /**
   * @return string
   */
  public function getGoogleMapsWidgetContextToken()
  {
    return $this->googleMapsWidgetContextToken;
  }
  /**
   * A list of supporting references retrieved from the grounding source. This
   * field is populated when the grounding source is Google Search, Vertex AI
   * Search, or Google Maps.
   *
   * @param GoogleCloudAiplatformV1GroundingChunk[] $groundingChunks
   */
  public function setGroundingChunks($groundingChunks)
  {
    $this->groundingChunks = $groundingChunks;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunk[]
   */
  public function getGroundingChunks()
  {
    return $this->groundingChunks;
  }
  /**
   * Optional. A list of grounding supports that connect the generated content
   * to the grounding chunks. This field is populated when the grounding source
   * is Google Search or Vertex AI Search.
   *
   * @param GoogleCloudAiplatformV1GroundingSupport[] $groundingSupports
   */
  public function setGroundingSupports($groundingSupports)
  {
    $this->groundingSupports = $groundingSupports;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingSupport[]
   */
  public function getGroundingSupports()
  {
    return $this->groundingSupports;
  }
  /**
   * Optional. Output only. Metadata related to the retrieval grounding source.
   *
   * @param GoogleCloudAiplatformV1RetrievalMetadata $retrievalMetadata
   */
  public function setRetrievalMetadata(GoogleCloudAiplatformV1RetrievalMetadata $retrievalMetadata)
  {
    $this->retrievalMetadata = $retrievalMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrievalMetadata
   */
  public function getRetrievalMetadata()
  {
    return $this->retrievalMetadata;
  }
  /**
   * Optional. A web search entry point that can be used to display search
   * results. This field is populated only when the grounding source is Google
   * Search.
   *
   * @param GoogleCloudAiplatformV1SearchEntryPoint $searchEntryPoint
   */
  public function setSearchEntryPoint(GoogleCloudAiplatformV1SearchEntryPoint $searchEntryPoint)
  {
    $this->searchEntryPoint = $searchEntryPoint;
  }
  /**
   * @return GoogleCloudAiplatformV1SearchEntryPoint
   */
  public function getSearchEntryPoint()
  {
    return $this->searchEntryPoint;
  }
  /**
   * Optional. Output only. A list of URIs that can be used to flag a place or
   * review for inappropriate content. This field is populated only when the
   * grounding source is Google Maps.
   *
   * @param GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri[] $sourceFlaggingUris
   */
  public function setSourceFlaggingUris($sourceFlaggingUris)
  {
    $this->sourceFlaggingUris = $sourceFlaggingUris;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingMetadataSourceFlaggingUri[]
   */
  public function getSourceFlaggingUris()
  {
    return $this->sourceFlaggingUris;
  }
  /**
   * Optional. The web search queries that were used to generate the content.
   * This field is populated only when the grounding source is Google Search.
   *
   * @param string[] $webSearchQueries
   */
  public function setWebSearchQueries($webSearchQueries)
  {
    $this->webSearchQueries = $webSearchQueries;
  }
  /**
   * @return string[]
   */
  public function getWebSearchQueries()
  {
    return $this->webSearchQueries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingMetadata');
