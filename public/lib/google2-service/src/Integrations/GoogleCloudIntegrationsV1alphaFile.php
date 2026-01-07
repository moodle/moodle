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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaFile extends \Google\Model
{
  /**
   * Default value.
   */
  public const TYPE_INTEGRATION_FILE_UNSPECIFIED = 'INTEGRATION_FILE_UNSPECIFIED';
  /**
   * Integration file.
   */
  public const TYPE_INTEGRATION = 'INTEGRATION';
  /**
   * Integration Config variables.
   */
  public const TYPE_INTEGRATION_CONFIG_VARIABLES = 'INTEGRATION_CONFIG_VARIABLES';
  /**
   * Integration version config file
   *
   * @var array[]
   */
  public $integrationConfig;
  protected $integrationVersionType = GoogleCloudIntegrationsV1alphaIntegrationVersion::class;
  protected $integrationVersionDataType = '';
  /**
   * File information like Integration version, Integration Config variables
   * etc.
   *
   * @var string
   */
  public $type;

  /**
   * Integration version config file
   *
   * @param array[] $integrationConfig
   */
  public function setIntegrationConfig($integrationConfig)
  {
    $this->integrationConfig = $integrationConfig;
  }
  /**
   * @return array[]
   */
  public function getIntegrationConfig()
  {
    return $this->integrationConfig;
  }
  /**
   * Integration version
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersion $integrationVersion
   */
  public function setIntegrationVersion(GoogleCloudIntegrationsV1alphaIntegrationVersion $integrationVersion)
  {
    $this->integrationVersion = $integrationVersion;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion
   */
  public function getIntegrationVersion()
  {
    return $this->integrationVersion;
  }
  /**
   * File information like Integration version, Integration Config variables
   * etc.
   *
   * Accepted values: INTEGRATION_FILE_UNSPECIFIED, INTEGRATION,
   * INTEGRATION_CONFIG_VARIABLES
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaFile::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaFile');
