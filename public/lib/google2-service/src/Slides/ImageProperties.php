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

namespace Google\Service\Slides;

class ImageProperties extends \Google\Model
{
  /**
   * The brightness effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect. This property is read-only.
   *
   * @var float
   */
  public $brightness;
  /**
   * The contrast effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect. This property is read-only.
   *
   * @var float
   */
  public $contrast;
  protected $cropPropertiesType = CropProperties::class;
  protected $cropPropertiesDataType = '';
  protected $linkType = Link::class;
  protected $linkDataType = '';
  protected $outlineType = Outline::class;
  protected $outlineDataType = '';
  protected $recolorType = Recolor::class;
  protected $recolorDataType = '';
  protected $shadowType = Shadow::class;
  protected $shadowDataType = '';
  /**
   * The transparency effect of the image. The value should be in the interval
   * [0.0, 1.0], where 0 means no effect and 1 means completely transparent.
   * This property is read-only.
   *
   * @var float
   */
  public $transparency;

  /**
   * The brightness effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect. This property is read-only.
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
   * The contrast effect of the image. The value should be in the interval
   * [-1.0, 1.0], where 0 means no effect. This property is read-only.
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
   * The crop properties of the image. If not set, the image is not cropped.
   * This property is read-only.
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
   * The hyperlink destination of the image. If unset, there is no link.
   *
   * @param Link $link
   */
  public function setLink(Link $link)
  {
    $this->link = $link;
  }
  /**
   * @return Link
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The outline of the image. If not set, the image has no outline.
   *
   * @param Outline $outline
   */
  public function setOutline(Outline $outline)
  {
    $this->outline = $outline;
  }
  /**
   * @return Outline
   */
  public function getOutline()
  {
    return $this->outline;
  }
  /**
   * The recolor effect of the image. If not set, the image is not recolored.
   * This property is read-only.
   *
   * @param Recolor $recolor
   */
  public function setRecolor(Recolor $recolor)
  {
    $this->recolor = $recolor;
  }
  /**
   * @return Recolor
   */
  public function getRecolor()
  {
    return $this->recolor;
  }
  /**
   * The shadow of the image. If not set, the image has no shadow. This property
   * is read-only.
   *
   * @param Shadow $shadow
   */
  public function setShadow(Shadow $shadow)
  {
    $this->shadow = $shadow;
  }
  /**
   * @return Shadow
   */
  public function getShadow()
  {
    return $this->shadow;
  }
  /**
   * The transparency effect of the image. The value should be in the interval
   * [0.0, 1.0], where 0 means no effect and 1 means completely transparent.
   * This property is read-only.
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
class_alias(ImageProperties::class, 'Google_Service_Slides_ImageProperties');
