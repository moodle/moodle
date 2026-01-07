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

class DrmSystems extends \Google\Model
{
  protected $clearkeyType = Clearkey::class;
  protected $clearkeyDataType = '';
  protected $fairplayType = Fairplay::class;
  protected $fairplayDataType = '';
  protected $playreadyType = Playready::class;
  protected $playreadyDataType = '';
  protected $widevineType = Widevine::class;
  protected $widevineDataType = '';

  /**
   * Clearkey configuration.
   *
   * @param Clearkey $clearkey
   */
  public function setClearkey(Clearkey $clearkey)
  {
    $this->clearkey = $clearkey;
  }
  /**
   * @return Clearkey
   */
  public function getClearkey()
  {
    return $this->clearkey;
  }
  /**
   * Fairplay configuration.
   *
   * @param Fairplay $fairplay
   */
  public function setFairplay(Fairplay $fairplay)
  {
    $this->fairplay = $fairplay;
  }
  /**
   * @return Fairplay
   */
  public function getFairplay()
  {
    return $this->fairplay;
  }
  /**
   * Playready configuration.
   *
   * @param Playready $playready
   */
  public function setPlayready(Playready $playready)
  {
    $this->playready = $playready;
  }
  /**
   * @return Playready
   */
  public function getPlayready()
  {
    return $this->playready;
  }
  /**
   * Widevine configuration.
   *
   * @param Widevine $widevine
   */
  public function setWidevine(Widevine $widevine)
  {
    $this->widevine = $widevine;
  }
  /**
   * @return Widevine
   */
  public function getWidevine()
  {
    return $this->widevine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DrmSystems::class, 'Google_Service_Transcoder_DrmSystems');
