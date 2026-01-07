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

class GoogleChromeManagementV1NetworkStatusReport extends \Google\Collection
{
  /**
   * Network connection state unspecified.
   */
  public const CONNECTION_STATE_NETWORK_CONNECTION_STATE_UNSPECIFIED = 'NETWORK_CONNECTION_STATE_UNSPECIFIED';
  /**
   * The network is connected and internet connectivity is available.
   */
  public const CONNECTION_STATE_ONLINE = 'ONLINE';
  /**
   * The network is connected and not in a detected portal state, but internet
   * connectivity may not be available.
   */
  public const CONNECTION_STATE_CONNECTED = 'CONNECTED';
  /**
   * The network is connected but a portal state was detected. Internet
   * connectivity may be limited.
   */
  public const CONNECTION_STATE_PORTAL = 'PORTAL';
  /**
   * The network is in the process of connecting.
   */
  public const CONNECTION_STATE_CONNECTING = 'CONNECTING';
  /**
   * The network is not connected.
   */
  public const CONNECTION_STATE_NOT_CONNECTED = 'NOT_CONNECTED';
  /**
   * Network connection type unspecified
   */
  public const CONNECTION_TYPE_NETWORK_TYPE_UNSPECIFIED = 'NETWORK_TYPE_UNSPECIFIED';
  /**
   * Cellular network connection.
   */
  public const CONNECTION_TYPE_CELLULAR = 'CELLULAR';
  /**
   * Ethernet network connection.
   */
  public const CONNECTION_TYPE_ETHERNET = 'ETHERNET';
  /**
   * Tether network connection.
   */
  public const CONNECTION_TYPE_TETHER = 'TETHER';
  /**
   * VPN network connection.
   */
  public const CONNECTION_TYPE_VPN = 'VPN';
  /**
   * Wifi network connection.
   */
  public const CONNECTION_TYPE_WIFI = 'WIFI';
  protected $collection_key = 'ipv6Address';
  /**
   * Output only. Current connection state of the network.
   *
   * @var string
   */
  public $connectionState;
  /**
   * Output only. Network connection type.
   *
   * @var string
   */
  public $connectionType;
  /**
   * Output only. Whether the wifi encryption key is turned off.
   *
   * @var bool
   */
  public $encryptionOn;
  /**
   * Output only. Gateway IP address.
   *
   * @var string
   */
  public $gatewayIpAddress;
  /**
   * Output only. The gateway IPv6 for this interface, if detected
   *
   * @var string
   */
  public $gatewayIpv6Address;
  /**
   * Output only. Network connection guid.
   *
   * @var string
   */
  public $guid;
  /**
   * Output only. IPv6 addresses assigned to this network, if any. Each address
   * is a string in standard IPv6 text representation (e.g., "2001:db8::1").
   *
   * @var string[]
   */
  public $ipv6Address;
  /**
   * Output only. LAN IP address.
   *
   * @var string
   */
  public $lanIpAddress;
  /**
   * Output only. The maximum downstream bandwidth in Kilobits per second
   * (Kbps), if reported by the network interface or connection.
   *
   * @var string
   */
  public $linkDownSpeedKbps;
  /**
   * Output only. Whether the network was detected as metered.
   *
   * @var bool
   */
  public $metered;
  /**
   * Output only. Receiving bit rate measured in Megabits per second.
   *
   * @var string
   */
  public $receivingBitRateMbps;
  /**
   * Output only. Time at which the network state was reported.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Output only. Frequency the report is sampled.
   *
   * @var string
   */
  public $sampleFrequency;
  /**
   * Output only. Signal strength for wireless networks measured in decibels.
   *
   * @var int
   */
  public $signalStrengthDbm;
  /**
   * Output only. Transmission bit rate measured in Megabits per second.
   *
   * @var string
   */
  public $transmissionBitRateMbps;
  /**
   * Output only. Transmission power measured in decibels.
   *
   * @var int
   */
  public $transmissionPowerDbm;
  /**
   * Output only. Wifi link quality. Value ranges from [0, 70]. 0 indicates no
   * signal and 70 indicates a strong signal.
   *
   * @var string
   */
  public $wifiLinkQuality;
  /**
   * Output only. Wifi power management enabled
   *
   * @var bool
   */
  public $wifiPowerManagementEnabled;

