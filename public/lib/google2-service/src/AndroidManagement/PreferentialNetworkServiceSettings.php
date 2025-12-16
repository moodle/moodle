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

class PreferentialNetworkServiceSettings extends \Google\Collection
{
  /**
   * Whether this value is valid and what it means depends on where it is used,
   * and this is documented on the relevant fields.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_UNSPECIFIED = 'PREFERENTIAL_NETWORK_ID_UNSPECIFIED';
  /**
   * Application does not use any preferential network.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_NO_PREFERENTIAL_NETWORK = 'NO_PREFERENTIAL_NETWORK';
  /**
   * Preferential network identifier 1.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_ONE = 'PREFERENTIAL_NETWORK_ID_ONE';
  /**
   * Preferential network identifier 2.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_TWO = 'PREFERENTIAL_NETWORK_ID_TWO';
  /**
   * Preferential network identifier 3.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_THREE = 'PREFERENTIAL_NETWORK_ID_THREE';
  /**
   * Preferential network identifier 4.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FOUR = 'PREFERENTIAL_NETWORK_ID_FOUR';
  /**
   * Preferential network identifier 5.
   */
  public const DEFAULT_PREFERENTIAL_NETWORK_ID_PREFERENTIAL_NETWORK_ID_FIVE = 'PREFERENTIAL_NETWORK_ID_FIVE';
  protected $collection_key = 'preferentialNetworkServiceConfigs';
  /**
   * Required. Default preferential network ID for the applications that are not
   * in applications or if ApplicationPolicy.preferentialNetworkId is set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED. There must be a configuration for the
   * specified network ID in preferentialNetworkServiceConfigs, unless this is
   * set to NO_PREFERENTIAL_NETWORK. If set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED or unset, this defaults to
   * NO_PREFERENTIAL_NETWORK. Note: If the default preferential network is
   * misconfigured, applications with no ApplicationPolicy.preferentialNetworkId
   * set are not able to access the internet. This setting does not apply to the
   * following critical apps: com.google.android.apps.work.clouddpc
   * com.google.android.gmsApplicationPolicy.preferentialNetworkId can still be
   * used to configure the preferential network for them.
   *
   * @var string
   */
  public $defaultPreferentialNetworkId;
  protected $preferentialNetworkServiceConfigsType = PreferentialNetworkServiceConfig::class;
  protected $preferentialNetworkServiceConfigsDataType = 'array';

  /**
   * Required. Default preferential network ID for the applications that are not
   * in applications or if ApplicationPolicy.preferentialNetworkId is set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED. There must be a configuration for the
   * specified network ID in preferentialNetworkServiceConfigs, unless this is
   * set to NO_PREFERENTIAL_NETWORK. If set to
   * PREFERENTIAL_NETWORK_ID_UNSPECIFIED or unset, this defaults to
   * NO_PREFERENTIAL_NETWORK. Note: If the default preferential network is
   * misconfigured, applications with no ApplicationPolicy.preferentialNetworkId
   * set are not able to access the internet. This setting does not apply to the
   * following critical apps: com.google.android.apps.work.clouddpc
   * com.google.android.gmsApplicationPolicy.preferentialNetworkId can still be
   * used to configure the preferential network for them.
   *
   * Accepted values: PREFERENTIAL_NETWORK_ID_UNSPECIFIED,
   * NO_PREFERENTIAL_NETWORK, PREFERENTIAL_NETWORK_ID_ONE,
   * PREFERENTIAL_NETWORK_ID_TWO, PREFERENTIAL_NETWORK_ID_THREE,
   * PREFERENTIAL_NETWORK_ID_FOUR, PREFERENTIAL_NETWORK_ID_FIVE
   *
   * @param self::DEFAULT_PREFERENTIAL_NETWORK_ID_* $defaultPreferentialNetworkId
   */
  public function setDefaultPreferentialNetworkId($defaultPreferentialNetworkId)
  {
    $this->defaultPreferentialNetworkId = $defaultPreferentialNetworkId;
  }
  /**
   * @return self::DEFAULT_PREFERENTIAL_NETWORK_ID_*
   */
  public function getDefaultPreferentialNetworkId()
  {
    return $this->defaultPreferentialNetworkId;
  }
  /**
   * Required. Preferential network service configurations which enables having
   * multiple enterprise slices. There must not be multiple configurations with
   * the same preferentialNetworkId. If a configuration is not referenced by any
   * application by setting ApplicationPolicy.preferentialNetworkId or by
   * setting defaultPreferentialNetworkId, it will be ignored. For devices on 4G
   * networks, enterprise APN needs to be configured additionally to set up data
   * call for preferential network service. These APNs can be added using
   * apnPolicy.
   *
   * @param PreferentialNetworkServiceConfig[] $preferentialNetworkServiceConfigs
   */
  public function setPreferentialNetworkServiceConfigs($preferentialNetworkServiceConfigs)
  {
    $this->preferentialNetworkServiceConfigs = $preferentialNetworkServiceConfigs;
  }
  /**
   * @return PreferentialNetworkServiceConfig[]
   */
  public function getPreferentialNetworkServiceConfigs()
  {
    return $this->preferentialNetworkServiceConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreferentialNetworkServiceSettings::class, 'Google_Service_AndroidManagement_PreferentialNetworkServiceSettings');
