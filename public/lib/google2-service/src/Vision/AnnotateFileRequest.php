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

namespace Google\Service\Vision;

class AnnotateFileRequest extends \Google\Collection
{
  protected $collection_key = 'pages';
  protected $featuresType = Feature::class;
  protected $featuresDataType = 'array';
  protected $imageContextType = ImageContext::class;
  protected $imageContextDataType = '';
  protected $inputConfigType = InputConfig::class;
  protected $inputConfigDataType = '';
  /**
   * Pages of the file to perform image annotation. Pages starts from 1, we
   * assume the first page of the file is page 1. At most 5 pages are supported
   * per request. Pages can be negative. Page 1 means the first page. Page 2
   * means the second page. Page -1 means the last page. Page -2 means the
   * second to the last page. If the file is GIF instead of PDF or TIFF, page
   * refers to GIF frames. If this field is empty, by default the service
   * performs image annotation for the first 5 pages of the file.
   *
   * @var int[]
   */
  public $pages;

  /**
   * Required. Requested features.
   *
   * @param Feature[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return Feature[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * Additional context that may accompany the image(s) in the file.
   *
   * @param ImageContext $imageContext
   */
  public function setImageContext(ImageContext $imageContext)
  {
    $this->imageContext = $imageContext;
  }
  /**
   * @return ImageContext
   */
  public function getImageContext()
  {
    return $this->imageContext;
  }
  /**
   * Required. Information about the input file.
   *
   * @param InputConfig $inputConfig
   */
  public function setInputConfig(InputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return InputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Pages of the file to perform image annotation. Pages starts from 1, we
   * assume the first page of the file is page 1. At most 5 pages are supported
   * per request. Pages can be negative. Page 1 means the first page. Page 2
   * means the second page. Page -1 means the last page. Page -2 means the
   * second to the last page. If the file is GIF instead of PDF or TIFF, page
   * refers to GIF frames. If this field is empty, by default the service
   * performs image annotation for the first 5 pages of the file.
   *
   * @param int[] $pages
   */
  public function setPages($pages)
  {
    $this->pages = $pages;
  }
  /**
   * @return int[]
   */
  public function getPages()
  {
    return $this->pages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotateFileRequest::class, 'Google_Service_Vision_AnnotateFileRequest');
