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

namespace Google\Service\Appengine;

class Version extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const SERVING_STATUS_SERVING_STATUS_UNSPECIFIED = 'SERVING_STATUS_UNSPECIFIED';
  /**
   * Currently serving. Instances are created according to the scaling settings
   * of the version.
   */
  public const SERVING_STATUS_SERVING = 'SERVING';
  /**
   * Disabled. No instances will be created and the scaling settings are ignored
   * until the state of the version changes to SERVING.
   */
  public const SERVING_STATUS_STOPPED = 'STOPPED';
  protected $collection_key = 'zones';
  protected $apiConfigType = ApiConfigHandler::class;
  protected $apiConfigDataType = '';
  /**
   * Allows App Engine second generation runtimes to access the legacy bundled
   * services.
   *
   * @var bool
   */
  public $appEngineApis;
  protected $automaticScalingType = AutomaticScaling::class;
  protected $automaticScalingDataType = '';
  protected $basicScalingType = BasicScaling::class;
  protected $basicScalingDataType = '';
  /**
   * Metadata settings that are supplied to this version to enable beta runtime
   * features.
   *
   * @var string[]
   */
  public $betaSettings;
  /**
   * Environment variables available to the build environment.Only returned in
   * GET requests if view=FULL is set.
   *
   * @var string[]
   */
  public $buildEnvVariables;
  /**
   * Time that this version was created.@OutputOnly
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Email address of the user who created this version.@OutputOnly
   *
   * @var string
   */
  public $createdBy;
  /**
   * Duration that static files should be cached by web proxies and browsers.
   * Only applicable if the corresponding StaticFilesHandler
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StaticFilesHandler) does not
   * specify its own expiration time.Only returned in GET requests if view=FULL
   * is set.
   *
   * @var string
   */
  public $defaultExpiration;
  protected $deploymentType = Deployment::class;
  protected $deploymentDataType = '';
  /**
   * Output only. Total size in bytes of all the files that are included in this
   * version and currently hosted on the App Engine disk.@OutputOnly
   *
   * @var string
   */
  public $diskUsageBytes;
  protected $endpointsApiServiceType = EndpointsApiService::class;
  protected $endpointsApiServiceDataType = '';
  protected $entrypointType = Entrypoint::class;
  protected $entrypointDataType = '';
  /**
   * App Engine execution environment for this version.Defaults to standard.
   *
   * @var string
   */
  public $env;
  /**
   * Environment variables available to the application.Only returned in GET
   * requests if view=FULL is set.
   *
   * @var string[]
   */
  public $envVariables;
  protected $errorHandlersType = ErrorHandler::class;
  protected $errorHandlersDataType = 'array';
  protected $flexibleRuntimeSettingsType = FlexibleRuntimeSettings::class;
  protected $flexibleRuntimeSettingsDataType = '';
  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetVersionRequest
   *
   * @var array[]
   */
  public $generatedCustomerMetadata;
  protected $handlersType = UrlMap::class;
  protected $handlersDataType = 'array';
  protected $healthCheckType = HealthCheck::class;
  protected $healthCheckDataType = '';
  /**
   * Relative name of the version within the service. Example: v1. Version names
   * can contain only lowercase letters, numbers, or hyphens. Reserved names:
   * "default", "latest", and any name with the prefix "ah-".
   *
   * @var string
   */
  public $id;
  /**
   * Before an application can receive email or XMPP messages, the application
   * must be configured to enable the service.
   *
   * @var string[]
   */
  public $inboundServices;
  /**
   * Instance class that is used to run this version. Valid values are:
   * AutomaticScaling: F1, F2, F4, F4_1G ManualScaling or BasicScaling: B1, B2,
   * B4, B8, B4_1GDefaults to F1 for AutomaticScaling and B1 for ManualScaling
   * or BasicScaling.
   *
   * @var string
   */
  public $instanceClass;
  protected $librariesType = Library::class;
  protected $librariesDataType = 'array';
  protected $livenessCheckType = LivenessCheck::class;
  protected $livenessCheckDataType = '';
  protected $manualScalingType = ManualScaling::class;
  protected $manualScalingDataType = '';
  /**
   * Output only. Full path to the Version resource in the API. Example:
   * apps/myapp/services/default/versions/v1.@OutputOnly
   *
   * @var string
   */
  public $name;
  protected $networkType = Network::class;
  protected $networkDataType = '';
  /**
   * Files that match this pattern will not be built into this version. Only
   * applicable for Go runtimes.Only returned in GET requests if view=FULL is
   * set.
   *
   * @var string
   */
  public $nobuildFilesRegex;
  protected $readinessCheckType = ReadinessCheck::class;
  protected $readinessCheckDataType = '';
  protected $resourcesType = Resources::class;
  protected $resourcesDataType = '';
  /**
   * Desired runtime. Example: python27.
   *
   * @var string
   */
  public $runtime;
  /**
   * The version of the API in the given runtime environment. Please see the
   * app.yaml reference for valid values at
   * https://cloud.google.com/appengine/docs/standard//config/appref
   *
   * @var string
   */
  public $runtimeApiVersion;
  /**
   * The channel of the runtime to use. Only available for some runtimes.
   * Defaults to the default channel.
   *
   * @var string
   */
  public $runtimeChannel;
  /**
   * The path or name of the app's main executable.
   *
   * @var string
   */
  public $runtimeMainExecutablePath;
  /**
   * The identity that the deployed version will run as. Admin API will use the
   * App Engine Appspot service account as default if this field is neither
   * provided in app.yaml file nor through CLI flag.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Current serving status of this version. Only the versions with a SERVING
   * status create instances and can be billed.SERVING_STATUS_UNSPECIFIED is an
   * invalid value. Defaults to SERVING.
   *
   * @var string
   */
  public $servingStatus;
  /**
   * Whether multiple requests can be dispatched to this version at once.
   *
   * @var bool
   */
  public $threadsafe;
  /**
   * Output only. Serving URL for this version. Example: "https://myversion-dot-
   * myservice-dot-myapp.appspot.com"@OutputOnly
   *
   * @var string
   */
  public $versionUrl;
  /**
   * Whether to deploy this version in a container on a virtual machine.
   *
   * @var bool
   */
  public $vm;
  protected $vpcAccessConnectorType = VpcAccessConnector::class;
  protected $vpcAccessConnectorDataType = '';
  /**
   * The Google Compute Engine zones that are supported by this version in the
   * App Engine flexible environment. Deprecated.
   *
   * @var string[]
   */
  public $zones;

  /**
   * Serving configuration for Google Cloud Endpoints
   * (https://cloud.google.com/endpoints).Only returned in GET requests if
   * view=FULL is set.
   *
   * @deprecated
   * @param ApiConfigHandler $apiConfig
   */
  public function setApiConfig(ApiConfigHandler $apiConfig)
  {
    $this->apiConfig = $apiConfig;
  }
  /**
   * @deprecated
   * @return ApiConfigHandler
   */
  public function getApiConfig()
  {
    return $this->apiConfig;
  }
  /**
   * Allows App Engine second generation runtimes to access the legacy bundled
   * services.
   *
   * @param bool $appEngineApis
   */
  public function setAppEngineApis($appEngineApis)
  {
    $this->appEngineApis = $appEngineApis;
  }
  /**
   * @return bool
   */
  public function getAppEngineApis()
  {
    return $this->appEngineApis;
  }
  /**
   * Automatic scaling is based on request rate, response latencies, and other
   * application metrics. Instances are dynamically created and destroyed as
   * needed in order to handle traffic.
   *
   * @param AutomaticScaling $automaticScaling
   */
  public function setAutomaticScaling(AutomaticScaling $automaticScaling)
  {
    $this->automaticScaling = $automaticScaling;
  }
  /**
   * @return AutomaticScaling
   */
  public function getAutomaticScaling()
  {
    return $this->automaticScaling;
  }
  /**
   * A service with basic scaling will create an instance when the application
   * receives a request. The instance will be turned down when the app becomes
   * idle. Basic scaling is ideal for work that is intermittent or driven by
   * user activity.
   *
   * @param BasicScaling $basicScaling
   */
  public function setBasicScaling(BasicScaling $basicScaling)
  {
    $this->basicScaling = $basicScaling;
  }
  /**
   * @return BasicScaling
   */
  public function getBasicScaling()
  {
    return $this->basicScaling;
  }
  /**
   * Metadata settings that are supplied to this version to enable beta runtime
   * features.
   *
   * @param string[] $betaSettings
   */
  public function setBetaSettings($betaSettings)
  {
    $this->betaSettings = $betaSettings;
  }
  /**
   * @return string[]
   */
  public function getBetaSettings()
  {
    return $this->betaSettings;
  }
  /**
   * Environment variables available to the build environment.Only returned in
   * GET requests if view=FULL is set.
   *
   * @param string[] $buildEnvVariables
   */
  public function setBuildEnvVariables($buildEnvVariables)
  {
    $this->buildEnvVariables = $buildEnvVariables;
  }
  /**
   * @return string[]
   */
  public function getBuildEnvVariables()
  {
    return $this->buildEnvVariables;
  }
  /**
   * Time that this version was created.@OutputOnly
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
   * Output only. Email address of the user who created this version.@OutputOnly
   *
   * @param string $createdBy
   */
  public function setCreatedBy($createdBy)
  {
    $this->createdBy = $createdBy;
  }
  /**
   * @return string
   */
  public function getCreatedBy()
  {
    return $this->createdBy;
  }
  /**
   * Duration that static files should be cached by web proxies and browsers.
   * Only applicable if the corresponding StaticFilesHandler
   * (https://cloud.google.com/appengine/docs/admin-
   * api/reference/rest/v1/apps.services.versions#StaticFilesHandler) does not
   * specify its own expiration time.Only returned in GET requests if view=FULL
   * is set.
   *
   * @param string $defaultExpiration
   */
  public function setDefaultExpiration($defaultExpiration)
  {
    $this->defaultExpiration = $defaultExpiration;
  }
  /**
   * @return string
   */
  public function getDefaultExpiration()
  {
    return $this->defaultExpiration;
  }
  /**
   * Code and application artifacts that make up this version.Only returned in
   * GET requests if view=FULL is set.
   *
   * @param Deployment $deployment
   */
  public function setDeployment(Deployment $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return Deployment
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Output only. Total size in bytes of all the files that are included in this
   * version and currently hosted on the App Engine disk.@OutputOnly
   *
   * @param string $diskUsageBytes
   */
  public function setDiskUsageBytes($diskUsageBytes)
  {
    $this->diskUsageBytes = $diskUsageBytes;
  }
  /**
   * @return string
   */
  public function getDiskUsageBytes()
  {
    return $this->diskUsageBytes;
  }
  /**
   * Cloud Endpoints configuration.If endpoints_api_service is set, the Cloud
   * Endpoints Extensible Service Proxy will be provided to serve the API
   * implemented by the app.
   *
   * @param EndpointsApiService $endpointsApiService
   */
  public function setEndpointsApiService(EndpointsApiService $endpointsApiService)
  {
    $this->endpointsApiService = $endpointsApiService;
  }
  /**
   * @return EndpointsApiService
   */
  public function getEndpointsApiService()
  {
    return $this->endpointsApiService;
  }
  /**
   * The entrypoint for the application.
   *
   * @param Entrypoint $entrypoint
   */
  public function setEntrypoint(Entrypoint $entrypoint)
  {
    $this->entrypoint = $entrypoint;
  }
  /**
   * @return Entrypoint
   */
  public function getEntrypoint()
  {
    return $this->entrypoint;
  }
  /**
   * App Engine execution environment for this version.Defaults to standard.
   *
   * @param string $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return string
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Environment variables available to the application.Only returned in GET
   * requests if view=FULL is set.
   *
   * @param string[] $envVariables
   */
  public function setEnvVariables($envVariables)
  {
    $this->envVariables = $envVariables;
  }
  /**
   * @return string[]
   */
  public function getEnvVariables()
  {
    return $this->envVariables;
  }
  /**
   * Custom static error pages. Limited to 10KB per page.Only returned in GET
   * requests if view=FULL is set.
   *
   * @param ErrorHandler[] $errorHandlers
   */
  public function setErrorHandlers($errorHandlers)
  {
    $this->errorHandlers = $errorHandlers;
  }
  /**
   * @return ErrorHandler[]
   */
  public function getErrorHandlers()
  {
    return $this->errorHandlers;
  }
  /**
   * Settings for App Engine flexible runtimes.
   *
   * @param FlexibleRuntimeSettings $flexibleRuntimeSettings
   */
  public function setFlexibleRuntimeSettings(FlexibleRuntimeSettings $flexibleRuntimeSettings)
  {
    $this->flexibleRuntimeSettings = $flexibleRuntimeSettings;
  }
  /**
   * @return FlexibleRuntimeSettings
   */
  public function getFlexibleRuntimeSettings()
  {
    return $this->flexibleRuntimeSettings;
  }
  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetVersionRequest
   *
   * @param array[] $generatedCustomerMetadata
   */
  public function setGeneratedCustomerMetadata($generatedCustomerMetadata)
  {
    $this->generatedCustomerMetadata = $generatedCustomerMetadata;
  }
  /**
   * @return array[]
   */
  public function getGeneratedCustomerMetadata()
  {
    return $this->generatedCustomerMetadata;
  }
  /**
   * An ordered list of URL-matching patterns that should be applied to incoming
   * requests. The first matching URL handles the request and other request
   * handlers are not attempted.Only returned in GET requests if view=FULL is
   * set.
   *
   * @param UrlMap[] $handlers
   */
  public function setHandlers($handlers)
  {
    $this->handlers = $handlers;
  }
  /**
   * @return UrlMap[]
   */
  public function getHandlers()
  {
    return $this->handlers;
  }
  /**
   * Configures health checking for instances. Unhealthy instances are stopped
   * and replaced with new instances. Only applicable in the App Engine flexible
   * environment.
   *
   * @param HealthCheck $healthCheck
   */
  public function setHealthCheck(HealthCheck $healthCheck)
  {
    $this->healthCheck = $healthCheck;
  }
  /**
   * @return HealthCheck
   */
  public function getHealthCheck()
  {
    return $this->healthCheck;
  }
  /**
   * Relative name of the version within the service. Example: v1. Version names
   * can contain only lowercase letters, numbers, or hyphens. Reserved names:
   * "default", "latest", and any name with the prefix "ah-".
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Before an application can receive email or XMPP messages, the application
   * must be configured to enable the service.
   *
   * @param string[] $inboundServices
   */
  public function setInboundServices($inboundServices)
  {
    $this->inboundServices = $inboundServices;
  }
  /**
   * @return string[]
   */
  public function getInboundServices()
  {
    return $this->inboundServices;
  }
  /**
   * Instance class that is used to run this version. Valid values are:
   * AutomaticScaling: F1, F2, F4, F4_1G ManualScaling or BasicScaling: B1, B2,
   * B4, B8, B4_1GDefaults to F1 for AutomaticScaling and B1 for ManualScaling
   * or BasicScaling.
   *
   * @param string $instanceClass
   */
  public function setInstanceClass($instanceClass)
  {
    $this->instanceClass = $instanceClass;
  }
  /**
   * @return string
   */
  public function getInstanceClass()
  {
    return $this->instanceClass;
  }
  /**
   * Configuration for third-party Python runtime libraries that are required by
   * the application.Only returned in GET requests if view=FULL is set.
   *
   * @param Library[] $libraries
   */
  public function setLibraries($libraries)
  {
    $this->libraries = $libraries;
  }
  /**
   * @return Library[]
   */
  public function getLibraries()
  {
    return $this->libraries;
  }
  /**
   * Configures liveness health checking for instances. Unhealthy instances are
   * stopped and replaced with new instances
   *
   * @param LivenessCheck $livenessCheck
   */
  public function setLivenessCheck(LivenessCheck $livenessCheck)
  {
    $this->livenessCheck = $livenessCheck;
  }
  /**
   * @return LivenessCheck
   */
  public function getLivenessCheck()
  {
    return $this->livenessCheck;
  }
  /**
   * A service with manual scaling runs continuously, allowing you to perform
   * complex initialization and rely on the state of its memory over time.
   * Manually scaled versions are sometimes referred to as "backends".
   *
   * @param ManualScaling $manualScaling
   */
  public function setManualScaling(ManualScaling $manualScaling)
  {
    $this->manualScaling = $manualScaling;
  }
  /**
   * @return ManualScaling
   */
  public function getManualScaling()
  {
    return $this->manualScaling;
  }
  /**
   * Output only. Full path to the Version resource in the API. Example:
   * apps/myapp/services/default/versions/v1.@OutputOnly
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
   * Extra network settings. Only applicable in the App Engine flexible
   * environment.
   *
   * @param Network $network
   */
  public function setNetwork(Network $network)
  {
    $this->network = $network;
  }
  /**
   * @return Network
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Files that match this pattern will not be built into this version. Only
   * applicable for Go runtimes.Only returned in GET requests if view=FULL is
   * set.
   *
   * @param string $nobuildFilesRegex
   */
  public function setNobuildFilesRegex($nobuildFilesRegex)
  {
    $this->nobuildFilesRegex = $nobuildFilesRegex;
  }
  /**
   * @return string
   */
  public function getNobuildFilesRegex()
  {
    return $this->nobuildFilesRegex;
  }
  /**
   * Configures readiness health checking for instances. Unhealthy instances are
   * not put into the backend traffic rotation.
   *
   * @param ReadinessCheck $readinessCheck
   */
  public function setReadinessCheck(ReadinessCheck $readinessCheck)
  {
    $this->readinessCheck = $readinessCheck;
  }
  /**
   * @return ReadinessCheck
   */
  public function getReadinessCheck()
  {
    return $this->readinessCheck;
  }
  /**
   * Machine resources for this version. Only applicable in the App Engine
   * flexible environment.
   *
   * @param Resources $resources
   */
  public function setResources(Resources $resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return Resources
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Desired runtime. Example: python27.
   *
   * @param string $runtime
   */
  public function setRuntime($runtime)
  {
    $this->runtime = $runtime;
  }
  /**
   * @return string
   */
  public function getRuntime()
  {
    return $this->runtime;
  }
  /**
   * The version of the API in the given runtime environment. Please see the
   * app.yaml reference for valid values at
   * https://cloud.google.com/appengine/docs/standard//config/appref
   *
   * @param string $runtimeApiVersion
   */
  public function setRuntimeApiVersion($runtimeApiVersion)
  {
    $this->runtimeApiVersion = $runtimeApiVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeApiVersion()
  {
    return $this->runtimeApiVersion;
  }
  /**
   * The channel of the runtime to use. Only available for some runtimes.
   * Defaults to the default channel.
   *
   * @param string $runtimeChannel
   */
  public function setRuntimeChannel($runtimeChannel)
  {
    $this->runtimeChannel = $runtimeChannel;
  }
  /**
   * @return string
   */
  public function getRuntimeChannel()
  {
    return $this->runtimeChannel;
  }
  /**
   * The path or name of the app's main executable.
   *
   * @param string $runtimeMainExecutablePath
   */
  public function setRuntimeMainExecutablePath($runtimeMainExecutablePath)
  {
    $this->runtimeMainExecutablePath = $runtimeMainExecutablePath;
  }
  /**
   * @return string
   */
  public function getRuntimeMainExecutablePath()
  {
    return $this->runtimeMainExecutablePath;
  }
  /**
   * The identity that the deployed version will run as. Admin API will use the
   * App Engine Appspot service account as default if this field is neither
   * provided in app.yaml file nor through CLI flag.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Current serving status of this version. Only the versions with a SERVING
   * status create instances and can be billed.SERVING_STATUS_UNSPECIFIED is an
   * invalid value. Defaults to SERVING.
   *
   * Accepted values: SERVING_STATUS_UNSPECIFIED, SERVING, STOPPED
   *
   * @param self::SERVING_STATUS_* $servingStatus
   */
  public function setServingStatus($servingStatus)
  {
    $this->servingStatus = $servingStatus;
  }
  /**
   * @return self::SERVING_STATUS_*
   */
  public function getServingStatus()
  {
    return $this->servingStatus;
  }
  /**
   * Whether multiple requests can be dispatched to this version at once.
   *
   * @param bool $threadsafe
   */
  public function setThreadsafe($threadsafe)
  {
    $this->threadsafe = $threadsafe;
  }
  /**
   * @return bool
   */
  public function getThreadsafe()
  {
    return $this->threadsafe;
  }
  /**
   * Output only. Serving URL for this version. Example: "https://myversion-dot-
   * myservice-dot-myapp.appspot.com"@OutputOnly
   *
   * @param string $versionUrl
   */
  public function setVersionUrl($versionUrl)
  {
    $this->versionUrl = $versionUrl;
  }
  /**
   * @return string
   */
  public function getVersionUrl()
  {
    return $this->versionUrl;
  }
  /**
   * Whether to deploy this version in a container on a virtual machine.
   *
   * @param bool $vm
   */
  public function setVm($vm)
  {
    $this->vm = $vm;
  }
  /**
   * @return bool
   */
  public function getVm()
  {
    return $this->vm;
  }
  /**
   * Enables VPC connectivity for standard apps.
   *
   * @param VpcAccessConnector $vpcAccessConnector
   */
  public function setVpcAccessConnector(VpcAccessConnector $vpcAccessConnector)
  {
    $this->vpcAccessConnector = $vpcAccessConnector;
  }
  /**
   * @return VpcAccessConnector
   */
  public function getVpcAccessConnector()
  {
    return $this->vpcAccessConnector;
  }
  /**
   * The Google Compute Engine zones that are supported by this version in the
   * App Engine flexible environment. Deprecated.
   *
   * @param string[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return string[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Version::class, 'Google_Service_Appengine_Version');
