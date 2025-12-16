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

namespace Google\Service\Dfareporting;

class MobileApp extends \Google\Model
{
  public const DIRECTORY_UNKNOWN = 'UNKNOWN';
  public const DIRECTORY_APPLE_APP_STORE = 'APPLE_APP_STORE';
  public const DIRECTORY_GOOGLE_PLAY_STORE = 'GOOGLE_PLAY_STORE';
  public const DIRECTORY_ROKU_APP_STORE = 'ROKU_APP_STORE';
  public const DIRECTORY_AMAZON_FIRETV_APP_STORE = 'AMAZON_FIRETV_APP_STORE';
  public const DIRECTORY_PLAYSTATION_APP_STORE = 'PLAYSTATION_APP_STORE';
  public const DIRECTORY_APPLE_TV_APP_STORE = 'APPLE_TV_APP_STORE';
  public const DIRECTORY_XBOX_APP_STORE = 'XBOX_APP_STORE';
  public const DIRECTORY_SAMSUNG_TV_APP_STORE = 'SAMSUNG_TV_APP_STORE';
  public const DIRECTORY_ANDROID_TV_APP_STORE = 'ANDROID_TV_APP_STORE';
  public const DIRECTORY_GENERIC_CTV_APP_STORE = 'GENERIC_CTV_APP_STORE';
  /**
   * Mobile app directory.
   *
   * @var string
   */
  public $directory;
  /**
   * ID of this mobile app.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#mobileApp".
   *
   * @var string
   */
  public $kind;
  /**
   * Publisher name.
   *
   * @var string
   */
  public $publisherName;
  /**
   * Title of this mobile app.
   *
   * @var string
   */
  public $title;

  /**
   * Mobile app directory.
   *
   * Accepted values: UNKNOWN, APPLE_APP_STORE, GOOGLE_PLAY_STORE,
   * ROKU_APP_STORE, AMAZON_FIRETV_APP_STORE, PLAYSTATION_APP_STORE,
   * APPLE_TV_APP_STORE, XBOX_APP_STORE, SAMSUNG_TV_APP_STORE,
   * ANDROID_TV_APP_STORE, GENERIC_CTV_APP_STORE
   *
   * @param self::DIRECTORY_* $directory
   */
  public function setDirectory($directory)
  {
    $this->directory = $directory;
  }
  /**
   * @return self::DIRECTORY_*
   */
  public function getDirectory()
  {
    return $this->directory;
  }
  /**
   * ID of this mobile app.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#mobileApp".
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
   * Publisher name.
   *
   * @param string $publisherName
   */
  public function setPublisherName($publisherName)
  {
    $this->publisherName = $publisherName;
  }
  /**
   * @return string
   */
  public function getPublisherName()
  {
    return $this->publisherName;
  }
  /**
   * Title of this mobile app.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileApp::class, 'Google_Service_Dfareporting_MobileApp');
