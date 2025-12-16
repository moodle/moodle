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

class Instance extends \Google\Model
{
  /**
   * Instance is for Android.
   */
  public const PLATFORM_TYPE_ANDROID = 'ANDROID';
  /**
   * Instance is for iOS.
   */
  public const PLATFORM_TYPE_IOS = 'IOS';
  /**
   * Instance is for Web App.
   */
  public const PLATFORM_TYPE_WEB_APP = 'WEB_APP';
  /**
   * URI which shows where a user can acquire this instance.
   *
   * @var string
   */
  public $acquisitionUri;
  protected $androidInstanceType = InstanceAndroidDetails::class;
  protected $androidInstanceDataType = '';
  protected $iosInstanceType = InstanceIosDetails::class;
  protected $iosInstanceDataType = '';
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#instance`.
   *
   * @var string
   */
  public $kind;
  /**
   * Localized display name.
   *
   * @var string
   */
  public $name;
  /**
   * The platform type.
   *
   * @var string
   */
  public $platformType;
  /**
   * Flag to show if this game instance supports realtime play.
   *
   * @var bool
   */
  public $realtimePlay;
  /**
   * Flag to show if this game instance supports turn based play.
   *
   * @var bool
   */
  public $turnBasedPlay;
  protected $webInstanceType = InstanceWebDetails::class;
  protected $webInstanceDataType = '';

  /**
   * URI which shows where a user can acquire this instance.
   *
   * @param string $acquisitionUri
   */
  public function setAcquisitionUri($acquisitionUri)
  {
    $this->acquisitionUri = $acquisitionUri;
  }
  /**
   * @return string
   */
  public function getAcquisitionUri()
  {
    return $this->acquisitionUri;
  }
  /**
   * Platform dependent details for Android.
   *
   * @param InstanceAndroidDetails $androidInstance
   */
  public function setAndroidInstance(InstanceAndroidDetails $androidInstance)
  {
    $this->androidInstance = $androidInstance;
  }
  /**
   * @return InstanceAndroidDetails
   */
  public function getAndroidInstance()
  {
    return $this->androidInstance;
  }
  /**
   * Platform dependent details for iOS.
   *
   * @param InstanceIosDetails $iosInstance
   */
  public function setIosInstance(InstanceIosDetails $iosInstance)
  {
    $this->iosInstance = $iosInstance;
  }
  /**
   * @return InstanceIosDetails
   */
  public function getIosInstance()
  {
    return $this->iosInstance;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#instance`.
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
   * Localized display name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The platform type.
   *
   * Accepted values: ANDROID, IOS, WEB_APP
   *
   * @param self::PLATFORM_TYPE_* $platformType
   */
  public function setPlatformType($platformType)
  {
    $this->platformType = $platformType;
  }
  /**
   * @return self::PLATFORM_TYPE_*
   */
  public function getPlatformType()
  {
    return $this->platformType;
  }
  /**
   * Flag to show if this game instance supports realtime play.
   *
   * @param bool $realtimePlay
   */
  public function setRealtimePlay($realtimePlay)
  {
    $this->realtimePlay = $realtimePlay;
  }
  /**
   * @return bool
   */
  public function getRealtimePlay()
  {
    return $this->realtimePlay;
  }
  /**
   * Flag to show if this game instance supports turn based play.
   *
   * @param bool $turnBasedPlay
   */
  public function setTurnBasedPlay($turnBasedPlay)
  {
    $this->turnBasedPlay = $turnBasedPlay;
  }
  /**
   * @return bool
   */
  public function getTurnBasedPlay()
  {
    return $this->turnBasedPlay;
  }
  /**
   * Platform dependent details for Web.
   *
   * @param InstanceWebDetails $webInstance
   */
  public function setWebInstance(InstanceWebDetails $webInstance)
  {
    $this->webInstance = $webInstance;
  }
  /**
   * @return InstanceWebDetails
   */
  public function getWebInstance()
  {
    return $this->webInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_Games_Instance');
