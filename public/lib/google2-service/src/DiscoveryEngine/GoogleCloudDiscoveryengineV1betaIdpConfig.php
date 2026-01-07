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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaIdpConfig extends \Google\Model
{
  /**
   * Default value. ACL search not enabled.
   */
  public const IDP_TYPE_IDP_TYPE_UNSPECIFIED = 'IDP_TYPE_UNSPECIFIED';
  /**
   * Google 1P provider.
   */
  public const IDP_TYPE_GSUITE = 'GSUITE';
  /**
   * Third party provider.
   */
  public const IDP_TYPE_THIRD_PARTY = 'THIRD_PARTY';
  protected $externalIdpConfigType = GoogleCloudDiscoveryengineV1betaIdpConfigExternalIdpConfig::class;
  protected $externalIdpConfigDataType = '';
  /**
   * Identity provider type configured.
   *
   * @var string
   */
  public $idpType;

  /**
   * External Identity provider config.
   *
   * @param GoogleCloudDiscoveryengineV1betaIdpConfigExternalIdpConfig $externalIdpConfig
   */
  public function setExternalIdpConfig(GoogleCloudDiscoveryengineV1betaIdpConfigExternalIdpConfig $externalIdpConfig)
  {
    $this->externalIdpConfig = $externalIdpConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaIdpConfigExternalIdpConfig
   */
  public function getExternalIdpConfig()
  {
    return $this->externalIdpConfig;
  }
  /**
   * Identity provider type configured.
   *
   * Accepted values: IDP_TYPE_UNSPECIFIED, GSUITE, THIRD_PARTY
   *
   * @param self::IDP_TYPE_* $idpType
   */
  public function setIdpType($idpType)
  {
    $this->idpType = $idpType;
  }
  /**
   * @return self::IDP_TYPE_*
   */
  public function getIdpType()
  {
    return $this->idpType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaIdpConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaIdpConfig');
