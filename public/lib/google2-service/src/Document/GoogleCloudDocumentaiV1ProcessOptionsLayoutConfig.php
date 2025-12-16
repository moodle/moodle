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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig extends \Google\Model
{
  protected $chunkingConfigType = GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig::class;
  protected $chunkingConfigDataType = '';
  /**
   * Optional. Whether to include image annotations in layout parser response.
   *
   * @var bool
   */
  public $enableImageAnnotation;
  /**
   * Optional. Whether to include table annotations in layout parser response.
   *
   * @var bool
   */
  public $enableTableAnnotation;
  /**
   * Optional. Whether to include bounding boxes in layout parser processor
   * response.
   *
   * @var bool
   */
  public $returnBoundingBoxes;
  /**
   * Optional. Whether to include images in layout parser processor response.
   *
   * @var bool
   */
  public $returnImages;

  /**
   * Optional. Config for chunking in layout parser processor.
   *
   * @param GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig $chunkingConfig
   */
  public function setChunkingConfig(GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig $chunkingConfig)
  {
    $this->chunkingConfig = $chunkingConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig
   */
  public function getChunkingConfig()
  {
    return $this->chunkingConfig;
  }
  /**
   * Optional. Whether to include image annotations in layout parser response.
   *
   * @param bool $enableImageAnnotation
   */
  public function setEnableImageAnnotation($enableImageAnnotation)
  {
    $this->enableImageAnnotation = $enableImageAnnotation;
  }
  /**
   * @return bool
   */
  public function getEnableImageAnnotation()
  {
    return $this->enableImageAnnotation;
  }
  /**
   * Optional. Whether to include table annotations in layout parser response.
   *
   * @param bool $enableTableAnnotation
   */
  public function setEnableTableAnnotation($enableTableAnnotation)
  {
    $this->enableTableAnnotation = $enableTableAnnotation;
  }
  /**
   * @return bool
   */
  public function getEnableTableAnnotation()
  {
    return $this->enableTableAnnotation;
  }
  /**
   * Optional. Whether to include bounding boxes in layout parser processor
   * response.
   *
   * @param bool $returnBoundingBoxes
   */
  public function setReturnBoundingBoxes($returnBoundingBoxes)
  {
    $this->returnBoundingBoxes = $returnBoundingBoxes;
  }
  /**
   * @return bool
   */
  public function getReturnBoundingBoxes()
  {
    return $this->returnBoundingBoxes;
  }
  /**
   * Optional. Whether to include images in layout parser processor response.
   *
   * @param bool $returnImages
   */
  public function setReturnImages($returnImages)
  {
    $this->returnImages = $returnImages;
  }
  /**
   * @return bool
   */
  public function getReturnImages()
  {
    return $this->returnImages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig');
