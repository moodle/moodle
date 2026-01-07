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

class BwdifConfig extends \Google\Model
{
  /**
   * Deinterlace all frames rather than just the frames identified as
   * interlaced. The default is `false`.
   *
   * @var bool
   */
  public $deinterlaceAllFrames;
  /**
   * Specifies the deinterlacing mode to adopt. The default is `send_frame`.
   * Supported values: - `send_frame`: Output one frame for each frame -
   * `send_field`: Output one frame for each field
   *
   * @var string
   */
  public $mode;
  /**
   * The picture field parity assumed for the input interlaced video. The
   * default is `auto`. Supported values: - `tff`: Assume the top field is first
   * - `bff`: Assume the bottom field is first - `auto`: Enable automatic
   * detection of field parity
   *
   * @var string
   */
  public $parity;

  /**
   * Deinterlace all frames rather than just the frames identified as
   * interlaced. The default is `false`.
   *
   * @param bool $deinterlaceAllFrames
   */
  public function setDeinterlaceAllFrames($deinterlaceAllFrames)
  {
    $this->deinterlaceAllFrames = $deinterlaceAllFrames;
  }
  /**
   * @return bool
   */
  public function getDeinterlaceAllFrames()
  {
    return $this->deinterlaceAllFrames;
  }
  /**
   * Specifies the deinterlacing mode to adopt. The default is `send_frame`.
   * Supported values: - `send_frame`: Output one frame for each frame -
   * `send_field`: Output one frame for each field
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The picture field parity assumed for the input interlaced video. The
   * default is `auto`. Supported values: - `tff`: Assume the top field is first
   * - `bff`: Assume the bottom field is first - `auto`: Enable automatic
   * detection of field parity
   *
   * @param string $parity
   */
  public function setParity($parity)
  {
    $this->parity = $parity;
  }
  /**
   * @return string
   */
  public function getParity()
  {
    return $this->parity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BwdifConfig::class, 'Google_Service_Transcoder_BwdifConfig');
