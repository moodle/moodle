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

class GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata extends \Google\Model
{
  /**
   * Output only. Image id is provided if the structured content is based on an
   * image.
   *
   * @var string
   */
  public $imageId;
  protected $structuredContentType = GoogleCloudDiscoveryengineV1ChunkStructuredContent::class;
  protected $structuredContentDataType = '';

  /**
   * Output only. Image id is provided if the structured content is based on an
   * image.
   *
   * @param string $imageId
   */
  public function setImageId($imageId)
  {
    $this->imageId = $imageId;
  }
  /**
   * @return string
   */
  public function getImageId()
  {
    return $this->imageId;
  }
  /**
   * Output only. The structured content information.
   *
   * @param GoogleCloudDiscoveryengineV1ChunkStructuredContent $structuredContent
   */
  public function setStructuredContent(GoogleCloudDiscoveryengineV1ChunkStructuredContent $structuredContent)
  {
    $this->structuredContent = $structuredContent;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ChunkStructuredContent
   */
  public function getStructuredContent()
  {
    return $this->structuredContent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ChunkAnnotationMetadata');
