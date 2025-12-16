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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Endpoint extends \Google\Collection
{
  protected $collection_key = 'deployedModels';
  protected $clientConnectionConfigType = GoogleCloudAiplatformV1ClientConnectionConfig::class;
  protected $clientConnectionConfigDataType = '';
  /**
   * Output only. Timestamp when this Endpoint was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. DNS of the dedicated endpoint. Will only be populated if
   * dedicated_endpoint_enabled is true. Depending on the features enabled, uid
   * might be a random number or a string. For example, if fast_tryout is
   * enabled, uid will be fasttryout. Format:
   * `https://{endpoint_id}.{region}-{uid}.prediction.vertexai.goog`.
   *
   * @var string
   */
  public $dedicatedEndpointDns;
  /**
   * If true, the endpoint will be exposed through a dedicated DNS
   * [Endpoint.dedicated_endpoint_dns]. Your request to the dedicated DNS will
   * be isolated from other users' traffic and will have better performance and
   * reliability. Note: Once you enabled dedicated endpoint, you won't be able
   * to send request to the shared DNS {region}-aiplatform.googleapis.com. The
   * limitation will be removed soon.
   *
   * @var bool
   */
  public $dedicatedEndpointEnabled;
  protected $deployedModelsType = GoogleCloudAiplatformV1DeployedModel::class;
  protected $deployedModelsDataType = 'array';
  /**
   * The description of the Endpoint.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the Endpoint. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Deprecated: If true, expose the Endpoint via private service connect. Only
   * one of the fields, network or enable_private_service_connect, can be set.
   *
   * @deprecated
   * @var bool
   */
  public $enablePrivateServiceConnect;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $gdcConfigType = GoogleCloudAiplatformV1GdcConfig::class;
  protected $gdcConfigDataType = '';
  protected $genAiAdvancedFeaturesConfigType = GoogleCloudAiplatformV1GenAiAdvancedFeaturesConfig::class;
  protected $genAiAdvancedFeaturesConfigDataType = '';
  /**
   * The labels with user-defined metadata to organize your Endpoints. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. See https://goo.gl/xmQnxf for
   * more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Resource name of the Model Monitoring job associated with this
   * Endpoint if monitoring is enabled by
   * JobService.CreateModelDeploymentMonitoringJob. Format: `projects/{project}/
   * locations/{location}/modelDeploymentMonitoringJobs/{model_deployment_monito
   * ring_job}`
   *
   * @var string
   */
  public $modelDeploymentMonitoringJob;
  /**
   * Output only. The resource name of the Endpoint.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com//compute/docs/networks-and-
   * firewalls#networks) to which the Endpoint should be peered. Private
   * services access must already be configured for the network. If left
   * unspecified, the Endpoint is not peered with any network. Only one of the
   * fields, network or enable_private_service_connect, can be set. [Format](htt
   * ps://cloud.google.com/compute/docs/reference/rest/v1/networks/insert):
   * `projects/{project}/global/networks/{network}`. Where `{project}` is a
   * project number, as in `12345`, and `{network}` is network name.
   *
   * @var string
   */
  public $network;
  protected $predictRequestResponseLoggingConfigType = GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig::class;
  protected $predictRequestResponseLoggingConfigDataType = '';
  protected $privateServiceConnectConfigType = GoogleCloudAiplatformV1PrivateServiceConnectConfig::class;
  protected $privateServiceConnectConfigDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * A map from a DeployedModel's ID to the percentage of this Endpoint's
   * traffic that should be forwarded to that DeployedModel. If a
   * DeployedModel's ID is not listed in this map, then it receives no traffic.
   * The traffic percentage values must add up to 100, or map must be empty if
   * the Endpoint is to not accept any traffic at a moment.
   *
   * @var int[]
   */
  public $trafficSplit;
  /**
   * Output only. Timestamp when this Endpoint was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Configurations that are applied to the endpoint for online prediction.
   *
   * @param GoogleCloudAiplatformV1ClientConnectionConfig $clientConnectionConfig
   */
  public function setClientConnectionConfig(GoogleCloudAiplatformV1ClientConnectionConfig $clientConnectionConfig)
  {
    $this->clientConnectionConfig = $clientConnectionConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ClientConnectionConfig
   */
  public function getClientConnectionConfig()
  {
    return $this->clientConnectionConfig;
  }
  /**
   * Output only. Timestamp when this Endpoint was created.
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
   * Output only. DNS of the dedicated endpoint. Will only be populated if
   * dedicated_endpoint_enabled is true. Depending on the features enabled, uid
   * might be a random number or a string. For example, if fast_tryout is
   * enabled, uid will be fasttryout. Format:
   * `https://{endpoint_id}.{region}-{uid}.prediction.vertexai.goog`.
   *
   * @param string $dedicatedEndpointDns
   */
  public function setDedicatedEndpointDns($dedicatedEndpointDns)
  {
    $this->dedicatedEndpointDns = $dedicatedEndpointDns;
  }
  /**
   * @return string
   */
  public function getDedicatedEndpointDns()
  {
    return $this->dedicatedEndpointDns;
  }
  /**
   * If true, the endpoint will be exposed through a dedicated DNS
   * [Endpoint.dedicated_endpoint_dns]. Your request to the dedicated DNS will
   * be isolated from other users' traffic and will have better performance and
   * reliability. Note: Once you enabled dedicated endpoint, you won't be able
   * to send request to the shared DNS {region}-aiplatform.googleapis.com. The
   * limitation will be removed soon.
   *
   * @param bool $dedicatedEndpointEnabled
   */
  public function setDedicatedEndpointEnabled($dedicatedEndpointEnabled)
  {
    $this->dedicatedEndpointEnabled = $dedicatedEndpointEnabled;
  }
  /**
   * @return bool
   */
  public function getDedicatedEndpointEnabled()
  {
    return $this->dedicatedEndpointEnabled;
  }
  /**
   * Output only. The models deployed in this Endpoint. To add or remove
   * DeployedModels use EndpointService.DeployModel and
   * EndpointService.UndeployModel respectively.
   *
   * @param GoogleCloudAiplatformV1DeployedModel[] $deployedModels
   */
  public function setDeployedModels($deployedModels)
  {
    $this->deployedModels = $deployedModels;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedModel[]
   */
  public function getDeployedModels()
  {
    return $this->deployedModels;
  }
  /**
   * The description of the Endpoint.
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
   * Required. The display name of the Endpoint. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Deprecated: If true, expose the Endpoint via private service connect. Only
   * one of the fields, network or enable_private_service_connect, can be set.
   *
   * @deprecated
   * @param bool $enablePrivateServiceConnect
   */
  public function setEnablePrivateServiceConnect($enablePrivateServiceConnect)
  {
    $this->enablePrivateServiceConnect = $enablePrivateServiceConnect;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnablePrivateServiceConnect()
  {
    return $this->enablePrivateServiceConnect;
  }
  /**
   * Customer-managed encryption key spec for an Endpoint. If set, this Endpoint
   * and all sub-resources of this Endpoint will be secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Configures the Google Distributed Cloud (GDC) environment for online
   * prediction. Only set this field when the Endpoint is to be deployed in a
   * GDC environment.
   *
   * @param GoogleCloudAiplatformV1GdcConfig $gdcConfig
   */
  public function setGdcConfig(GoogleCloudAiplatformV1GdcConfig $gdcConfig)
  {
    $this->gdcConfig = $gdcConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GdcConfig
   */
  public function getGdcConfig()
  {
    return $this->gdcConfig;
  }
  /**
   * Optional. Configuration for GenAiAdvancedFeatures. If the endpoint is
   * serving GenAI models, advanced features like native RAG integration can be
   * configured. Currently, only Model Garden models are supported.
   *
   * @param GoogleCloudAiplatformV1GenAiAdvancedFeaturesConfig $genAiAdvancedFeaturesConfig
   */
  public function setGenAiAdvancedFeaturesConfig(GoogleCloudAiplatformV1GenAiAdvancedFeaturesConfig $genAiAdvancedFeaturesConfig)
  {
    $this->genAiAdvancedFeaturesConfig = $genAiAdvancedFeaturesConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenAiAdvancedFeaturesConfig
   */
  public function getGenAiAdvancedFeaturesConfig()
  {
    return $this->genAiAdvancedFeaturesConfig;
  }
  /**
   * The labels with user-defined metadata to organize your Endpoints. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. See https://goo.gl/xmQnxf for
   * more information and examples of labels.
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
   * Output only. Resource name of the Model Monitoring job associated with this
   * Endpoint if monitoring is enabled by
   * JobService.CreateModelDeploymentMonitoringJob. Format: `projects/{project}/
   * locations/{location}/modelDeploymentMonitoringJobs/{model_deployment_monito
   * ring_job}`
   *
   * @param string $modelDeploymentMonitoringJob
   */
  public function setModelDeploymentMonitoringJob($modelDeploymentMonitoringJob)
  {
    $this->modelDeploymentMonitoringJob = $modelDeploymentMonitoringJob;
  }
  /**
   * @return string
   */
  public function getModelDeploymentMonitoringJob()
  {
    return $this->modelDeploymentMonitoringJob;
  }
  /**
   * Output only. The resource name of the Endpoint.
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
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com//compute/docs/networks-and-
   * firewalls#networks) to which the Endpoint should be peered. Private
   * services access must already be configured for the network. If left
   * unspecified, the Endpoint is not peered with any network. Only one of the
   * fields, network or enable_private_service_connect, can be set. [Format](htt
   * ps://cloud.google.com/compute/docs/reference/rest/v1/networks/insert):
   * `projects/{project}/global/networks/{network}`. Where `{project}` is a
   * project number, as in `12345`, and `{network}` is network name.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Configures the request-response logging for online prediction.
   *
   * @param GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig $predictRequestResponseLoggingConfig
   */
  public function setPredictRequestResponseLoggingConfig(GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig $predictRequestResponseLoggingConfig)
  {
    $this->predictRequestResponseLoggingConfig = $predictRequestResponseLoggingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig
   */
  public function getPredictRequestResponseLoggingConfig()
  {
    return $this->predictRequestResponseLoggingConfig;
  }
  /**
   * Optional. Configuration for private service connect. network and
   * private_service_connect_config are mutually exclusive.
   *
   * @param GoogleCloudAiplatformV1PrivateServiceConnectConfig $privateServiceConnectConfig
   */
  public function setPrivateServiceConnectConfig(GoogleCloudAiplatformV1PrivateServiceConnectConfig $privateServiceConnectConfig)
  {
    $this->privateServiceConnectConfig = $privateServiceConnectConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PrivateServiceConnectConfig
   */
  public function getPrivateServiceConnectConfig()
  {
    return $this->privateServiceConnectConfig;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * A map from a DeployedModel's ID to the percentage of this Endpoint's
   * traffic that should be forwarded to that DeployedModel. If a
   * DeployedModel's ID is not listed in this map, then it receives no traffic.
   * The traffic percentage values must add up to 100, or map must be empty if
   * the Endpoint is to not accept any traffic at a moment.
   *
   * @param int[] $trafficSplit
   */
  public function setTrafficSplit($trafficSplit)
  {
    $this->trafficSplit = $trafficSplit;
  }
  /**
   * @return int[]
   */
  public function getTrafficSplit()
  {
    return $this->trafficSplit;
  }
  /**
   * Output only. Timestamp when this Endpoint was last updated.
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
class_alias(GoogleCloudAiplatformV1Endpoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Endpoint');
