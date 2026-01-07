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

namespace Google\Service\AuthorizedBuyersMarketplace;

class PublisherProfileMobileApplication extends \Google\Model
{
  /**
   * A placeholder for an unknown app store.
   */
  public const APP_STORE_APP_STORE_TYPE_UNSPECIFIED = 'APP_STORE_TYPE_UNSPECIFIED';
  /**
   * Apple iTunes
   */
  public const APP_STORE_APPLE_ITUNES = 'APPLE_ITUNES';
  /**
   * Google Play
   */
  public const APP_STORE_GOOGLE_PLAY = 'GOOGLE_PLAY';
  /**
   * Roku
   */
  public const APP_STORE_ROKU = 'ROKU';
  /**
   * Amazon Fire TV
   */
  public const APP_STORE_AMAZON_FIRE_TV = 'AMAZON_FIRE_TV';
  /**
   * PlayStation
   */
  public const APP_STORE_PLAYSTATION = 'PLAYSTATION';
  /**
   * Xbox
   */
  public const APP_STORE_XBOX = 'XBOX';
  /**
   * Samsung TV
   */
  public const APP_STORE_SAMSUNG_TV = 'SAMSUNG_TV';
  /**
   * Amazon Appstore
   */
  public const APP_STORE_AMAZON = 'AMAZON';
  /**
   * OPPO App Market
   */
  public const APP_STORE_OPPO = 'OPPO';
  /**
   * Samsung Galaxy Store
   */
  public const APP_STORE_SAMSUNG = 'SAMSUNG';
  /**
   * VIVO App Store
   */
  public const APP_STORE_VIVO = 'VIVO';
  /**
   * Xiaomi GetApps
   */
  public const APP_STORE_XIAOMI = 'XIAOMI';
  /**
   * LG TV
   */
  public const APP_STORE_LG_TV = 'LG_TV';
  /**
   * The app store the app belongs to. Can be used to filter the response of the
   * publisherProfiles.list method.
   *
   * @var string
   */
  public $appStore;
  /**
   * The external ID for the app from its app store. Can be used to filter the
   * response of the publisherProfiles.list method.
   *
   * @var string
   */
  public $externalAppId;
  /**
   * The name of the app.
   *
   * @var string
   */
  public $name;

  /**
   * The app store the app belongs to. Can be used to filter the response of the
   * publisherProfiles.list method.
   *
   * Accepted values: APP_STORE_TYPE_UNSPECIFIED, APPLE_ITUNES, GOOGLE_PLAY,
   * ROKU, AMAZON_FIRE_TV, PLAYSTATION, XBOX, SAMSUNG_TV, AMAZON, OPPO, SAMSUNG,
   * VIVO, XIAOMI, LG_TV
   *
   * @param self::APP_STORE_* $appStore
   */
  public function setAppStore($appStore)
  {
    $this->appStore = $appStore;
  }
  /**
   * @return self::APP_STORE_*
   */
  public function getAppStore()
  {
    return $this->appStore;
  }
  /**
   * The external ID for the app from its app store. Can be used to filter the
   * response of the publisherProfiles.list method.
   *
   * @param string $externalAppId
   */
  public function setExternalAppId($externalAppId)
  {
    $this->externalAppId = $externalAppId;
  }
  /**
   * @return string
   */
  public function getExternalAppId()
  {
    return $this->externalAppId;
  }
  /**
   * The name of the app.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublisherProfileMobileApplication::class, 'Google_Service_AuthorizedBuyersMarketplace_PublisherProfileMobileApplication');
