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

class GoogleCloudApigeeV1EnvironmentConfig extends \Google\Collection
{
  protected $collection_key = 'targets';
  protected $addonsConfigType = GoogleCloudApigeeV1RuntimeAddonsConfig::class;
  protected $addonsConfigDataType = '';
  /**
   * The location for the config blob of API Runtime Control, aka Envoy Adapter,
   * for op-based authentication as a URI, e.g. a Cloud Storage URI. This is
   * only used by Envoy-based gateways.
   *
   * @var string
   */
  public $arcConfigLocation;
  protected $clientIpResolutionConfigType = GoogleCloudApigeeV1EnvironmentConfigClientIPResolutionConfig::class;
  protected $clientIpResolutionConfigDataType = '';
  /**
   * Time that the environment configuration was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataCollectorsType = GoogleCloudApigeeV1DataCollectorConfig::class;
  protected $dataCollectorsDataType = 'array';
  protected $debugMaskType = GoogleCloudApigeeV1DebugMask::class;
  protected $debugMaskDataType = '';
  protected $deploymentGroupsType = GoogleCloudApigeeV1DeploymentGroupConfig::class;
  protected $deploymentGroupsDataType = 'array';
  protected $deploymentsType = GoogleCloudApigeeV1DeploymentConfig::class;
  protected $deploymentsDataType = 'array';
  /**
   * Revision ID for environment-scoped resources (e.g. target servers,
   * keystores) in this config. This ID will increment any time a resource not
   * scoped to a deployment group changes.
   *
   * @var string
   */
  public $envScopedRevisionId;
  /**
   * Feature flags inherited from the organization and environment.
   *
   * @var string[]
   */
  public $featureFlags;
  protected $flowhooksType = GoogleCloudApigeeV1FlowHookConfig::class;
  protected $flowhooksDataType = 'array';
  /**
   * The forward proxy's url to be used by the runtime. When set, runtime will
   * send requests to the target via the given forward proxy. This is only used
   * by programmable gateways.
   *
   * @var string
   */
  public $forwardProxyUri;
  /**
   * The location for the gateway config blob as a URI, e.g. a Cloud Storage
   * URI. This is only used by Envoy-based gateways.
   *
   * @var string
   */
  public $gatewayConfigLocation;
  protected $keystoresType = GoogleCloudApigeeV1KeystoreConfig::class;
  protected $keystoresDataType = 'array';
  /**
   * Name of the environment configuration in the following format:
   * `organizations/{org}/environments/{env}/configs/{config}`
   *
   * @var string
   */
  public $name;
  /**
   * Used by the Control plane to add context information to help detect the
   * source of the document during diagnostics and debugging.
   *
   * @var string
   */
  public $provider;
  /**
   * Name of the PubSub topic for the environment.
   *
   * @var string
   */
  public $pubsubTopic;
  protected $resourceReferencesType = GoogleCloudApigeeV1ReferenceConfig::class;
  protected $resourceReferencesDataType = 'array';
  protected $resourcesType = GoogleCloudApigeeV1ResourceConfig::class;
  protected $resourcesDataType = 'array';
  /**
   * Revision ID of the environment configuration. The higher the value, the
   * more recently the configuration was deployed.
   *
   * @var string
   */
  public $revisionId;
  /**
   * DEPRECATED: Use revision_id.
   *
   * @var string
   */
  public $sequenceNumber;
  protected $targetsType = GoogleCloudApigeeV1TargetServerConfig::class;
  protected $targetsDataType = 'array';
  protected $traceConfigType = GoogleCloudApigeeV1RuntimeTraceConfig::class;
  protected $traceConfigDataType = '';
  /**
   * Unique ID for the environment configuration. The ID will only change if the
   * environment is deleted and recreated.
   *
   * @var string
   */
  public $uid;

