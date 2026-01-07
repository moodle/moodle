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

namespace Google\Service\Docs;

class ImageProperties extends \Google\Model
{
  /**
   * The clockwise rotation angle of the image, in radians.
   *
   * @var float
   */
  public $angle;
  /**
   * The brightness effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect.
   *
   * @var float
   */
  public $brightness;
  /**
   * A URI to the image with a default lifetime of 30 minutes. This URI is
   * tagged with the account of the requester. Anyone with the URI effectively
   * accesses the image as the original requester. Access to the image may be
   * lost if the document's sharing settings change.
   *
   * @var string
   */
  public $contentUri;
  /**
   * The contrast effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect.
   *
   * @var float
   */
  public $contrast;
  protected $cropPropertiesType = CropProperties::class;
  protected $cropPropertiesDataType = '';
  /**
   * The source URI is the URI used to insert the image. The source URI can be
   * empty.
   *
   * @var string
   */
  public $sourceUri;
  /**
   * The transparency effect of the image. The value should be in the interval
   * [0.0, 1.0], where 0 means no effect and 1 means transparent.
   *
   * @var float
   */
  public $transparency;

  /**
   * The clockwise rotation angle of the image, in radians.
   *
   * @param float $angle
   */
  public function setAngle($angle)
  {
    $this->angle = $angle;
  }
  /**
   * @return float
   */
  public function getAngle()
  {
    return $this->angle;
  }
  /**
   * The brightness effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect.
   *
   * @param float $brightness
   */
  public function setBrightness($brightness)
  {
    $this->brightness = $brightness;
  }
  /**
   * @return float
   */
  public function getBrightness()
  {
    return $this->brightness;
  }
  /**
   * A URI to the image with a default lifetime of 30 minutes. This URI is
   * tagged with the account of the requester. Anyone with the URI effectively
   * accesses the image as the original requester. Access to the image may be
   * lost if the document's sharing settings change.
   *
   * @param string $contentUri
   */
  public function setContentUri($contentUri)
  {
    $this->contentUri = $contentUri;
  }
  /**
   * @return string
   */
  public function getContentUri()
  {
    return $this->contentUri;
  }
  /**
   * The contrast effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect.
   *
   * @param float $contrast
   */
  public function setContrast($contrast)
  {
    $this->contrast = $contrast;
  }
  /**
   * @return float
   */
  public function getContrast()
  {
    return $this->contrast;
  }
  /**
   * The crop properties of the image.
   *
   * @param CropProperties $cropProperties
   */
  public function setCropProperties(CropProperties $cropProperties)
  {
    $this->cropProperties = $cropProperties;
  }
  /**
   * @return CropProperties
   */
  public function getCropProperties()
  {
    return $this->cropProperties;
  }
  /**
   * The source URI is the URI used to insert the image. The source URI can be
   * empty.
   *
   * @param string $sourceUri
   */
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return string
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
  /**
   * The transparency effect of the image. The value should be in the interval
   * [0.0, 1.0], where 0 means no effect and 1 means transparent.
   *
   * @param float $transparency
   */
  public function setTransparency($transparency)
  {
    $this->transparency = $transparency;
  }
  /**
   * @return float
   */
  public function getTransparency()
  {
    return $this->transparency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageProperties::class, 'Google_Service_Docs_ImageProperties');