  /**
   * Output only. Current connection state of the network.
   *
   * Accepted values: NETWORK_CONNECTION_STATE_UNSPECIFIED, ONLINE, CONNECTED,
   * PORTAL, CONNECTING, NOT_CONNECTED
   *
   * @param self::CONNECTION_STATE_* $connectionState
   */
  public function setConnectionState($connectionState)
  {
    $this->connectionState = $connectionState;
  }
  /**
   * @return self::CONNECTION_STATE_*
   */
  public function getConnectionState()
  {
    return $this->connectionState;
  }
  /**
   * Output only. Network connection type.
   *
   * Accepted values: NETWORK_TYPE_UNSPECIFIED, CELLULAR, ETHERNET, TETHER, VPN,
   * WIFI
   *
   * @param self::CONNECTION_TYPE_* $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return self::CONNECTION_TYPE_*
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
  /**
   * Output only. Whether the wifi encryption key is turned off.
   *
   * @param bool $encryptionOn
   */
  public function setEncryptionOn($encryptionOn)
  {
    $this->encryptionOn = $encryptionOn;
  }
  /**
   * @return bool
   */
  public function getEncryptionOn()
  {
    return $this->encryptionOn;
  }
  /**
   * Output only. Gateway IP address.
   *
   * @param string $gatewayIpAddress
   */
  public function setGatewayIpAddress($gatewayIpAddress)
  {
    $this->gatewayIpAddress = $gatewayIpAddress;
  }
  /**
   * @return string
   */
  public function getGatewayIpAddress()
  {
    return $this->gatewayIpAddress;
  }
  /**
   * Output only. The gateway IPv6 for this interface, if detected
   *
   * @param string $gatewayIpv6Address
   */
  public function setGatewayIpv6Address($gatewayIpv6Address)
  {
    $this->gatewayIpv6Address = $gatewayIpv6Address;
  }
  /**
   * @return string
   */
  public function getGatewayIpv6Address()
  {
    return $this->gatewayIpv6Address;
  }
  /**
   * Output only. Network connection guid.
   *
   * @param string $guid
   */
  public function setGuid($guid)
  {
    $this->guid = $guid;
  }
  /**
   * @return string
   */
  public function getGuid()
  {
    return $this->guid;
  }
  /**
   * Output only. IPv6 addresses assigned to this network, if any. Each address
   * is a string in standard IPv6 text representation (e.g., "2001:db8::1").
   *
   * @param string[] $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string[]
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
   * Output only. LAN IP address.
   *
   * @param string $lanIpAddress
   */
  public function setLanIpAddress($lanIpAddress)
  {
    $this->lanIpAddress = $lanIpAddress;
  }
  /**
   * @return string
   */
  public function getLanIpAddress()
  {
    return $this->lanIpAddress;
  }
  /**
   * Output only. The maximum downstream bandwidth in Kilobits per second
   * (Kbps), if reported by the network interface or connection.
   *
   * @param string $linkDownSpeedKbps
   */
  public function setLinkDownSpeedKbps($linkDownSpeedKbps)
  {
    $this->linkDownSpeedKbps = $linkDownSpeedKbps;
  }
  /**
   * @return string
   */
  public function getLinkDownSpeedKbps()
  {
    return $this->linkDownSpeedKbps;
  }
  /**
   * Output only. Whether the network was detected as metered.
   *
   * @param bool $metered
   */
  public function setMetered($metered)
  {
    $this->metered = $metered;
  }
  /**
   * @return bool
   */
  public function getMetered()
  {
    return $this->metered;
  }
  /**
   * Output only. Receiving bit rate measured in Megabits per second.
   *
   * @param string $receivingBitRateMbps
   */
  public function setReceivingBitRateMbps($receivingBitRateMbps)
  {
    $this->receivingBitRateMbps = $receivingBitRateMbps;
  }
  /**
   * @return string
   */
  public function getReceivingBitRateMbps()
  {
    return $this->receivingBitRateMbps;
  }
  /**
   * Output only. Time at which the network state was reported.
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
   * Output only. Frequency the report is sampled.
   *
   * @param string $sampleFrequency
   */
  public function setSampleFrequency($sampleFrequency)
  {
    $this->sampleFrequency = $sampleFrequency;
  }
  /**
   * @return string
   */
  public function getSampleFrequency()
  {
    return $this->sampleFrequency;
  }
  /**
   * Output only. Signal strength for wireless networks measured in decibels.
   *
   * @param int $signalStrengthDbm
   */
  public function setSignalStrengthDbm($signalStrengthDbm)
  {
    $this->signalStrengthDbm = $signalStrengthDbm;
  }
  /**
   * @return int
   */
  public function getSignalStrengthDbm()
  {
    return $this->signalStrengthDbm;
  }
  /**
   * Output only. Transmission bit rate measured in Megabits per second.
   *
   * @param string $transmissionBitRateMbps
   */
  public function setTransmissionBitRateMbps($transmissionBitRateMbps)
  {
    $this->transmissionBitRateMbps = $transmissionBitRateMbps;
  }
  /**
   * @return string
   */
  public function getTransmissionBitRateMbps()
  {
    return $this->transmissionBitRateMbps;
  }
  /**
   * Output only. Transmission power measured in decibels.
   *
   * @param int $transmissionPowerDbm
   */
  public function setTransmissionPowerDbm($transmissionPowerDbm)
  {
    $this->transmissionPowerDbm = $transmissionPowerDbm;
  }
  /**
   * @return int
   */
  public function getTransmissionPowerDbm()
  {
    return $this->transmissionPowerDbm;
  }
  /**
   * Output only. Wifi link quality. Value ranges from [0, 70]. 0 indicates no
   * signal and 70 indicates a strong signal.
   *
   * @param string $wifiLinkQuality
   */
  public function setWifiLinkQuality($wifiLinkQuality)
  {
    $this->wifiLinkQuality = $wifiLinkQuality;
  }
  /**
   * @return string
   */
  public function getWifiLinkQuality()
  {
    return $this->wifiLinkQuality;
  }
  /**
   * Output only. Wifi power management enabled
   *
   * @param bool $wifiPowerManagementEnabled
   */
  public function setWifiPowerManagementEnabled($wifiPowerManagementEnabled)
  {
    $this->wifiPowerManagementEnabled = $wifiPowerManagementEnabled;
  }
  /**
   * @return bool
   */
  public function getWifiPowerManagementEnabled()
  {
    return $this->wifiPowerManagementEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1NetworkStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1NetworkStatusReport');
