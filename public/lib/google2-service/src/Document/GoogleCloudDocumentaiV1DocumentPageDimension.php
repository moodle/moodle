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

class GoogleCloudDocumentaiV1DocumentPageDimension extends \Google\Model
{
  /**
   * Page height.
   *
   * @var float
   */
  public $height;
  /**
   * Dimension unit.
   *
   * @var string
   */
  public $unit;
  /**
   * Page width.
   *
   * @var float
   */
  public $width;

  /**
   * Page height.
   *
   * @param float $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return float
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * Dimension unit.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * Page width.
   *
   * @param float $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return float
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageDimension::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentPageDimension');
