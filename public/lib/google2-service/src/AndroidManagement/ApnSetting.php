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

namespace Google\Service\AndroidManagement;

class ApnSetting extends \Google\Collection
{
  /**
   * Unspecified. Defaults to NOT_ALWAYS_ON.
   */
  public const ALWAYS_ON_SETTING_ALWAYS_ON_SETTING_UNSPECIFIED = 'ALWAYS_ON_SETTING_UNSPECIFIED';
  /**
   * The PDU session brought up by this APN should not be always on.
   */
  public const ALWAYS_ON_SETTING_NOT_ALWAYS_ON = 'NOT_ALWAYS_ON';
  /**
   * The PDU session brought up by this APN should always be on. Supported on
   * Android 15 and above. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 15.
   */
  public const ALWAYS_ON_SETTING_ALWAYS_ON = 'ALWAYS_ON';
  /**
   * Unspecified. If username is empty, defaults to NONE. Otherwise, defaults to
   * PAP_OR_CHAP.
   */
  public const AUTH_TYPE_AUTH_TYPE_UNSPECIFIED = 'AUTH_TYPE_UNSPECIFIED';
  /**
   * Authentication is not required.
   */
  public const AUTH_TYPE_NONE = 'NONE';
  /**
   * Authentication type for PAP.
   */
  public const AUTH_TYPE_PAP = 'PAP';
  /**
   * Authentication type for CHAP.
   */
  public const AUTH_TYPE_CHAP = 'CHAP';
  /**
   * Authentication type for PAP or CHAP.
   */
  public const AUTH_TYPE_PAP_OR_CHAP = 'PAP_OR_CHAP';
  /**
   * The MVNO type is not specified.
   */
  public const MVNO_TYPE_MVNO_TYPE_UNSPECIFIED = 'MVNO_TYPE_UNSPECIFIED';
  /**
   * MVNO type for group identifier level 1.
   */
  public const MVNO_TYPE_GID = 'GID';
  /**
   * MVNO type for ICCID.
   */
  public const MVNO_TYPE_ICCID = 'ICCID';
  /**
   * MVNO type for IMSI.
   */
  public const MVNO_TYPE_IMSI = 'IMSI';
  /**
   * MVNO type for SPN (service provider name).
   */
  public const MVNO_TYPE_SPN = 'SPN';
  /**
   * The protocol is not specified.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Internet protocol.
   */
  public const PROTOCOL_IP = 'IP';
  /**
   * Virtual PDP type introduced to handle dual IP stack UE capability.
   */
  public const PROTOCOL_IPV4V6 = 'IPV4V6';
  /**
   * Internet protocol, version 6.
   */
  public const PROTOCOL_IPV6 = 'IPV6';
  /**
   * Transfer of Non-IP data to external packet data network.
   */
  public const PROTOCOL_NON_IP = 'NON_IP';
  /**
   * Point to point protocol.
   */
  public const PROTOCOL_PPP = 'PPP';
  /**
   * Transfer of Unstructured data to the Data Network via N6.
   */
  public const PROTOCOL_UNSTRUCTURED = 'UNSTRUCTURED';
  /**
   * The protocol is not specified.
   */
  public const ROAMING_PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Internet protocol.
   */
  public const ROAMING_PROTOCOL_IP = 'IP';
  /**
   * Virtual PDP type introduced to handle dual IP stack UE capability.
   */
  public const ROAMING_PROTOCOL_IPV4V6 = 'IPV4V6';
  /**
   * Internet protocol, version 6.
   */
  public const ROAMING_PROTOCOL_IPV6 = 'IPV6';
  /**
   * Transfer of Non-IP data to external packet data network.
   */
  public const ROAMING_PROTOCOL_NON_IP = 'NON_IP';
  /**
   * Point to point protocol.
   */
  public const ROAMING_PROTOCOL_PPP = 'PPP';
  /**
   * Transfer of Unstructured data to the Data Network via N6.
   */
  public const ROAMING_PROTOCOL_UNSTRUCTURED = 'UNSTRUCTURED';
  protected $collection_key = 'networkTypes';
  /**
   * Optional. Whether User Plane resources have to be activated during every
   * transition from CM-IDLE mode to CM-CONNECTED state for this APN. See 3GPP
   * TS 23.501 section 5.6.13.
   *
   * @var string
   */
  public $alwaysOnSetting;
  /**
   * Required. Name of the APN. Policy will be rejected if this field is empty.
   *
   * @var string
   */
  public $apn;
  /**
   * Required. Usage categories for the APN. Policy will be rejected if this
   * field is empty or contains APN_TYPE_UNSPECIFIED or duplicates. Multiple APN
   * types can be set on fully managed devices. ENTERPRISE is the only allowed
   * APN type on work profiles. A NonComplianceDetail with MANAGEMENT_MODE is
   * reported for any other value on work profiles. APN types that are not
   * supported on the device or management mode will be ignored. If this results
   * in the empty list, the APN setting will be ignored, because apnTypes is a
   * required field. A NonComplianceDetail with INVALID_VALUE is reported if
   * none of the APN types are supported on the device or management mode.
   *
   * @var string[]
   */
  public $apnTypes;
  /**
   * Optional. Authentication type of the APN.
   *
   * @var string
   */
  public $authType;
  /**
   * Optional. Carrier ID for the APN. A value of 0 (default) means not set and
   * negative values are rejected.
   *
   * @var int
   */
  public $carrierId;
  /**
   * Required. Human-readable name that describes the APN. Policy will be
   * rejected if this field is empty.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. MMS (Multimedia Messaging Service) proxy address of the APN which
   * can be an IP address or hostname (not a URL).
   *
   * @var string
   */
  public $mmsProxyAddress;
  /**
   * Optional. MMS (Multimedia Messaging Service) proxy port of the APN. A value
   * of 0 (default) means not set and negative values are rejected.
   *
   * @var int
   */
  public $mmsProxyPort;
  /**
   * Optional. MMSC (Multimedia Messaging Service Center) URI of the APN.
   *
   * @var string
   */
  public $mmsc;
  /**
   * Optional. The default MTU (Maximum Transmission Unit) size in bytes of the
   * IPv4 routes brought up by this APN setting. A value of 0 (default) means
   * not set and negative values are rejected. Supported on Android 13 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 13.
   *
   * @var int
   */
  public $mtuV4;
  /**
   * Optional. The MTU (Maximum Transmission Unit) size of the IPv6 mobile
   * interface to which the APN connected. A value of 0 (default) means not set
   * and negative values are rejected. Supported on Android 13 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   *
   * @var int
   */
  public $mtuV6;
  /**
   * Optional. MVNO match type for the APN.
   *
   * @var string
   */
  public $mvnoType;
  /**
   * Optional. Radio technologies (network types) the APN may use. Policy will
   * be rejected if this field contains NETWORK_TYPE_UNSPECIFIED or duplicates.
   *
   * @var string[]
   */
  public $networkTypes;
  /**
   * Optional. The numeric operator ID of the APN. Numeric operator ID is
   * defined as MCC (Mobile Country Code) + MNC (Mobile Network Code).
   *
   * @var string
   */
  public $numericOperatorId;
  /**
   * Optional. APN password of the APN.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. The protocol to use to connect to this APN.
   *
   * @var string
   */
  public $protocol;
  /**
   * Optional. The proxy address of the APN.
   *
   * @var string
   */
  public $proxyAddress;
  /**
   * Optional. The proxy port of the APN. A value of 0 (default) means not set
   * and negative values are rejected.
   *
   * @var int
   */
  public $proxyPort;
  /**
   * Optional. The protocol to use to connect to this APN while the device is
   * roaming.
   *
   * @var string
   */
  public $roamingProtocol;
  /**
   * Optional. APN username of the APN.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. Whether User Plane resources have to be activated during every
   * transition from CM-IDLE mode to CM-CONNECTED state for this APN. See 3GPP
   * TS 23.501 section 5.6.13.
   *
   * Accepted values: ALWAYS_ON_SETTING_UNSPECIFIED, NOT_ALWAYS_ON, ALWAYS_ON
   *
   * @param self::ALWAYS_ON_SETTING_* $alwaysOnSetting
   */
  public function setAlwaysOnSetting($alwaysOnSetting)
  {
    $this->alwaysOnSetting = $alwaysOnSetting;
  }
  /**
   * @return self::ALWAYS_ON_SETTING_*
   */
  public function getAlwaysOnSetting()
  {
    return $this->alwaysOnSetting;
  }
  /**
   * Required. Name of the APN. Policy will be rejected if this field is empty.
   *
   * @param string $apn
   */
  public function setApn($apn)
  {
    $this->apn = $apn;
  }
  /**
   * @return string
   */
  public function getApn()
  {
    return $this->apn;
  }
  /**
   * Required. Usage categories for the APN. Policy will be rejected if this
   * field is empty or contains APN_TYPE_UNSPECIFIED or duplicates. Multiple APN
   * types can be set on fully managed devices. ENTERPRISE is the only allowed
   * APN type on work profiles. A NonComplianceDetail with MANAGEMENT_MODE is
   * reported for any other value on work profiles. APN types that are not
   * supported on the device or management mode will be ignored. If this results
   * in the empty list, the APN setting will be ignored, because apnTypes is a
   * required field. A NonComplianceDetail with INVALID_VALUE is reported if
   * none of the APN types are supported on the device or management mode.
   *
   * @param string[] $apnTypes
   */
  public function setApnTypes($apnTypes)
  {
    $this->apnTypes = $apnTypes;
  }
  /**
   * @return string[]
   */
  public function getApnTypes()
  {
    return $this->apnTypes;
  }
  /**
   * Optional. Authentication type of the APN.
   *
   * Accepted values: AUTH_TYPE_UNSPECIFIED, NONE, PAP, CHAP, PAP_OR_CHAP
   *
   * @param self::AUTH_TYPE_* $authType
   */
  public function setAuthType($authType)
  {
    $this->authType = $authType;
  }
  /**
   * @return self::AUTH_TYPE_*
   */
  public function getAuthType()
  {
    return $this->authType;
  }
  /**
   * Optional. Carrier ID for the APN. A value of 0 (default) means not set and
   * negative values are rejected.
   *
   * @param int $carrierId
   */
  public function setCarrierId($carrierId)
  {
    $this->carrierId = $carrierId;
  }
  /**
   * @return int
   */
  public function getCarrierId()
  {
    return $this->carrierId;
  }
  /**
   * Required. Human-readable name that describes the APN. Policy will be
   * rejected if this field is empty.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. MMS (Multimedia Messaging Service) proxy address of the APN which
   * can be an IP address or hostname (not a URL).
   *
   * @param string $mmsProxyAddress
   */
  public function setMmsProxyAddress($mmsProxyAddress)
  {
    $this->mmsProxyAddress = $mmsProxyAddress;
  }
  /**
   * @return string
   */
  public function getMmsProxyAddress()
  {
    return $this->mmsProxyAddress;
  }
  /**
   * Optional. MMS (Multimedia Messaging Service) proxy port of the APN. A value
   * of 0 (default) means not set and negative values are rejected.
   *
   * @param int $mmsProxyPort
   */
  public function setMmsProxyPort($mmsProxyPort)
  {
    $this->mmsProxyPort = $mmsProxyPort;
  }
  /**
   * @return int
   */
  public function getMmsProxyPort()
  {
    return $this->mmsProxyPort;
  }
  /**
   * Optional. MMSC (Multimedia Messaging Service Center) URI of the APN.
   *
   * @param string $mmsc
   */
  public function setMmsc($mmsc)
  {
    $this->mmsc = $mmsc;
  }
  /**
   * @return string
   */
  public function getMmsc()
  {
    return $this->mmsc;
  }
  /**
   * Optional. The default MTU (Maximum Transmission Unit) size in bytes of the
   * IPv4 routes brought up by this APN setting. A value of 0 (default) means
   * not set and negative values are rejected. Supported on Android 13 and
   * above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 13.
   *
   * @param int $mtuV4
   */
  public function setMtuV4($mtuV4)
  {
    $this->mtuV4 = $mtuV4;
  }
  /**
   * @return int
   */
  public function getMtuV4()
  {
    return $this->mtuV4;
  }
  /**
   * Optional. The MTU (Maximum Transmission Unit) size of the IPv6 mobile
   * interface to which the APN connected. A value of 0 (default) means not set
   * and negative values are rejected. Supported on Android 13 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 13.
   *
   * @param int $mtuV6
   */
  public function setMtuV6($mtuV6)
  {
    $this->mtuV6 = $mtuV6;
  }
  /**
   * @return int
   */
  public function getMtuV6()
  {
    return $this->mtuV6;
  }
  /**
   * Optional. MVNO match type for the APN.
   *
   * Accepted values: MVNO_TYPE_UNSPECIFIED, GID, ICCID, IMSI, SPN
   *
   * @param self::MVNO_TYPE_* $mvnoType
   */
  public function setMvnoType($mvnoType)
  {
    $this->mvnoType = $mvnoType;
  }
  /**
   * @return self::MVNO_TYPE_*
   */
  public function getMvnoType()
  {
    return $this->mvnoType;
  }
  /**
   * Optional. Radio technologies (network types) the APN may use. Policy will
   * be rejected if this field contains NETWORK_TYPE_UNSPECIFIED or duplicates.
   *
   * @param string[] $networkTypes
   */
  public function setNetworkTypes($networkTypes)
  {
    $this->networkTypes = $networkTypes;
  }
  /**
   * @return string[]
   */
  public function getNetworkTypes()
  {
    return $this->networkTypes;
  }
  /**
   * Optional. The numeric operator ID of the APN. Numeric operator ID is
   * defined as MCC (Mobile Country Code) + MNC (Mobile Network Code).
   *
   * @param string $numericOperatorId
   */
  public function setNumericOperatorId($numericOperatorId)
  {
    $this->numericOperatorId = $numericOperatorId;
  }
  /**
   * @return string
   */
  public function getNumericOperatorId()
  {
    return $this->numericOperatorId;
  }
  /**
   * Optional. APN password of the APN.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Optional. The protocol to use to connect to this APN.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, IP, IPV4V6, IPV6, NON_IP, PPP,
   * UNSTRUCTURED
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Optional. The proxy address of the APN.
   *
   * @param string $proxyAddress
   */
  public function setProxyAddress($proxyAddress)
  {
    $this->proxyAddress = $proxyAddress;
  }
  /**
   * @return string
   */
  public function getProxyAddress()
  {
    return $this->proxyAddress;
  }
  /**
   * Optional. The proxy port of the APN. A value of 0 (default) means not set
   * and negative values are rejected.
   *
   * @param int $proxyPort
   */
  public function setProxyPort($proxyPort)
  {
    $this->proxyPort = $proxyPort;
  }
  /**
   * @return int
   */
  public function getProxyPort()
  {
    return $this->proxyPort;
  }
  /**
   * Optional. The protocol to use to connect to this APN while the device is
   * roaming.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, IP, IPV4V6, IPV6, NON_IP, PPP,
   * UNSTRUCTURED
   *
   * @param self::ROAMING_PROTOCOL_* $roamingProtocol
   */
  public function setRoamingProtocol($roamingProtocol)
  {
    $this->roamingProtocol = $roamingProtocol;
  }
  /**
   * @return self::ROAMING_PROTOCOL_*
   */
  public function getRoamingProtocol()
  {
    return $this->roamingProtocol;
  }
  /**
   * Optional. APN username of the APN.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApnSetting::class, 'Google_Service_AndroidManagement_ApnSetting');
