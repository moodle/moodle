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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Plugin extends \Google\Collection
{
  /**
   * The gateway type is not specified.
   */
  public const GATEWAY_TYPE_GATEWAY_TYPE_UNSPECIFIED = 'GATEWAY_TYPE_UNSPECIFIED';
  /**
   * The gateway type is Apigee X and Hybrid.
   */
  public const GATEWAY_TYPE_APIGEE_X_AND_HYBRID = 'APIGEE_X_AND_HYBRID';
  /**
   * The gateway type is Apigee Edge Public Cloud.
   */
  public const GATEWAY_TYPE_APIGEE_EDGE_PUBLIC_CLOUD = 'APIGEE_EDGE_PUBLIC_CLOUD';
  /**
   * The gateway type is Apigee Edge Private Cloud.
   */
  public const GATEWAY_TYPE_APIGEE_EDGE_PRIVATE_CLOUD = 'APIGEE_EDGE_PRIVATE_CLOUD';
  /**
   * The gateway type is Cloud API Gateway.
   */
  public const GATEWAY_TYPE_CLOUD_API_GATEWAY = 'CLOUD_API_GATEWAY';
  /**
   * The gateway type is Cloud Endpoints.
   */
  public const GATEWAY_TYPE_CLOUD_ENDPOINTS = 'CLOUD_ENDPOINTS';
  /**
   * The gateway type is API Discovery.
   */
  public const GATEWAY_TYPE_API_DISCOVERY = 'API_DISCOVERY';
  /**
   * The gateway type for any other types of gateways.
   */
  public const GATEWAY_TYPE_OTHERS = 'OTHERS';
  /**
   * Default unspecified type.
   */
  public const OWNERSHIP_TYPE_OWNERSHIP_TYPE_UNSPECIFIED = 'OWNERSHIP_TYPE_UNSPECIFIED';
  /**
   * System owned plugins are defined by API hub and are available out of the
   * box in API hub.
   */
  public const OWNERSHIP_TYPE_SYSTEM_OWNED = 'SYSTEM_OWNED';
  /**
   * User owned plugins are defined by the user and need to be explicitly added
   * to API hub via CreatePlugin method.
   */
  public const OWNERSHIP_TYPE_USER_OWNED = 'USER_OWNED';
  /**
   * Default unspecified plugin type.
   */
  public const PLUGIN_CATEGORY_PLUGIN_CATEGORY_UNSPECIFIED = 'PLUGIN_CATEGORY_UNSPECIFIED';
  /**
   * API_GATEWAY plugins represent plugins built for API Gateways like Apigee.
   */
  public const PLUGIN_CATEGORY_API_GATEWAY = 'API_GATEWAY';
  /**
   * API_PRODUCER plugins represent plugins built for API Producers like Cloud
   * Run, Application Integration etc.
   */
  public const PLUGIN_CATEGORY_API_PRODUCER = 'API_PRODUCER';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The plugin is enabled.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The plugin is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  protected $collection_key = 'actionsConfig';
  protected $actionsConfigType = GoogleCloudApihubV1PluginActionConfig::class;
  protected $actionsConfigDataType = 'array';
  protected $configTemplateType = GoogleCloudApihubV1ConfigTemplate::class;
  protected $configTemplateDataType = '';
  /**
   * Output only. Timestamp indicating when the plugin was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The plugin description. Max length is 2000 characters (Unicode
   * code points).
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the plugin. Max length is 50 characters
   * (Unicode code points).
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  /**
   * Optional. The type of the gateway.
   *
   * @var string
   */
  public $gatewayType;
  protected $hostingServiceType = GoogleCloudApihubV1HostingService::class;
  protected $hostingServiceDataType = '';
  /**
   * Identifier. The name of the plugin. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The type of the plugin, indicating whether it is
   * 'SYSTEM_OWNED' or 'USER_OWNED'.
   *
   * @var string
   */
  public $ownershipType;
  /**
   * Optional. The category of the plugin, identifying its primary category or
   * purpose. This field is required for all plugins.
   *
   * @var string
   */
  public $pluginCategory;
  /**
   * Output only. Represents the state of the plugin. Note this field will not
   * be set for plugins developed via plugin framework as the state will be
   * managed at plugin instance level.
   *
   * @var string
   */
  public $state;
  protected $typeType = GoogleCloudApihubV1AttributeValues::class;
  protected $typeDataType = '';
  /**
   * Output only. Timestamp indicating when the plugin was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The configuration of actions supported by the plugin.
   * **REQUIRED**: This field must be provided when creating or updating a
   * Plugin. The server will reject requests if this field is missing.
   *
   * @param GoogleCloudApihubV1PluginActionConfig[] $actionsConfig
   */
  public function setActionsConfig($actionsConfig)
  {
    $this->actionsConfig = $actionsConfig;
  }
  /**
   * @return GoogleCloudApihubV1PluginActionConfig[]
   */
  public function getActionsConfig()
  {
    return $this->actionsConfig;
  }
  /**
   * Optional. The configuration template for the plugin.
   *
   * @param GoogleCloudApihubV1ConfigTemplate $configTemplate
   */
  public function setConfigTemplate(GoogleCloudApihubV1ConfigTemplate $configTemplate)
  {
    $this->configTemplate = $configTemplate;
  }
  /**
   * @return GoogleCloudApihubV1ConfigTemplate
   */
  public function getConfigTemplate()
  {
    return $this->configTemplate;
  }
  /**
   * Output only. Timestamp indicating when the plugin was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The plugin description. Max length is 2000 characters (Unicode
   * code points).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the plugin. Max length is 50 characters
   * (Unicode code points).
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
   * Optional. The documentation of the plugin, that explains how to set up and
   * use the plugin.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. The type of the gateway.
   *
   * Accepted values: GATEWAY_TYPE_UNSPECIFIED, APIGEE_X_AND_HYBRID,
   * APIGEE_EDGE_PUBLIC_CLOUD, APIGEE_EDGE_PRIVATE_CLOUD, CLOUD_API_GATEWAY,
   * CLOUD_ENDPOINTS, API_DISCOVERY, OTHERS
   *
   * @param self::GATEWAY_TYPE_* $gatewayType
   */
  public function setGatewayType($gatewayType)
  {
    $this->gatewayType = $gatewayType;
  }
  /**
   * @return self::GATEWAY_TYPE_*
   */
  public function getGatewayType()
  {
    return $this->gatewayType;
  }
  /**
   * Optional. This field is optional. It is used to notify the plugin hosting
   * service for any lifecycle changes of the plugin instance and trigger
   * execution of plugin instance actions in case of API hub managed actions.
   * This field should be provided if the plugin instance lifecycle of the
   * developed plugin needs to be managed from API hub. Also, in this case the
   * plugin hosting service interface needs to be implemented. This field should
   * not be provided if the plugin wants to manage plugin instance lifecycle
   * events outside of hub interface and use plugin framework for only
   * registering of plugin and plugin instances to capture the source of data
   * into hub. Note, in this case the plugin hosting service interface is not
   * required to be implemented. Also, the plugin instance lifecycle actions
   * will be disabled from API hub's UI.
   *
   * @param GoogleCloudApihubV1HostingService $hostingService
   */
  public function setHostingService(GoogleCloudApihubV1HostingService $hostingService)
  {
    $this->hostingService = $hostingService;
  }
  /**
   * @return GoogleCloudApihubV1HostingService
   */
  public function getHostingService()
  {
    return $this->hostingService;
  }
  /**
   * Identifier. The name of the plugin. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}`
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
   * Output only. The type of the plugin, indicating whether it is
   * 'SYSTEM_OWNED' or 'USER_OWNED'.
   *
   * Accepted values: OWNERSHIP_TYPE_UNSPECIFIED, SYSTEM_OWNED, USER_OWNED
   *
   * @param self::OWNERSHIP_TYPE_* $ownershipType
   */
  public function setOwnershipType($ownershipType)
  {
    $this->ownershipType = $ownershipType;
  }
  /**
   * @return self::OWNERSHIP_TYPE_*
   */
  public function getOwnershipType()
  {
    return $this->ownershipType;
  }
  /**
   * Optional. The category of the plugin, identifying its primary category or
   * purpose. This field is required for all plugins.
   *
   * Accepted values: PLUGIN_CATEGORY_UNSPECIFIED, API_GATEWAY, API_PRODUCER
   *
   * @param self::PLUGIN_CATEGORY_* $pluginCategory
   */
  public function setPluginCategory($pluginCategory)
  {
    $this->pluginCategory = $pluginCategory;
  }
  /**
   * @return self::PLUGIN_CATEGORY_*
   */
  public function getPluginCategory()
  {
    return $this->pluginCategory;
  }
  /**
   * Output only. Represents the state of the plugin. Note this field will not
   * be set for plugins developed via plugin framework as the state will be
   * managed at plugin instance level.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. The type of the API. This maps to the following system defined
   * attribute: `projects/{project}/locations/{location}/attributes/system-
   * plugin-type` attribute. The number of allowed values for this attribute
   * will be based on the cardinality of the attribute. The same can be
   * retrieved via GetAttribute API. All values should be from the list of
   * allowed values defined for the attribute. Note this field is not required
   * for plugins developed via plugin framework.
   *
   * @param GoogleCloudApihubV1AttributeValues $type
   */
  public function setType(GoogleCloudApihubV1AttributeValues $type)
  {
    $this->type = $type;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Timestamp indicating when the plugin was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Plugin::class, 'Google_Service_APIhub_GoogleCloudApihubV1Plugin');
