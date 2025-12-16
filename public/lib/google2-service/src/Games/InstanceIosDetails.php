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

namespace Google\Service\Games;

class InstanceIosDetails extends \Google\Model
{
  /**
   * Bundle identifier.
   *
   * @var string
   */
  public $bundleIdentifier;
  /**
   * iTunes App ID.
   *
   * @var string
   */
  public $itunesAppId;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#instanceIosDetails`.
   *
   * @var string
   */
  public $kind;
  /**
   * Indicates that this instance is the default for new installations on iPad
   * devices.
   *
   * @var bool
   */
  public $preferredForIpad;
  /**
   * Indicates that this instance is the default for new installations on iPhone
   * devices.
   *
   * @var bool
   */
  public $preferredForIphone;
  /**
   * Flag to indicate if this instance supports iPad.
   *
   * @var bool
   */
  public $supportIpad;
  /**
   * Flag to indicate if this instance supports iPhone.
   *
   * @var bool
   */
  public $supportIphone;

  /**
   * Bundle identifier.
   *
   * @param string $bundleIdentifier
   */
  public function setBundleIdentifier($bundleIdentifier)
  {
    $this->bundleIdentifier = $bundleIdentifier;
  }
  /**
   * @return string
   */
  public function getBundleIdentifier()
  {
    return $this->bundleIdentifier;
  }
  /**
   * iTunes App ID.
   *
   * @param string $itunesAppId
   */
  public function setItunesAppId($itunesAppId)
  {
    $this->itunesAppId = $itunesAppId;
  }
  /**
   * @return string
   */
  public function getItunesAppId()
  {
    return $this->itunesAppId;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#instanceIosDetails`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Indicates that this instance is the default for new installations on iPad
   * devices.
   *
   * @param bool $preferredForIpad
   */
  public function setPreferredForIpad($preferredForIpad)
  {
    $this->preferredForIpad = $preferredForIpad;
  }
  /**
   * @return bool
   */
  public function getPreferredForIpad()
  {
    return $this->preferredForIpad;
  }
  /**
   * Indicates that this instance is the default for new installations on iPhone
   * devices.
   *
   * @param bool $preferredForIphone
   */
  public function setPreferredForIphone($preferredForIphone)
  {
    $this->preferredForIphone = $preferredForIphone;
  }
  /**
   * @return bool
   */
  public function getPreferredForIphone()
  {
    return $this->preferredForIphone;
  }
  /**
   * Flag to indicate if this instance supports iPad.
   *
   * @param bool $supportIpad
   */
  public function setSupportIpad($supportIpad)
  {
    $this->supportIpad = $supportIpad;
  }
  /**
   * @return bool
   */
  public function getSupportIpad()
  {
    return $this->supportIpad;
  }
  /**
   * Flag to indicate if this instance supports iPhone.
   *
   * @param bool $supportIphone
   */
  public function setSupportIphone($supportIphone)
  {
    $this->supportIphone = $supportIphone;
  }
  /**
   * @return bool
   */
  public function getSupportIphone()
  {
    return $this->supportIphone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceIosDetails::class, 'Google_Service_Games_InstanceIosDetails');
