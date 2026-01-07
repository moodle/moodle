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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Environment extends \Google\Model
{
  /**
   * API proxy type not specified.
   */
  public const API_PROXY_TYPE_API_PROXY_TYPE_UNSPECIFIED = 'API_PROXY_TYPE_UNSPECIFIED';
  /**
   * Programmable API Proxies enable you to develop APIs with highly flexible
   * behavior using bundled policy configuration and one or more programming
   * languages to describe complex sequential and/or conditional flows of logic.
   */
  public const API_PROXY_TYPE_PROGRAMMABLE = 'PROGRAMMABLE';
  /**
   * Configurable API Proxies enable you to develop efficient APIs using simple
   * configuration while complex execution control flow logic is handled by
   * Apigee. This type only works with the ARCHIVE deployment type and cannot be
   * combined with the PROXY deployment type.
   */
  public const API_PROXY_TYPE_CONFIGURABLE = 'CONFIGURABLE';
  /**
   * Deployment type not specified.
   */
  public const DEPLOYMENT_TYPE_DEPLOYMENT_TYPE_UNSPECIFIED = 'DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * Proxy deployment enables you to develop and deploy API proxies using Apigee
   * on Google Cloud. This cannot currently be combined with the CONFIGURABLE
   * API proxy type.
   */
  public const DEPLOYMENT_TYPE_PROXY = 'PROXY';
  /**
   * Archive deployment enables you to develop API proxies locally then deploy
   * an archive of your API proxy configuration to an environment in Apigee on
   * Google Cloud. You will be prevented from performing a [subset of
   * actions](/apigee/docs/api-platform/local-development/overview#prevented-
   * actions) within the environment.
   */
  public const DEPLOYMENT_TYPE_ARCHIVE = 'ARCHIVE';
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Environment type not specified.
   */
  public const TYPE_ENVIRONMENT_TYPE_UNSPECIFIED = 'ENVIRONMENT_TYPE_UNSPECIFIED';
  /**
   * This is the default type. Base environment has limited capacity and
   * capabilities and are usually used when you are getting started with Apigee
   * or while experimenting. Refer to Apigee's public documentation for more
   * details.
   */
  public const TYPE_BASE = 'BASE';
  /**
   * Intermediate environment supports API management features and higher
   * capacity than Base environment. Refer to Apigee's public documentation for
   * more details.
   */
  public const TYPE_INTERMEDIATE = 'INTERMEDIATE';
  /**
   * Comprehensive environment supports advanced capabilites and even higher
   * capacity than Intermediate environment. Refer to Apigee's public
   * documentation for more details.
   */
  public const TYPE_COMPREHENSIVE = 'COMPREHENSIVE';
  /**
   * Optional. API Proxy type supported by the environment. The type can be set
   * when creating the Environment and cannot be changed.
   *
   * @var string
   */
  public $apiProxyType;
  protected $clientIpResolutionConfigType = GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig::class;
  protected $clientIpResolutionConfigDataType = '';
  /**
   * Output only. Creation time of this environment as milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Optional. Deployment type supported by the environment. The deployment type
   * can be set when creating the environment and cannot be changed. When you
   * enable archive deployment, you will be **prevented from performing** a
   * [subset of actions](/apigee/docs/api-platform/local-
   * development/overview#prevented-actions) within the environment, including:
   * * Managing the deployment of API proxy or shared flow revisions * Creating,
   * updating, or deleting resource files * Creating, updating, or deleting
   * target servers
   *
   * @var string
   */
  public $deploymentType;
  /**
   * Optional. Description of the environment.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Display name for this environment.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. URI of the forward proxy to be applied to the runtime instances
   * in this environment. Must be in the format of {scheme}://{hostname}:{port}.
   * Note that the only supported scheme is "http". The port must be supplied.
   * To remove a forward proxy setting, update the field to an empty value.
   * Note: At this time, PUT operations to add forwardProxyUri to an existing
   * environment fail if the environment has nodeConfig set up. To successfully
   * add the forwardProxyUri setting in this case, include the NodeConfig
   * details with the request.
   *
   * @var string
   */
  public $forwardProxyUri;
  /**
   * @var bool
   */
  public $hasAttachedFlowHooks;
  /**
   * Output only. Last modification time of this environment as milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Required. Name of the environment. Values must match the regular expression
   * `^[.\\p{Alnum}-_]{1,255}$`
   *
   * @var string
   */
  public $name;
  protected $nodeConfigType = GoogleCloudApigeeV1NodeConfig::class;
  protected $nodeConfigDataType = '';
  protected $propertiesType = GoogleCloudApigeeV1Properties::class;
  protected $propertiesDataType = '';
  /**
   * Output only. State of the environment. Values other than ACTIVE means the
   * resource is not ready to use.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. EnvironmentType selected for the environment.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. API Proxy type supported by the environment. The type can be set
   * when creating the Environment and cannot be changed.
   *
   * Accepted values: API_PROXY_TYPE_UNSPECIFIED, PROGRAMMABLE, CONFIGURABLE
   *
   * @param self::API_PROXY_TYPE_* $apiProxyType
   */
  public function setApiProxyType($apiProxyType)
  {
    $this->apiProxyType = $apiProxyType;
  }
  /**
   * @return self::API_PROXY_TYPE_*
   */
  public function getApiProxyType()
  {
    return $this->apiProxyType;
  }
  /**
   * Optional. The algorithm to resolve IP. This will affect Analytics, API
   * Security, and other features that use the client ip. To remove a client ip
   * resolution config, update the field to an empty value. Example: '{
   * "clientIpResolutionConfig" = {} }' For more information, see:
   * https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/client-ip-resolution.
   *
   * @param GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig $clientIpResolutionConfig
   */
  public function setClientIpResolutionConfig(GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig $clientIpResolutionConfig)
  {
    $this->clientIpResolutionConfig = $clientIpResolutionConfig;
  }
  /**
   * @return GoogleCloudApigeeV1EnvironmentClientIPResolutionConfig
   */
  public function getClientIpResolutionConfig()
  {
    return $this->clientIpResolutionConfig;
  }
  /**
   * Output only. Creation time of this environment as milliseconds since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Optional. Deployment type supported by the environment. The deployment type
   * can be set when creating the environment and cannot be changed. When you
   * enable archive deployment, you will be **prevented from performing** a
   * [subset of actions](/apigee/docs/api-platform/local-
   * development/overview#prevented-actions) within the environment, including:
   * * Managing the deployment of API proxy or shared flow revisions * Creating,
   * updating, or deleting resource files * Creating, updating, or deleting
   * target servers
   *
   * Accepted values: DEPLOYMENT_TYPE_UNSPECIFIED, PROXY, ARCHIVE
   *
   * @param self::DEPLOYMENT_TYPE_* $deploymentType
   */
  public function setDeploymentType($deploymentType)
  {
    $this->deploymentType = $deploymentType;
  }
  /**
   * @return self::DEPLOYMENT_TYPE_*
   */
  public function getDeploymentType()
  {
    return $this->deploymentType;
  }
  /**
   * Optional. Description of the environment.
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
   * Optional. Display name for this environment.
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
   * Optional. URI of the forward proxy to be applied to the runtime instances
   * in this environment. Must be in the format of {scheme}://{hostname}:{port}.
   * Note that the only supported scheme is "http". The port must be supplied.
   * To remove a forward proxy setting, update the field to an empty value.
   * Note: At this time, PUT operations to add forwardProxyUri to an existing
   * environment fail if the environment has nodeConfig set up. To successfully
   * add the forwardProxyUri setting in this case, include the NodeConfig
   * details with the request.
   *
   * @param string $forwardProxyUri
   */
  public function setForwardProxyUri($forwardProxyUri)
  {
    $this->forwardProxyUri = $forwardProxyUri;
  }
  /**
   * @return string
   */
  public function getForwardProxyUri()
  {
    return $this->forwardProxyUri;
  }
  /**
   * @param bool $hasAttachedFlowHooks
   */
  public function setHasAttachedFlowHooks($hasAttachedFlowHooks)
  {
    $this->hasAttachedFlowHooks = $hasAttachedFlowHooks;
  }
  /**
   * @return bool
   */
  public function getHasAttachedFlowHooks()
  {
    return $this->hasAttachedFlowHooks;
  }
  /**
   * Output only. Last modification time of this environment as milliseconds
   * since epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Required. Name of the environment. Values must match the regular expression
   * `^[.\\p{Alnum}-_]{1,255}$`
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
   * Optional. NodeConfig of the environment.
   *
   * @param GoogleCloudApigeeV1NodeConfig $nodeConfig
   */
  public function setNodeConfig(GoogleCloudApigeeV1NodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return GoogleCloudApigeeV1NodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * Optional. Key-value pairs that may be used for customizing the environment.
   *
   * @param GoogleCloudApigeeV1Properties $properties
   */
  public function setProperties(GoogleCloudApigeeV1Properties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudApigeeV1Properties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. State of the environment. Values other than ACTIVE means the
   * resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
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
   * Optional. EnvironmentType selected for the environment.
   *
   * Accepted values: ENVIRONMENT_TYPE_UNSPECIFIED, BASE, INTERMEDIATE,
   * COMPREHENSIVE
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
class_alias(GoogleCloudApigeeV1Environment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Environment');
