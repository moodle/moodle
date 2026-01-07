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

namespace Google\Service\MyBusinessLodging;

class Connectivity extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_WIFI_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_WIFI_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_WIFI_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_WIFI_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PUBLIC_INTERNET_TERMINAL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PUBLIC_INTERNET_TERMINAL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PUBLIC_INTERNET_TERMINAL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PUBLIC_INTERNET_TERMINAL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const WIFI_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const WIFI_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const WIFI_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Free wifi. The hotel offers guests wifi for free.
   *
   * @var bool
   */
  public $freeWifi;
  /**
   * Free wifi exception.
   *
   * @var string
   */
  public $freeWifiException;
  /**
   * Public area wifi available. Guests have the ability to wirelessly connect
   * to the internet in the areas of the hotel accessible to anyone. Can be free
   * or for a fee.
   *
   * @var bool
   */
  public $publicAreaWifiAvailable;
  /**
   * Public area wifi available exception.
   *
   * @var string
   */
  public $publicAreaWifiAvailableException;
  /**
   * Public internet terminal. An area of the hotel supplied with computers and
   * designated for the purpose of providing guests with the ability to access
   * the internet.
   *
   * @var bool
   */
  public $publicInternetTerminal;
  /**
   * Public internet terminal exception.
   *
   * @var string
   */
  public $publicInternetTerminalException;
  /**
   * Wifi available. The hotel provides the ability for guests to wirelessly
   * connect to the internet. Can be in the public areas of the hotel and/or in
   * the guest rooms. Can be free or for a fee.
   *
   * @var bool
   */
  public $wifiAvailable;
  /**
   * Wifi available exception.
   *
   * @var string
   */
  public $wifiAvailableException;

  /**
   * Free wifi. The hotel offers guests wifi for free.
   *
   * @param bool $freeWifi
   */
  public function setFreeWifi($freeWifi)
  {
    $this->freeWifi = $freeWifi;
  }
  /**
   * @return bool
   */
  public function getFreeWifi()
  {
    return $this->freeWifi;
  }
  /**
   * Free wifi exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_WIFI_EXCEPTION_* $freeWifiException
   */
  public function setFreeWifiException($freeWifiException)
  {
    $this->freeWifiException = $freeWifiException;
  }
  /**
   * @return self::FREE_WIFI_EXCEPTION_*
   */
  public function getFreeWifiException()
  {
    return $this->freeWifiException;
  }
  /**
   * Public area wifi available. Guests have the ability to wirelessly connect
   * to the internet in the areas of the hotel accessible to anyone. Can be free
   * or for a fee.
   *
   * @param bool $publicAreaWifiAvailable
   */
  public function setPublicAreaWifiAvailable($publicAreaWifiAvailable)
  {
    $this->publicAreaWifiAvailable = $publicAreaWifiAvailable;
  }
  /**
   * @return bool
   */
  public function getPublicAreaWifiAvailable()
  {
    return $this->publicAreaWifiAvailable;
  }
  /**
   * Public area wifi available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_* $publicAreaWifiAvailableException
   */
  public function setPublicAreaWifiAvailableException($publicAreaWifiAvailableException)
  {
    $this->publicAreaWifiAvailableException = $publicAreaWifiAvailableException;
  }
  /**
   * @return self::PUBLIC_AREA_WIFI_AVAILABLE_EXCEPTION_*
   */
  public function getPublicAreaWifiAvailableException()
  {
    return $this->publicAreaWifiAvailableException;
  }
  /**
   * Public internet terminal. An area of the hotel supplied with computers and
   * designated for the purpose of providing guests with the ability to access
   * the internet.
   *
   * @param bool $publicInternetTerminal
   */
  public function setPublicInternetTerminal($publicInternetTerminal)
  {
    $this->publicInternetTerminal = $publicInternetTerminal;
  }
  /**
   * @return bool
   */
  public function getPublicInternetTerminal()
  {
    return $this->publicInternetTerminal;
  }
  /**
   * Public internet terminal exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PUBLIC_INTERNET_TERMINAL_EXCEPTION_* $publicInternetTerminalException
   */
  public function setPublicInternetTerminalException($publicInternetTerminalException)
  {
    $this->publicInternetTerminalException = $publicInternetTerminalException;
  }
  /**
   * @return self::PUBLIC_INTERNET_TERMINAL_EXCEPTION_*
   */
  public function getPublicInternetTerminalException()
  {
    return $this->publicInternetTerminalException;
  }
  /**
   * Wifi available. The hotel provides the ability for guests to wirelessly
   * connect to the internet. Can be in the public areas of the hotel and/or in
   * the guest rooms. Can be free or for a fee.
   *
   * @param bool $wifiAvailable
   */
  public function setWifiAvailable($wifiAvailable)
  {
    $this->wifiAvailable = $wifiAvailable;
  }
  /**
   * @return bool
   */
  public function getWifiAvailable()
  {
    return $this->wifiAvailable;
  }
  /**
   * Wifi available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::WIFI_AVAILABLE_EXCEPTION_* $wifiAvailableException
   */
  public function setWifiAvailableException($wifiAvailableException)
  {
    $this->wifiAvailableException = $wifiAvailableException;
  }
  /**
   * @return self::WIFI_AVAILABLE_EXCEPTION_*
   */
  public function getWifiAvailableException()
  {
    return $this->wifiAvailableException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connectivity::class, 'Google_Service_MyBusinessLodging_Connectivity');
