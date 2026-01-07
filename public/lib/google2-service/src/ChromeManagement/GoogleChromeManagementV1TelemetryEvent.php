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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1TelemetryEvent extends \Google\Model
{
  /**
   * Event type unknown.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Triggered when a audio devices run out of buffer data for more than 5
   * seconds.
   */
  public const EVENT_TYPE_AUDIO_SEVERE_UNDERRUN = 'AUDIO_SEVERE_UNDERRUN';
  /**
   * Triggered immediately on any changes to a network connection.
   */
  public const EVENT_TYPE_NETWORK_STATE_CHANGE = 'NETWORK_STATE_CHANGE';
  /**
   * Triggered when USB devices are added.
   */
  public const EVENT_TYPE_USB_ADDED = 'USB_ADDED';
  /**
   * Triggered when USB devices are removed.
   */
  public const EVENT_TYPE_USB_REMOVED = 'USB_REMOVED';
  /**
   * Triggered when a new HTTPS latency problem was detected or the device has
   * recovered form an existing HTTPS latency problem.
   */
  public const EVENT_TYPE_NETWORK_HTTPS_LATENCY_CHANGE = 'NETWORK_HTTPS_LATENCY_CHANGE';
  /**
   * Triggered when connected WiFi network signal strength drops below -70dBm.
   */
  public const EVENT_TYPE_WIFI_SIGNAL_STRENGTH_LOW = 'WIFI_SIGNAL_STRENGTH_LOW';
  /**
   * Triggered when connected WiFi network signal strength is recovered from a
   * signal drop.
   */
  public const EVENT_TYPE_WIFI_SIGNAL_STRENGTH_RECOVERED = 'WIFI_SIGNAL_STRENGTH_RECOVERED';
  /**
   * Triggered on changes to VPN connections.
   */
  public const EVENT_TYPE_VPN_CONNECTION_STATE_CHANGE = 'VPN_CONNECTION_STATE_CHANGE';
  /**
   * Triggered when an app is installed.
   */
  public const EVENT_TYPE_APP_INSTALLED = 'APP_INSTALLED';
  /**
   * Triggered when an app is uninstalled.
   */
  public const EVENT_TYPE_APP_UNINSTALLED = 'APP_UNINSTALLED';
  /**
   * Triggered when an app is launched.
   */
  public const EVENT_TYPE_APP_LAUNCHED = 'APP_LAUNCHED';
  /**
   * Triggered when a crash occurs.
   */
  public const EVENT_TYPE_OS_CRASH = 'OS_CRASH';
  /**
   * Triggered when an external display is connected.
   */
  public const EVENT_TYPE_EXTERNAL_DISPLAY_CONNECTED = 'EXTERNAL_DISPLAY_CONNECTED';
  /**
   * Triggered when an external display is disconnected.
   */
  public const EVENT_TYPE_EXTERNAL_DISPLAY_DISCONNECTED = 'EXTERNAL_DISPLAY_DISCONNECTED';
  protected $appInstallEventType = GoogleChromeManagementV1TelemetryAppInstallEvent::class;
  protected $appInstallEventDataType = '';
  protected $appLaunchEventType = GoogleChromeManagementV1TelemetryAppLaunchEvent::class;
  protected $appLaunchEventDataType = '';
  protected $appUninstallEventType = GoogleChromeManagementV1TelemetryAppUninstallEvent::class;
  protected $appUninstallEventDataType = '';
  protected $audioSevereUnderrunEventType = GoogleChromeManagementV1TelemetryAudioSevereUnderrunEvent::class;
  protected $audioSevereUnderrunEventDataType = '';
  protected $deviceType = GoogleChromeManagementV1TelemetryDeviceInfo::class;
  protected $deviceDataType = '';
  /**
   * The event type of the current event.
   *
   * @var string
   */
  public $eventType;
  protected $externalDisplaysEventType = GoogleChromeManagementV1TelemetryExternalDisplayEvent::class;
  protected $externalDisplaysEventDataType = '';
  protected $httpsLatencyChangeEventType = GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent::class;
  protected $httpsLatencyChangeEventDataType = '';
  /**
   * Output only. Resource name of the event.
   *
   * @var string
   */
  public $name;
  protected $networkStateChangeEventType = GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent::class;
  protected $networkStateChangeEventDataType = '';
  protected $osCrashEventType = GoogleChromeManagementV1TelemetryOsCrashEvent::class;
  protected $osCrashEventDataType = '';
  /**
   * Timestamp that represents when the event was reported.
   *
   * @var string
   */
  public $reportTime;
  protected $usbPeripheralsEventType = GoogleChromeManagementV1TelemetryUsbPeripheralsEvent::class;
  protected $usbPeripheralsEventDataType = '';
  protected $userType = GoogleChromeManagementV1TelemetryUserInfo::class;
  protected $userDataType = '';
  protected $vpnConnectionStateChangeEventType = GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent::class;
  protected $vpnConnectionStateChangeEventDataType = '';
  protected $wifiSignalStrengthEventType = GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent::class;
  protected $wifiSignalStrengthEventDataType = '';

  /**
   * Output only. Payload for app install event. Present only when `event_type`
   * is `APP_INSTALLED`.
   *
   * @param GoogleChromeManagementV1TelemetryAppInstallEvent $appInstallEvent
   */
  public function setAppInstallEvent(GoogleChromeManagementV1TelemetryAppInstallEvent $appInstallEvent)
  {
    $this->appInstallEvent = $appInstallEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryAppInstallEvent
   */
  public function getAppInstallEvent()
  {
    return $this->appInstallEvent;
  }
  /**
   * Output only. Payload for app launch event.Present only when `event_type` is
   * `APP_LAUNCHED`.
   *
   * @param GoogleChromeManagementV1TelemetryAppLaunchEvent $appLaunchEvent
   */
  public function setAppLaunchEvent(GoogleChromeManagementV1TelemetryAppLaunchEvent $appLaunchEvent)
  {
    $this->appLaunchEvent = $appLaunchEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryAppLaunchEvent
   */
  public function getAppLaunchEvent()
  {
    return $this->appLaunchEvent;
  }
  /**
   * Output only. Payload for app uninstall event. Present only when
   * `event_type` is `APP_UNINSTALLED`.
   *
   * @param GoogleChromeManagementV1TelemetryAppUninstallEvent $appUninstallEvent
   */
  public function setAppUninstallEvent(GoogleChromeManagementV1TelemetryAppUninstallEvent $appUninstallEvent)
  {
    $this->appUninstallEvent = $appUninstallEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryAppUninstallEvent
   */
  public function getAppUninstallEvent()
  {
    return $this->appUninstallEvent;
  }
  /**
   * Output only. Payload for audio severe underrun event. Present only when the
   * `event_type` field is `AUDIO_SEVERE_UNDERRUN`.
   *
   * @param GoogleChromeManagementV1TelemetryAudioSevereUnderrunEvent $audioSevereUnderrunEvent
   */
  public function setAudioSevereUnderrunEvent(GoogleChromeManagementV1TelemetryAudioSevereUnderrunEvent $audioSevereUnderrunEvent)
  {
    $this->audioSevereUnderrunEvent = $audioSevereUnderrunEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryAudioSevereUnderrunEvent
   */
  public function getAudioSevereUnderrunEvent()
  {
    return $this->audioSevereUnderrunEvent;
  }
  /**
   * Output only. Information about the device associated with the event.
   *
   * @param GoogleChromeManagementV1TelemetryDeviceInfo $device
   */
  public function setDevice(GoogleChromeManagementV1TelemetryDeviceInfo $device)
  {
    $this->device = $device;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryDeviceInfo
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * The event type of the current event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, AUDIO_SEVERE_UNDERRUN,
   * NETWORK_STATE_CHANGE, USB_ADDED, USB_REMOVED, NETWORK_HTTPS_LATENCY_CHANGE,
   * WIFI_SIGNAL_STRENGTH_LOW, WIFI_SIGNAL_STRENGTH_RECOVERED,
   * VPN_CONNECTION_STATE_CHANGE, APP_INSTALLED, APP_UNINSTALLED, APP_LAUNCHED,
   * OS_CRASH, EXTERNAL_DISPLAY_CONNECTED, EXTERNAL_DISPLAY_DISCONNECTED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Output only. Payload for external display connected/disconnected event.
   * Present only when `event_type` is `EXTERNAL_DISPLAY_CONNECTED` or
   * `EXTERNAL_DISPLAY_DISCONNECTED`.
   *
   * @param GoogleChromeManagementV1TelemetryExternalDisplayEvent $externalDisplaysEvent
   */
  public function setExternalDisplaysEvent(GoogleChromeManagementV1TelemetryExternalDisplayEvent $externalDisplaysEvent)
  {
    $this->externalDisplaysEvent = $externalDisplaysEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryExternalDisplayEvent
   */
  public function getExternalDisplaysEvent()
  {
    return $this->externalDisplaysEvent;
  }
  /**
   * Output only. Payload for HTTPS latency change event. Present only when
   * `event_type` is `NETWORK_HTTPS_LATENCY_CHANGE`.
   *
   * @param GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent $httpsLatencyChangeEvent
   */
  public function setHttpsLatencyChangeEvent(GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent $httpsLatencyChangeEvent)
  {
    $this->httpsLatencyChangeEvent = $httpsLatencyChangeEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent
   */
  public function getHttpsLatencyChangeEvent()
  {
    return $this->httpsLatencyChangeEvent;
  }
  /**
   * Output only. Resource name of the event.
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
   * Output only. Payload for network connection state change event. Present
   * only when `event_type` is `NETWORK_STATE_CHANGE`.
   *
   * @param GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent $networkStateChangeEvent
   */
  public function setNetworkStateChangeEvent(GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent $networkStateChangeEvent)
  {
    $this->networkStateChangeEvent = $networkStateChangeEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent
   */
  public function getNetworkStateChangeEvent()
  {
    return $this->networkStateChangeEvent;
  }
  /**
   * Output only. Payload for OS crash event. Present only when `event_type` is
   * `OS_CRASH`.
   *
   * @param GoogleChromeManagementV1TelemetryOsCrashEvent $osCrashEvent
   */
  public function setOsCrashEvent(GoogleChromeManagementV1TelemetryOsCrashEvent $osCrashEvent)
  {
    $this->osCrashEvent = $osCrashEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryOsCrashEvent
   */
  public function getOsCrashEvent()
  {
    return $this->osCrashEvent;
  }
  /**
   * Timestamp that represents when the event was reported.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Output only. Payload for usb peripherals event. Present only when the
   * `event_type` field is either `USB_ADDED` or `USB_REMOVED`.
   *
   * @param GoogleChromeManagementV1TelemetryUsbPeripheralsEvent $usbPeripheralsEvent
   */
  public function setUsbPeripheralsEvent(GoogleChromeManagementV1TelemetryUsbPeripheralsEvent $usbPeripheralsEvent)
  {
    $this->usbPeripheralsEvent = $usbPeripheralsEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryUsbPeripheralsEvent
   */
  public function getUsbPeripheralsEvent()
  {
    return $this->usbPeripheralsEvent;
  }
  /**
   * Output only. Information about the user associated with the event.
   *
   * @param GoogleChromeManagementV1TelemetryUserInfo $user
   */
  public function setUser(GoogleChromeManagementV1TelemetryUserInfo $user)
  {
    $this->user = $user;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryUserInfo
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * Output only. Payload for VPN connection state change event. Present only
   * when `event_type` is `VPN_CONNECTION_STATE_CHANGE`.
   *
   * @param GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent $vpnConnectionStateChangeEvent
   */
  public function setVpnConnectionStateChangeEvent(GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent $vpnConnectionStateChangeEvent)
  {
    $this->vpnConnectionStateChangeEvent = $vpnConnectionStateChangeEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent
   */
  public function getVpnConnectionStateChangeEvent()
  {
    return $this->vpnConnectionStateChangeEvent;
  }
  /**
   * Output only. Payload for WiFi signal strength events. Present only when
   * `event_type` is `WIFI_SIGNAL_STRENGTH_LOW` or
   * `WIFI_SIGNAL_STRENGTH_RECOVERED`.
   *
   * @param GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent $wifiSignalStrengthEvent
   */
  public function setWifiSignalStrengthEvent(GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent $wifiSignalStrengthEvent)
  {
    $this->wifiSignalStrengthEvent = $wifiSignalStrengthEvent;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent
   */
  public function getWifiSignalStrengthEvent()
  {
    return $this->wifiSignalStrengthEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryEvent');
