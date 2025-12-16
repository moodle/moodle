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

class Deblock extends \Google\Model
{
  /**
   * Enable deblocker. The default is `false`.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Set strength of the deblocker. Enter a value between 0 and 1. The higher
   * the value, the stronger the block removal. 0 is no deblocking. The default
   * is 0.
   *
   * @var 
   */
  public $strength;

  /**
   * Enable deblocker. The default is `false`.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  public function setStrength($strength)
  {
    $this->strength = $strength;
  }
  public function getStrength()
  {
    return $this->strength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Deblock::class, 'Google_Service_Transcoder_Deblock');
