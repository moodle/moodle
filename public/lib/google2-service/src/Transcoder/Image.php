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

namespace Google\Service\Transcoder;

class Image extends \Google\Model
{
  /**
   * Target image opacity. Valid values are from `1.0` (solid, default) to `0.0`
   * (transparent), exclusive. Set this to a value greater than `0.0`.
   *
   * @var 
   */
  public $alpha;
  protected $resolutionType = NormalizedCoordinate::class;
  protected $resolutionDataType = '';
  /**
   * Required. URI of the image in Cloud Storage. For example,
   * `gs://bucket/inputs/image.png`. Only PNG and JPEG images are supported.
   *
   * @var string
   */
  public $uri;

  public function setAlpha($alpha)
  {
    $this->alpha = $alpha;
  }
  public function getAlpha()
  {
    return $this->alpha;
  }
  /**
   * Normalized image resolution, based on output video resolution. Valid
   * values: `0.0`–`1.0`. To respect the original image aspect ratio, set either
   * `x` or `y` to `0.0`. To use the original image resolution, set both `x` and
   * `y` to `0.0`.
   *
   * @param NormalizedCoordinate $resolution
   */
  public function setResolution(NormalizedCoordinate $resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return NormalizedCoordinate
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * Required. URI of the image in Cloud Storage. For example,
   * `gs://bucket/inputs/image.png`. Only PNG and JPEG images are supported.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Image::class, 'Google_Service_Transcoder_Image');
