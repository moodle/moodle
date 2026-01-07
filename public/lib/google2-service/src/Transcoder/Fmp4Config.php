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

class Fmp4Config extends \Google\Model
{
  /**
   * Optional. Specify the codec tag string that will be used in the media
   * bitstream. When not specified, the codec appropriate value is used.
   * Supported H265 codec tags: - `hvc1` (default) - `hev1`
   *
   * @var string
   */
  public $codecTag;

  /**
   * Optional. Specify the codec tag string that will be used in the media
   * bitstream. When not specified, the codec appropriate value is used.
   * Supported H265 codec tags: - `hvc1` (default) - `hev1`
   *
   * @param string $codecTag
   */
  public function setCodecTag($codecTag)
  {
    $this->codecTag = $codecTag;
  }
  /**
   * @return string
   */
  public function getCodecTag()
  {
    return $this->codecTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Fmp4Config::class, 'Google_Service_Transcoder_Fmp4Config');
