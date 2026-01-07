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

class GoogleCloudConnectorsV1Connection extends \Google\Collection
{
  /**
   * LAUNCH_STAGE_UNSPECIFIED.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * PREVIEW.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_PREVIEW = 'PREVIEW';
  /**
   * GA.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_GA = 'GA';
  /**
   * DEPRECATED.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  /**
   * TEST.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_TEST = 'TEST';
  /**
   * PRIVATE_PREVIEW.
   */
  public const CONNECTOR_VERSION_LAUNCH_STAGE_PRIVATE_PREVIEW = 'PRIVATE_PREVIEW';
  /**
   * Eventing Enablement Type Unspecifeied.
   */
  public const EVENTING_ENABLEMENT_TYPE_EVENTING_ENABLEMENT_TYPE_UNSPECIFIED = 'EVENTING_ENABLEMENT_TYPE_UNSPECIFIED';
  /**
   * Both connection and eventing.
   */
  public const EVENTING_ENABLEMENT_TYPE_EVENTING_AND_CONNECTION = 'EVENTING_AND_CONNECTION';
  /**
   * Only Eventing.
   */
  public const EVENTING_ENABLEMENT_TYPE_ONLY_EVENTING = 'ONLY_EVENTING';
  /**
   * Unspecified subscription type.
   */
  public const SUBSCRIPTION_TYPE_SUBSCRIPTION_TYPE_UNSPECIFIED = 'SUBSCRIPTION_TYPE_UNSPECIFIED';
  /**
   * PayG subscription.
   */
  public const SUBSCRIPTION_TYPE_PAY_G = 'PAY_G';
  /**
   * Paid Subscription.
   */
  public const SUBSCRIPTION_TYPE_PAID = 'PAID';
  protected $collection_key = 'trafficShapingConfigs';
  /**
   * Optional. Async operations enabled for the connection. If Async Operations
   * is enabled, Connection allows the customers to initiate async long running
   * operations using the actions API.
   *
   * @var bool
   */
  public $asyncOperationsEnabled;
  protected $authConfigType = GoogleCloudConnectorsV1AuthConfig::class;
  protected $authConfigDataType = '';
  /**
   * Optional. Auth override enabled for the connection. If Auth Override is
   * enabled, Connection allows the backend service auth to be overridden in the
   * entities/actions API.
   *
   * @var bool
   */
  public $authOverrideEnabled;
  protected $billingConfigType = GoogleCloudConnectorsV1BillingConfig::class;
  protected $billingConfigDataType = '';
  protected $configVariablesType = GoogleCloudConnectorsV1ConfigVariable::class;
  protected $configVariablesDataType = 'array';
  /**
   * Output only. Connection revision. This field is only updated when the
   * connection is created or updated by User.
   *
   * @var string
   */
  public $connectionRevision;
  /**
   * Required. Connector version on which the connection is created. The format
   * is: projects/locations/providers/connectors/versions Only global location
   * is supported for ConnectorVersion resource.
   *
   * @var string
   */
  public $connectorVersion;
  protected $connectorVersionInfraConfigType = GoogleCloudConnectorsV1ConnectorVersionInfraConfig::class;
  protected $connectorVersionInfraConfigDataType = '';
  /**
   * Output only. Flag to mark the version indicating the launch stage.
   *
   * @var string
   */
  public $connectorVersionLaunchStage;
  /**
   * Output only. Created time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the resource.
   *
   * @var string
   */
  public $description;
  protected $destinationConfigsType = GoogleCloudConnectorsV1DestinationConfig::class;
  protected $destinationConfigsDataType = 'array';
  /**
   * Output only. GCR location where the envoy image is stored. formatted like:
   * gcr.io/{bucketName}/{imageName}
   *
   * @var string
   */
  public $envoyImageLocation;
  protected $euaOauthAuthConfigType = GoogleCloudConnectorsV1AuthConfig::class;
  protected $euaOauthAuthConfigDataType = '';
  protected $eventingConfigType = GoogleCloudConnectorsV1EventingConfig::class;
  protected $eventingConfigDataType = '';
  /**
   * Optional. Eventing enablement type. Will be nil if eventing is not enabled.
   *
   * @var string
   */
  public $eventingEnablementType;
  protected $eventingRuntimeDataType = GoogleCloudConnectorsV1EventingRuntimeData::class;
  protected $eventingRuntimeDataDataType = '';
  /**
   * Optional. Fallback on admin credentials for the connection. If this both
   * auth_override_enabled and fallback_on_admin_credentials are set to true,
   * the connection will use the admin credentials if the dynamic auth header is
   * not present during auth override.
   *
   * @var bool
   */
  public $fallbackOnAdminCredentials;
  /**
   * Output only. The name of the Hostname of the Service Directory service with
   * TLS.
   *
   * @var string
   */
  public $host;
  /**
   * Output only. GCR location where the runtime image is stored. formatted
   * like: gcr.io/{bucketName}/{imageName}
   *
   * @var string
   */
  public $imageLocation;
  /**
   * Output only. Is trusted tester program enabled for the project.
   *
   * @var bool
   */
  public $isTrustedTester;
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  protected $lockConfigType = GoogleCloudConnectorsV1LockConfig::class;
  protected $lockConfigDataType = '';
  protected $logConfigType = GoogleCloudConnectorsV1LogConfig::class;
  protected $logConfigDataType = '';
  /**
   * Output only. Resource name of the Connection. Format:
   * projects/{project}/locations/{location}/connections/{connection}
   *
   * @var string
   */
  public $name;
  protected $nodeConfigType = GoogleCloudConnectorsV1NodeConfig::class;
  protected $nodeConfigDataType = '';
  /**
   * Optional. Service account needed for runtime plane to access Google Cloud
   * resources.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. The name of the Service Directory service name. Used for
   * Private Harpoon to resolve the ILB address. e.g. "projects/cloud-
   * connectors-e2e-testing/locations/us-central1/namespaces/istio-
   * system/services/istio-ingressgateway-connectors"
   *
   * @var string
   */
  public $serviceDirectory;
  protected $sslConfigType = GoogleCloudConnectorsV1SslConfig::class;
  protected $sslConfigDataType = '';
  protected $statusType = GoogleCloudConnectorsV1ConnectionStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. This subscription type enum states the subscription type of
   * the project.
   *
   * @var string
   */
  public $subscriptionType;
  /**
   * Optional. Suspended indicates if a user has suspended a connection or not.
   *
   * @var bool
   */
  public $suspended;
  /**
   * Output only. The name of the Service Directory service with TLS.
   *
   * @var string
   */
  public $tlsServiceDirectory;
  protected $trafficShapingConfigsType = GoogleCloudConnectorsV1TrafficShapingConfig::class;
  protected $trafficShapingConfigsDataType = 'array';
  /**
   * Output only. Updated time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Async operations enabled for the connection. If Async Operations
   * is enabled, Connection allows the customers to initiate async long running
   * operations using the actions API.
   *
   * @param bool $asyncOperationsEnabled
   */
  public function setAsyncOperationsEnabled($asyncOperationsEnabled)
  {
    $this->asyncOperationsEnabled = $asyncOperationsEnabled;
  }
  /**
   * @return bool
   */
  public function getAsyncOperationsEnabled()
  {
    return $this->asyncOperationsEnabled;
  }
  /**
   * Optional. Configuration for establishing the connection's authentication
   * with an external system.
   *
   * @param GoogleCloudConnectorsV1AuthConfig $authConfig
   */
  public function setAuthConfig(GoogleCloudConnectorsV1AuthConfig $authConfig)
  {
    $this->authConfig = $authConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfig
   */
  public function getAuthConfig()
  {
    return $this->authConfig;
  }
  /**
   * Optional. Auth override enabled for the connection. If Auth Override is
   * enabled, Connection allows the backend service auth to be overridden in the
   * entities/actions API.
   *
   * @param bool $authOverrideEnabled
   */
  public function setAuthOverrideEnabled($authOverrideEnabled)
  {
    $this->authOverrideEnabled = $authOverrideEnabled;
  }
  /**
   * @return bool
   */
  public function getAuthOverrideEnabled()
  {
    return $this->authOverrideEnabled;
  }
  /**
   * Output only. Billing config for the connection.
   *
   * @param GoogleCloudConnectorsV1BillingConfig $billingConfig
   */
  public function setBillingConfig(GoogleCloudConnectorsV1BillingConfig $billingConfig)
  {
    $this->billingConfig = $billingConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1BillingConfig
   */
  public function getBillingConfig()
  {
    return $this->billingConfig;
  }
  /**
   * Optional. Configuration for configuring the connection with an external
   * system.
   *
   * @param GoogleCloudConnectorsV1ConfigVariable[] $configVariables
   */
  public function setConfigVariables($configVariables)
  {
    $this->configVariables = $configVariables;
  }
  /**
   * @return GoogleCloudConnectorsV1ConfigVariable[]
   */
  public function getConfigVariables()
  {
    return $this->configVariables;
  }
  /**
   * Output only. Connection revision. This field is only updated when the
   * connection is created or updated by User.
   *
   * @param string $connectionRevision
   */
  public function setConnectionRevision($connectionRevision)
  {
    $this->connectionRevision = $connectionRevision;
  }
  /**
   * @return string
   */
  public function getConnectionRevision()
  {
    return $this->connectionRevision;
  }
  /**
   * Required. Connector version on which the connection is created. The format
   * is: projects/locations/providers/connectors/versions Only global location
   * is supported for ConnectorVersion resource.
   *
   * @param string $connectorVersion
   */
  public function setConnectorVersion($connectorVersion)
  {
    $this->connectorVersion = $connectorVersion;
  }
  /**
   * @return string
   */
  public function getConnectorVersion()
  {
    return $this->connectorVersion;
  }
  /**
   * Output only. Infra configs supported by Connector Version.
   *
   * @param GoogleCloudConnectorsV1ConnectorVersionInfraConfig $connectorVersionInfraConfig
   */
  public function setConnectorVersionInfraConfig(GoogleCloudConnectorsV1ConnectorVersionInfraConfig $connectorVersionInfraConfig)
  {
    $this->connectorVersionInfraConfig = $connectorVersionInfraConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1ConnectorVersionInfraConfig
   */
  public function getConnectorVersionInfraConfig()
  {
    return $this->connectorVersionInfraConfig;
  }
  /**
   * Output only. Flag to mark the version indicating the launch stage.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, PREVIEW, GA, DEPRECATED, TEST,
   * PRIVATE_PREVIEW
   *
   * @param self::CONNECTOR_VERSION_LAUNCH_STAGE_* $connectorVersionLaunchStage
   */
  public function setConnectorVersionLaunchStage($connectorVersionLaunchStage)
  {
    $this->connectorVersionLaunchStage = $connectorVersionLaunchStage;
  }
  /**
   * @return self::CONNECTOR_VERSION_LAUNCH_STAGE_*
   */
  public function getConnectorVersionLaunchStage()
  {
    return $this->connectorVersionLaunchStage;
  }
  /**
   * Output only. Created time.
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
   * Optional. Description of the resource.
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
   * Optional. Configuration of the Connector's destination. Only accepted for
   * Connectors that accepts user defined destination(s).
   *
   * @param GoogleCloudConnectorsV1DestinationConfig[] $destinationConfigs
   */
  public function setDestinationConfigs($destinationConfigs)
  {
    $this->destinationConfigs = $destinationConfigs;
  }
  /**
   * @return GoogleCloudConnectorsV1DestinationConfig[]
   */
  public function getDestinationConfigs()
  {
    return $this->destinationConfigs;
  }
  /**
   * Output only. GCR location where the envoy image is stored. formatted like:
   * gcr.io/{bucketName}/{imageName}
   *
   * @param string $envoyImageLocation
   */
  public function setEnvoyImageLocation($envoyImageLocation)
  {
    $this->envoyImageLocation = $envoyImageLocation;
  }
  /**
   * @return string
   */
  public function getEnvoyImageLocation()
  {
    return $this->envoyImageLocation;
  }
  /**
   * Optional. Additional Oauth2.0 Auth config for EUA. If the connection is
   * configured using non-OAuth authentication but OAuth needs to be used for
   * EUA, this field can be populated with the OAuth config. This should be a
   * OAuth2AuthCodeFlow Auth type only.
   *
   * @param GoogleCloudConnectorsV1AuthConfig $euaOauthAuthConfig
   */
  public function setEuaOauthAuthConfig(GoogleCloudConnectorsV1AuthConfig $euaOauthAuthConfig)
  {
    $this->euaOauthAuthConfig = $euaOauthAuthConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfig
   */
  public function getEuaOauthAuthConfig()
  {
    return $this->euaOauthAuthConfig;
  }
  /**
   * Optional. Eventing config of a connection
   *
   * @param GoogleCloudConnectorsV1EventingConfig $eventingConfig
   */
  public function setEventingConfig(GoogleCloudConnectorsV1EventingConfig $eventingConfig)
  {
    $this->eventingConfig = $eventingConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1EventingConfig
   */
  public function getEventingConfig()
  {
    return $this->eventingConfig;
  }
  /**
   * Optional. Eventing enablement type. Will be nil if eventing is not enabled.
   *
   * Accepted values: EVENTING_ENABLEMENT_TYPE_UNSPECIFIED,
   * EVENTING_AND_CONNECTION, ONLY_EVENTING
   *
   * @param self::EVENTING_ENABLEMENT_TYPE_* $eventingEnablementType
   */
  public function setEventingEnablementType($eventingEnablementType)
  {
    $this->eventingEnablementType = $eventingEnablementType;
  }
  /**
   * @return self::EVENTING_ENABLEMENT_TYPE_*
   */
  public function getEventingEnablementType()
  {
    return $this->eventingEnablementType;
  }
  /**
   * Output only. Eventing Runtime Data.
   *
   * @param GoogleCloudConnectorsV1EventingRuntimeData $eventingRuntimeData
   */
  public function setEventingRuntimeData(GoogleCloudConnectorsV1EventingRuntimeData $eventingRuntimeData)
  {
    $this->eventingRuntimeData = $eventingRuntimeData;
  }
  /**
   * @return GoogleCloudConnectorsV1EventingRuntimeData
   */
  public function getEventingRuntimeData()
  {
    return $this->eventingRuntimeData;
  }
  /**
   * Optional. Fallback on admin credentials for the connection. If this both
   * auth_override_enabled and fallback_on_admin_credentials are set to true,
   * the connection will use the admin credentials if the dynamic auth header is
   * not present during auth override.
   *
   * @param bool $fallbackOnAdminCredentials
   */
  public function setFallbackOnAdminCredentials($fallbackOnAdminCredentials)
  {
    $this->fallbackOnAdminCredentials = $fallbackOnAdminCredentials;
  }
  /**
   * @return bool
   */
  public function getFallbackOnAdminCredentials()
  {
    return $this->fallbackOnAdminCredentials;
  }
  /**
   * Output only. The name of the Hostname of the Service Directory service with
   * TLS.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Output only. GCR location where the runtime image is stored. formatted
   * like: gcr.io/{bucketName}/{imageName}
   *
   * @param string $imageLocation
   */
  public function setImageLocation($imageLocation)
  {
    $this->imageLocation = $imageLocation;
  }
  /**
   * @return string
   */
  public function getImageLocation()
  {
    return $this->imageLocation;
  }
  /**
   * Output only. Is trusted tester program enabled for the project.
   *
   * @param bool $isTrustedTester
   */
  public function setIsTrustedTester($isTrustedTester)
  {
    $this->isTrustedTester = $isTrustedTester;
  }
  /**
   * @return bool
   */
  public function getIsTrustedTester()
  {
    return $this->isTrustedTester;
  }
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Configuration that indicates whether or not the Connection can be
   * edited.
   *
   * @param GoogleCloudConnectorsV1LockConfig $lockConfig
   */
  public function setLockConfig(GoogleCloudConnectorsV1LockConfig $lockConfig)
  {
    $this->lockConfig = $lockConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1LockConfig
   */
  public function getLockConfig()
  {
    return $this->lockConfig;
  }
  /**
   * Optional. Log configuration for the connection.
   *
   * @param GoogleCloudConnectorsV1LogConfig $logConfig
   */
  public function setLogConfig(GoogleCloudConnectorsV1LogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1LogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * Output only. Resource name of the Connection. Format:
   * projects/{project}/locations/{location}/connections/{connection}
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
   * Optional. Node configuration for the connection.
   *
   * @param GoogleCloudConnectorsV1NodeConfig $nodeConfig
   */
  public function setNodeConfig(GoogleCloudConnectorsV1NodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1NodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * Optional. Service account needed for runtime plane to access Google Cloud
   * resources.
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
   * Output only. The name of the Service Directory service name. Used for
   * Private Harpoon to resolve the ILB address. e.g. "projects/cloud-
   * connectors-e2e-testing/locations/us-central1/namespaces/istio-
   * system/services/istio-ingressgateway-connectors"
   *
   * @param string $serviceDirectory
   */
  public function setServiceDirectory($serviceDirectory)
  {
    $this->serviceDirectory = $serviceDirectory;
  }
  /**
   * @return string
   */
  public function getServiceDirectory()
  {
    return $this->serviceDirectory;
  }
  /**
   * Optional. Ssl config of a connection
   *
   * @param GoogleCloudConnectorsV1SslConfig $sslConfig
   */
  public function setSslConfig(GoogleCloudConnectorsV1SslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1SslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
  /**
   * Output only. Current status of the connection.
   *
   * @param GoogleCloudConnectorsV1ConnectionStatus $status
   */
  public function setStatus(GoogleCloudConnectorsV1ConnectionStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleCloudConnectorsV1ConnectionStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. This subscription type enum states the subscription type of
   * the project.
   *
   * Accepted values: SUBSCRIPTION_TYPE_UNSPECIFIED, PAY_G, PAID
   *
   * @param self::SUBSCRIPTION_TYPE_* $subscriptionType
   */
  public function setSubscriptionType($subscriptionType)
  {
    $this->subscriptionType = $subscriptionType;
  }
  /**
   * @return self::SUBSCRIPTION_TYPE_*
   */
  public function getSubscriptionType()
  {
    return $this->subscriptionType;
  }
  /**
   * Optional. Suspended indicates if a user has suspended a connection or not.
   *
   * @param bool $suspended
   */
  public function setSuspended($suspended)
  {
    $this->suspended = $suspended;
  }
  /**
   * @return bool
   */
  public function getSuspended()
  {
    return $this->suspended;
  }
  /**
   * Output only. The name of the Service Directory service with TLS.
   *
   * @param string $tlsServiceDirectory
   */
  public function setTlsServiceDirectory($tlsServiceDirectory)
  {
    $this->tlsServiceDirectory = $tlsServiceDirectory;
  }
  /**
   * @return string
   */
  public function getTlsServiceDirectory()
  {
    return $this->tlsServiceDirectory;
  }
  /**
   * Optional. Traffic shaping configuration for the connection.
   *
   * @param GoogleCloudConnectorsV1TrafficShapingConfig[] $trafficShapingConfigs
   */
  public function setTrafficShapingConfigs($trafficShapingConfigs)
  {
    $this->trafficShapingConfigs = $trafficShapingConfigs;
  }
  /**
   * @return GoogleCloudConnectorsV1TrafficShapingConfig[]
   */
  public function getTrafficShapingConfigs()
  {
    return $this->trafficShapingConfigs;
  }
  /**
   * Output only. Updated time.
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
class_alias(GoogleCloudConnectorsV1Connection::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1Connection');
