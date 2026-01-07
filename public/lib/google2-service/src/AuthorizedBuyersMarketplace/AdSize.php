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

namespace Google\Service\AuthorizedBuyersMarketplace;

class AdSize extends \Google\Model
{
  /**
   * A placeholder for an undefined size type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Ad slot with size specified by height and width in pixels.
   */
  public const TYPE_PIXEL = 'PIXEL';
  /**
   * Special size to describe an interstitial ad slot.
   */
  public const TYPE_INTERSTITIAL = 'INTERSTITIAL';
  /**
   * Native (mobile) ads rendered by the publisher.
   */
  public const TYPE_NATIVE = 'NATIVE';
  /**
   * Fluid size (responsive size) can be resized automatically with the change
   * of outside environment.
   */
  public const TYPE_FLUID = 'FLUID';
  /**
   * The height of the ad slot in pixels. This field will be present only when
   * size type is `PIXEL`.
   *
   * @var string
   */
  public $height;
  /**
   * The type of the ad slot size.
   *
   * @var string
   */
  public $type;
  /**
   * The width of the ad slot in pixels. This field will be present only when
   * size type is `PIXEL`.
   *
   * @var string
   */
  public $width;

  /**
   * The height of the ad slot in pixels. This field will be present only when
   * size type is `PIXEL`.
   *
   * @param string $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return string
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * The type of the ad slot size.
   *
   * Accepted values: TYPE_UNSPECIFIED, PIXEL, INTERSTITIAL, NATIVE, FLUID
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The width of the ad slot in pixels. This field will be present only when
   * size type is `PIXEL`.
   *
   * @param string $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return string
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdSize::class, 'Google_Service_AuthorizedBuyersMarketplace_AdSize');
