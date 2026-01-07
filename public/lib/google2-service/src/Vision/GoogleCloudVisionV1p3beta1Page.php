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

class GoogleCloudVisionV1p3beta1Page extends \Google\Collection
{
  protected $collection_key = 'blocks';
  protected $blocksType = GoogleCloudVisionV1p3beta1Block::class;
  protected $blocksDataType = 'array';
  /**
   * Confidence of the OCR results on the page. Range [0, 1].
   *
   * @var float
   */
  public $confidence;
  /**
   * Page height. For PDFs the unit is points. For images (including TIFFs) the
   * unit is pixels.
   *
   * @var int
   */
  public $height;
  protected $propertyType = GoogleCloudVisionV1p3beta1TextAnnotationTextProperty::class;
  protected $propertyDataType = '';
  /**
   * Page width. For PDFs the unit is points. For images (including TIFFs) the
   * unit is pixels.
   *
   * @var int
   */
  public $width;

  /**
   * List of blocks of text, images etc on this page.
   *
   * @param GoogleCloudVisionV1p3beta1Block[] $blocks
   */
  public function setBlocks($blocks)
  {
    $this->blocks = $blocks;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1Block[]
   */
  public function getBlocks()
  {
    return $this->blocks;
  }
  /**
   * Confidence of the OCR results on the page. Range [0, 1].
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Page height. For PDFs the unit is points. For images (including TIFFs) the
   * unit is pixels.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * Additional information detected on the page.
   *
   * @param GoogleCloudVisionV1p3beta1TextAnnotationTextProperty $property
   */
  public function setProperty(GoogleCloudVisionV1p3beta1TextAnnotationTextProperty $property)
  {
    $this->property = $property;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1TextAnnotationTextProperty
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * Page width. For PDFs the unit is points. For images (including TIFFs) the
   * unit is pixels.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p3beta1Page::class, 'Google_Service_Vision_GoogleCloudVisionV1p3beta1Page');
