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

namespace Google\Service\FirebaseCloudMessaging;

class LightSettings extends \Google\Model
{
  protected $colorType = Color::class;
  protected $colorDataType = '';
  /**
   * Required. Along with `light_on_duration `, define the blink rate of LED
   * flashes. Resolution defined by
   * [proto.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration)
   *
   * @var string
   */
  public $lightOffDuration;
  /**
   * Required. Along with `light_off_duration`, define the blink rate of LED
   * flashes. Resolution defined by
   * [proto.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration)
   *
   * @var string
   */
  public $lightOnDuration;

  /**
   * Required. Set `color` of the LED with [google.type.Color](https://github.co
   * m/googleapis/googleapis/blob/master/google/type/color.proto).
   *
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * Required. Along with `light_on_duration `, define the blink rate of LED
   * flashes. Resolution defined by
   * [proto.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration)
   *
   * @param string $lightOffDuration
   */
  public function setLightOffDuration($lightOffDuration)
  {
    $this->lightOffDuration = $lightOffDuration;
  }
  /**
   * @return string
   */
  public function getLightOffDuration()
  {
    return $this->lightOffDuration;
  }
  /**
   * Required. Along with `light_off_duration`, define the blink rate of LED
   * flashes. Resolution defined by
   * [proto.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration)
   *
   * @param string $lightOnDuration
   */
  public function setLightOnDuration($lightOnDuration)
  {
    $this->lightOnDuration = $lightOnDuration;
  }
  /**
   * @return string
   */
  public function getLightOnDuration()
  {
    return $this->lightOnDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LightSettings::class, 'Google_Service_FirebaseCloudMessaging_LightSettings');