  /**
   * The latest runtime configurations for add-ons.
   *
   * @param GoogleCloudApigeeV1RuntimeAddonsConfig $addonsConfig
   */
  public function setAddonsConfig(GoogleCloudApigeeV1RuntimeAddonsConfig $addonsConfig)
  {
    $this->addonsConfig = $addonsConfig;
  }
  /**
   * @return GoogleCloudApigeeV1RuntimeAddonsConfig
   */
  public function getAddonsConfig()
  {
    return $this->addonsConfig;
  }
  /**
   * The location for the config blob of API Runtime Control, aka Envoy Adapter,
   * for op-based authentication as a URI, e.g. a Cloud Storage URI. This is
   * only used by Envoy-based gateways.
   *
   * @param string $arcConfigLocation
   */
  public function setArcConfigLocation($arcConfigLocation)
  {
    $this->arcConfigLocation = $arcConfigLocation;
  }
  /**
   * @return string
   */
  public function getArcConfigLocation()
  {
    return $this->arcConfigLocation;
  }
  /**
   * The algorithm to resolve IP.
   *
   * @param GoogleCloudApigeeV1EnvironmentConfigClientIPResolutionConfig $clientIpResolutionConfig
   */
  public function setClientIpResolutionConfig(GoogleCloudApigeeV1EnvironmentConfigClientIPResolutionConfig $clientIpResolutionConfig)
  {
    $this->clientIpResolutionConfig = $clientIpResolutionConfig;
  }
  /**
   * @return GoogleCloudApigeeV1EnvironmentConfigClientIPResolutionConfig
   */
  public function getClientIpResolutionConfig()
  {
    return $this->clientIpResolutionConfig;
  }
  /**
   * Time that the environment configuration was created.
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
   * List of data collectors used by the deployments in the environment.
   *
   * @param GoogleCloudApigeeV1DataCollectorConfig[] $dataCollectors
   */
  public function setDataCollectors($dataCollectors)
  {
    $this->dataCollectors = $dataCollectors;
  }
  /**
   * @return GoogleCloudApigeeV1DataCollectorConfig[]
   */
  public function getDataCollectors()
  {
    return $this->dataCollectors;
  }
  /**
   * Debug mask that applies to all deployments in the environment.
   *
   * @param GoogleCloudApigeeV1DebugMask $debugMask
   */
  public function setDebugMask(GoogleCloudApigeeV1DebugMask $debugMask)
  {
    $this->debugMask = $debugMask;
  }
  /**
   * @return GoogleCloudApigeeV1DebugMask
   */
  public function getDebugMask()
  {
    return $this->debugMask;
  }
  /**
   * List of deployment groups in the environment.
   *
   * @param GoogleCloudApigeeV1DeploymentGroupConfig[] $deploymentGroups
   */
  public function setDeploymentGroups($deploymentGroups)
  {
    $this->deploymentGroups = $deploymentGroups;
  }
  /**
   * @return GoogleCloudApigeeV1DeploymentGroupConfig[]
   */
  public function getDeploymentGroups()
  {
    return $this->deploymentGroups;
  }
  /**
   * List of deployments in the environment.
   *
   * @param GoogleCloudApigeeV1DeploymentConfig[] $deployments
   */
  public function setDeployments($deployments)
  {
    $this->deployments = $deployments;
  }
  /**
   * @return GoogleCloudApigeeV1DeploymentConfig[]
   */
  public function getDeployments()
  {
    return $this->deployments;
  }
  /**
   * Revision ID for environment-scoped resources (e.g. target servers,
   * keystores) in this config. This ID will increment any time a resource not
   * scoped to a deployment group changes.
   *
   * @param string $envScopedRevisionId
   */
  public function setEnvScopedRevisionId($envScopedRevisionId)
  {
    $this->envScopedRevisionId = $envScopedRevisionId;
  }
  /**
   * @return string
   */
  public function getEnvScopedRevisionId()
  {
    return $this->envScopedRevisionId;
  }
  /**
   * Feature flags inherited from the organization and environment.
   *
   * @param string[] $featureFlags
   */
  public function setFeatureFlags($featureFlags)
  {
    $this->featureFlags = $featureFlags;
  }
  /**
   * @return string[]
   */
  public function getFeatureFlags()
  {
    return $this->featureFlags;
  }
  /**
   * List of flow hooks in the environment.
   *
   * @param GoogleCloudApigeeV1FlowHookConfig[] $flowhooks
   */
  public function setFlowhooks($flowhooks)
  {
    $this->flowhooks = $flowhooks;
  }
  /**
   * @return GoogleCloudApigeeV1FlowHookConfig[]
   */
  public function getFlowhooks()
  {
    return $this->flowhooks;
  }
  /**
   * The forward proxy's url to be used by the runtime. When set, runtime will
   * send requests to the target via the given forward proxy. This is only used
   * by programmable gateways.
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
   * The location for the gateway config blob as a URI, e.g. a Cloud Storage
   * URI. This is only used by Envoy-based gateways.
   *
   * @param string $gatewayConfigLocation
   */
  public function setGatewayConfigLocation($gatewayConfigLocation)
  {
    $this->gatewayConfigLocation = $gatewayConfigLocation;
  }
  /**
   * @return string
   */
  public function getGatewayConfigLocation()
  {
    return $this->gatewayConfigLocation;
  }
  /**
   * List of keystores in the environment.
   *
   * @param GoogleCloudApigeeV1KeystoreConfig[] $keystores
   */
  public function setKeystores($keystores)
  {
    $this->keystores = $keystores;
  }
  /**
   * @return GoogleCloudApigeeV1KeystoreConfig[]
   */
  public function getKeystores()
  {
    return $this->keystores;
  }
  /**
   * Name of the environment configuration in the following format:
   * `organizations/{org}/environments/{env}/configs/{config}`
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
   * Used by the Control plane to add context information to help detect the
   * source of the document during diagnostics and debugging.
   *
   * @param string $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Name of the PubSub topic for the environment.
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
  /**
   * List of resource references in the environment.
   *
   * @param GoogleCloudApigeeV1ReferenceConfig[] $resourceReferences
   */
  public function setResourceReferences($resourceReferences)
  {
    $this->resourceReferences = $resourceReferences;
  }
  /**
   * @return GoogleCloudApigeeV1ReferenceConfig[]
   */
  public function getResourceReferences()
  {
    return $this->resourceReferences;
  }
  /**
   * List of resource versions in the environment.
   *
   * @param GoogleCloudApigeeV1ResourceConfig[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return GoogleCloudApigeeV1ResourceConfig[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Revision ID of the environment configuration. The higher the value, the
   * more recently the configuration was deployed.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * DEPRECATED: Use revision_id.
   *
   * @param string $sequenceNumber
   */
  public function setSequenceNumber($sequenceNumber)
  {
    $this->sequenceNumber = $sequenceNumber;
  }
  /**
   * @return string
   */
  public function getSequenceNumber()
  {
    return $this->sequenceNumber;
  }
  /**
   * List of target servers in the environment. Disabled target servers are not
   * displayed.
   *
   * @param GoogleCloudApigeeV1TargetServerConfig[] $targets
   */
  public function setTargets($targets)
  {
    $this->targets = $targets;
  }
  /**
   * @return GoogleCloudApigeeV1TargetServerConfig[]
   */
  public function getTargets()
  {
    return $this->targets;
  }
  /**
   * Trace configurations. Contains config for the environment and config
   * overrides for specific API proxies.
   *
   * @param GoogleCloudApigeeV1RuntimeTraceConfig $traceConfig
   */
  public function setTraceConfig(GoogleCloudApigeeV1RuntimeTraceConfig $traceConfig)
  {
    $this->traceConfig = $traceConfig;
  }
  /**
   * @return GoogleCloudApigeeV1RuntimeTraceConfig
   */
  public function getTraceConfig()
  {
    return $this->traceConfig;
  }
  /**
   * Unique ID for the environment configuration. The ID will only change if the
   * environment is deleted and recreated.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentConfig');
