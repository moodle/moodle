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

class Audio extends \Google\Model
{
  /**
   * Enable boosting high frequency components. The default is `false`.
   * **Note:** This field is not supported.
   *
   * @var bool
   */
  public $highBoost;
  /**
   * Enable boosting low frequency components. The default is `false`. **Note:**
   * This field is not supported.
   *
   * @var bool
   */
  public $lowBoost;
  /**
   * Specify audio loudness normalization in loudness units relative to full
   * scale (LUFS). Enter a value between -24 and 0 (the default), where: * -24
   * is the Advanced Television Systems Committee (ATSC A/85) standard * -23 is
   * the EU R128 broadcast standard * -19 is the prior standard for online mono
   * audio * -18 is the ReplayGain standard * -16 is the prior standard for
   * stereo audio * -14 is the new online audio standard recommended by Spotify,
   * as well as Amazon Echo * 0 disables normalization
   *
   * @var 
   */
  public $lufs;

  /**
   * Enable boosting high frequency components. The default is `false`.
   * **Note:** This field is not supported.
   *
   * @param bool $highBoost
   */
  public function setHighBoost($highBoost)
  {
    $this->highBoost = $highBoost;
  }
  /**
   * @return bool
   */
  public function getHighBoost()
  {
    return $this->highBoost;
  }
  /**
   * Enable boosting low frequency components. The default is `false`. **Note:**
   * This field is not supported.
   *
   * @param bool $lowBoost
   */
  public function setLowBoost($lowBoost)
  {
    $this->lowBoost = $lowBoost;
  }
  /**
   * @return bool
   */
  public function getLowBoost()
  {
    return $this->lowBoost;
  }
  public function setLufs($lufs)
  {
    $this->lufs = $lufs;
  }
  public function getLufs()
  {
    return $this->lufs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Audio::class, 'Google_Service_Transcoder_Audio');
