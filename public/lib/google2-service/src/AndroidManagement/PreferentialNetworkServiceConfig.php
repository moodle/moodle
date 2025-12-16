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

class PreferentialNetworkServiceConfig extends \Google\Model
{
  /**
   * Unspecified. Defaults to FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED.
   */
  public const FALLBACK_TO_DEFAULT_CONNECTION_FALLBACK_TO_DEFAULT_CONNECTION_UNSPECIFIED = 'FALLBACK_TO_DEFAULT_CONNECTION_UNSPECIFIED';
  /**
   * Fallback to default connection is allowed. If this is set,
   * nonMatchingNetworks must not be set to NON_MATCHING_NETWORKS_DISALLOWED,
   * the policy will be rejected otherwise.
   */
  public const FALLBACK_TO_DEFAULT_CONNECTION_FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED = 'FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED';
  /**
   * Fallback to default connection is not allowed.
   */
  public const FALLBACK_TO_DEFAULT_CONNECTION_FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED = 'FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED';
  /**
   * Unspecified. Defaults to NON_MATCHING_NETWORKS_ALLOWED.
   */
  public const NON_MATCHING_NETWORKS_NON_MATCHING_NETWORKS_UNSPECIFIED = 'NON_MATCHING_NETWORKS_UNSPECIFIED';
  /**
   * Apps this configuration applies to are allowed to use networks other than
   * the preferential service.
   */
  public const NON_MATCHING_NETWORKS_NON_MATCHING_NETWORKS_ALLOWED = 'NON_MATCHING_NETWORKS_ALLOWED';
  /**
   * Apps this configuration applies to are disallowed from using other networks
   * than the preferential service. This can be set on Android 14 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 14. If this is set, fallbackToDefaultConnection must be set to
   * FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED, the policy will be rejected
   * otherwise.
   */
  public const NON_MATCHING_NETWORKS_NON_MATCHING_NETWORKS_DISALLOWED = 'NON_MATCHING_NETWORKS_DISALLOWED';
  /**
   * Whether this value is valid and what it means depends on where it is used,
   * and this is documented on the relevant fields.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_UNSPECIFIED = 'PREFERENTIAL_NETWORK_ID_UNSPECIFIED';
  /**
   * Application does not use any preferential network.
   */
  public const PREFERENTIAL_NETWORK_ID_NO_PREFERENTIAL_NETWORK = 'NO_PREFERENTIAL_NETWORK';
  /**
   * Preferential network identifier 1.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_ONE = 'PREFERENTIAL_NETWORK_ID_ONE';
  /**
   * Preferential network identifier 2.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_TWO = 'PREFERENTIAL_NETWORK_ID_TWO';
  /**
   * Preferential network identifier 3.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_THREE = 'PREFERENTIAL_NETWORK_ID_THREE';
  /**
   * Preferential network identifier 4.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FOUR = 'PREFERENTIAL_NETWORK_ID_FOUR';
  /**
   * Preferential network identifier 5.
   */
  public const PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FIVE = 'PREFERENTIAL_NETWORK_ID_FIVE';
  /**
   * Optional. Whether fallback to the device-wide default network is allowed.
   * If this is set to FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED, then
   * nonMatchingNetworks must not be set to NON_MATCHING_NETWORKS_DISALLOWED,
   * the policy will be rejected otherwise. Note: If this is set to
   * FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED, applications are not able to
   * access the internet if the 5G slice is not available.
   *
   * @var string
   */
  public $fallbackToDefaultConnection;
  /**
   * Optional. Whether apps this configuration applies to are blocked from using
   * networks other than the preferential service. If this is set to
   * NON_MATCHING_NETWORKS_DISALLOWED, then fallbackToDefaultConnection must be
   * set to FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED.
   *
   * @var string
   */
  public $nonMatchingNetworks;
  /**
   * Required. Preferential network identifier. This must not be set to
   * NO_PREFERENTIAL_NETWORK or PREFERENTIAL_NETWORK_ID_UNSPECIFIED, the policy
   * will be rejected otherwise.
   *
   * @var string
   */
  public $preferentialNetworkId;

  /**
   * Optional. Whether fallback to the device-wide default network is allowed.
   * If this is set to FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED, then
   * nonMatchingNetworks must not be set to NON_MATCHING_NETWORKS_DISALLOWED,
   * the policy will be rejected otherwise. Note: If this is set to
   * FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED, applications are not able to
   * access the internet if the 5G slice is not available.
   *
   * Accepted values: FALLBACK_TO_DEFAULT_CONNECTION_UNSPECIFIED,
   * FALLBACK_TO_DEFAULT_CONNECTION_ALLOWED,
   * FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED
   *
   * @param self::FALLBACK_TO_DEFAULT_CONNECTION_* $fallbackToDefaultConnection
   */
  public function setFallbackToDefaultConnection($fallbackToDefaultConnection)
  {
    $this->fallbackToDefaultConnection = $fallbackToDefaultConnection;
  }
  /**
   * @return self::FALLBACK_TO_DEFAULT_CONNECTION_*
   */
  public function getFallbackToDefaultConnection()
  {
    return $this->fallbackToDefaultConnection;
  }
  /**
   * Optional. Whether apps this configuration applies to are blocked from using
   * networks other than the preferential service. If this is set to
   * NON_MATCHING_NETWORKS_DISALLOWED, then fallbackToDefaultConnection must be
   * set to FALLBACK_TO_DEFAULT_CONNECTION_DISALLOWED.
   *
   * Accepted values: NON_MATCHING_NETWORKS_UNSPECIFIED,
   * NON_MATCHING_NETWORKS_ALLOWED, NON_MATCHING_NETWORKS_DISALLOWED
   *
   * @param self::NON_MATCHING_NETWORKS_* $nonMatchingNetworks
   */
  public function setNonMatchingNetworks($nonMatchingNetworks)
  {
    $this->nonMatchingNetworks = $nonMatchingNetworks;
  }
  /**
   * @return self::NON_MATCHING_NETWORKS_*
   */
  public function getNonMatchingNetworks()
  {
    return $this->nonMatchingNetworks;
  }
  /**
   * Required. Preferential network identifier. This must not be set to
   * NO_PREFERENTIAL_NETWORK or PREFERENTIAL_NETWORK_ID_UNSPECIFIED, the policy
   * will be rejected otherwise.
   *
   * Accepted values: PREFERENTIAL_NETWORK_ID_UNSPECIFIED,
   * NO_PREFERENTIAL_NETWORK, PREFERENTIAL_NETWORK_ID_ONE,
   * PREFERENTIAL_NETWORK_ID_TWO, PREFERENTIAL_NETWORK_ID_THREE,
   * PREFERENTIAL_NETWORK_ID_FOUR, PREFERENTIAL_NETWORK_ID_FIVE
   *
   * @param self::PREFERENTIAL_NETWORK_ID_* $preferentialNetworkId
   */
  public function setPreferentialNetworkId($preferentialNetworkId)
  {
    $this->preferentialNetworkId = $preferentialNetworkId;
  }
  /**
   * @return self::PREFERENTIAL_NETWORK_ID_*
   */
  public function getPreferentialNetworkId()
  {
    return $this->preferentialNetworkId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreferentialNetworkServiceConfig::class, 'Google_Service_AndroidManagement_PreferentialNetworkServiceConfig');
