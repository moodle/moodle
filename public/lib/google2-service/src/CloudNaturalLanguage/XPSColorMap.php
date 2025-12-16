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

namespace Google\Service\CloudNaturalLanguage;

class XPSColorMap extends \Google\Model
{
  /**
   * Should be used during training.
   *
   * @var string
   */
  public $annotationSpecIdToken;
  protected $colorType = Color::class;
  protected $colorDataType = '';
  /**
   * Should be used during preprocessing.
   *
   * @var string
   */
  public $displayName;
  protected $intColorType = XPSColorMapIntColor::class;
  protected $intColorDataType = '';

  /**
   * Should be used during training.
   *
   * @param string $annotationSpecIdToken
   */
  public function setAnnotationSpecIdToken($annotationSpecIdToken)
  {
    $this->annotationSpecIdToken = $annotationSpecIdToken;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecIdToken()
  {
    return $this->annotationSpecIdToken;
  }
  /**
   * This type is deprecated in favor of the IntColor below. This is because
   * google.type.Color represent color has a float which semantically does not
   * reflect discrete classes/categories concept. Moreover, to handle it well we
   * need to have some tolerance when converting to a discretized color. As
   * such, the recommendation is to have API surface still use google.type.Color
   * while internally IntColor is used.
   *
   * @deprecated
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * Should be used during preprocessing.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * @param XPSColorMapIntColor $intColor
   */
  public function setIntColor(XPSColorMapIntColor $intColor)
  {
    $this->intColor = $intColor;
  }
  /**
   * @return XPSColorMapIntColor
   */
  public function getIntColor()
  {
    return $this->intColor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSColorMap::class, 'Google_Service_CloudNaturalLanguage_XPSColorMap');
