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

class GoogleCloudAiplatformV1DeployedModel extends \Google\Model
{
  protected $automaticResourcesType = GoogleCloudAiplatformV1AutomaticResources::class;
  protected $automaticResourcesDataType = '';
  /**
   * The checkpoint id of the model.
   *
   * @var string
   */
  public $checkpointId;
  /**
   * Output only. Timestamp when the DeployedModel was created.
   *
   * @var string
   */
  public $createTime;
  protected $dedicatedResourcesType = GoogleCloudAiplatformV1DedicatedResources::class;
  protected $dedicatedResourcesDataType = '';
  /**
   * For custom-trained Models and AutoML Tabular Models, the container of the
   * DeployedModel instances will send `stderr` and `stdout` streams to Cloud
   * Logging by default. Please note that the logs incur cost, which are subject
   * to [Cloud Logging pricing](https://cloud.google.com/logging/pricing). User
   * can disable container logging by setting this flag to true.
   *
   * @var bool
   */
  public $disableContainerLogging;
  /**
   * If true, deploy the model without explainable feature, regardless the
   * existence of Model.explanation_spec or explanation_spec.
   *
   * @var bool
   */
  public $disableExplanations;
  /**
   * The display name of the DeployedModel. If not provided upon creation, the
   * Model's display_name is used.
   *
   * @var string
   */
  public $displayName;
  /**
   * If true, online prediction access logs are sent to Cloud Logging. These
   * logs are like standard server access logs, containing information like
   * timestamp and latency for each prediction request. Note that logs may incur
   * a cost, especially if your project receives prediction requests at a high
   * queries per second rate (QPS). Estimate your costs before enabling this
   * option.
   *
   * @var bool
   */
  public $enableAccessLogging;
  protected $explanationSpecType = GoogleCloudAiplatformV1ExplanationSpec::class;
  protected $explanationSpecDataType = '';
  protected $fasterDeploymentConfigType = GoogleCloudAiplatformV1FasterDeploymentConfig::class;
  protected $fasterDeploymentConfigDataType = '';
  /**
   * GDC pretrained / Gemini model name. The model name is a plain model name,
   * e.g. gemini-1.5-flash-002.
   *
   * @var string
   */
  public $gdcConnectedModel;
  /**
   * Immutable. The ID of the DeployedModel. If not provided upon deployment,
   * Vertex AI will generate a value for this ID. This value should be 1-10
   * characters, and valid characters are `/[0-9]/`.
   *
   * @var string
   */
  public $id;
  /**
   * The resource name of the Model that this is the deployment of. Note that
   * the Model may be in a different location than the DeployedModel's Endpoint.
   * The resource name may contain version id or version alias to specify the
   * version. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` if no
   * version is specified, the default version will be deployed.
   *
   * @var string
   */
  public $model;
  /**
   * Output only. The version ID of the model that is deployed.
   *
   * @var string
   */
  public $modelVersionId;
  protected $privateEndpointsType = GoogleCloudAiplatformV1PrivateEndpoints::class;
  protected $privateEndpointsDataType = '';
  /**
   * The service account that the DeployedModel's container runs as. Specify the
   * email address of the service account. If this service account is not
   * specified, the container runs as a service account that doesn't have access
   * to the resource project. Users deploying the Model must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * The resource name of the shared DeploymentResourcePool to deploy on.
   * Format: `projects/{project}/locations/{location}/deploymentResourcePools/{d
   * eployment_resource_pool}`
   *
   * @var string
   */
  public $sharedResources;
  protected $speculativeDecodingSpecType = GoogleCloudAiplatformV1SpeculativeDecodingSpec::class;
  protected $speculativeDecodingSpecDataType = '';
  protected $statusType = GoogleCloudAiplatformV1DeployedModelStatus::class;
  protected $statusDataType = '';
  /**
   * System labels to apply to Model Garden deployments. System labels are
   * managed by Google for internal use only.
   *
   * @var string[]
   */
  public $systemLabels;

  /**
   * A description of resources that to large degree are decided by Vertex AI,
   * and require only a modest additional configuration.
   *
   * @param GoogleCloudAiplatformV1AutomaticResources $automaticResources
   */
  public function setAutomaticResources(GoogleCloudAiplatformV1AutomaticResources $automaticResources)
  {
    $this->automaticResources = $automaticResources;
  }
  /**
   * @return GoogleCloudAiplatformV1AutomaticResources
   */
  public function getAutomaticResources()
  {
    return $this->automaticResources;
  }
  /**
   * The checkpoint id of the model.
   *
   * @param string $checkpointId
   */
  public function setCheckpointId($checkpointId)
  {
    $this->checkpointId = $checkpointId;
  }
  /**
   * @return string
   */
  public function getCheckpointId()
  {
    return $this->checkpointId;
  }
  /**
   * Output only. Timestamp when the DeployedModel was created.
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
   * A description of resources that are dedicated to the DeployedModel, and
   * that need a higher degree of manual configuration.
   *
   * @param GoogleCloudAiplatformV1DedicatedResources $dedicatedResources
   */
  public function setDedicatedResources(GoogleCloudAiplatformV1DedicatedResources $dedicatedResources)
  {
    $this->dedicatedResources = $dedicatedResources;
  }
  /**
   * @return GoogleCloudAiplatformV1DedicatedResources
   */
  public function getDedicatedResources()
  {
    return $this->dedicatedResources;
  }
  /**
   * For custom-trained Models and AutoML Tabular Models, the container of the
   * DeployedModel instances will send `stderr` and `stdout` streams to Cloud
   * Logging by default. Please note that the logs incur cost, which are subject
   * to [Cloud Logging pricing](https://cloud.google.com/logging/pricing). User
   * can disable container logging by setting this flag to true.
   *
   * @param bool $disableContainerLogging
   */
  public function setDisableContainerLogging($disableContainerLogging)
  {
    $this->disableContainerLogging = $disableContainerLogging;
  }
  /**
   * @return bool
   */
  public function getDisableContainerLogging()
  {
    return $this->disableContainerLogging;
  }
  /**
   * If true, deploy the model without explainable feature, regardless the
   * existence of Model.explanation_spec or explanation_spec.
   *
   * @param bool $disableExplanations
   */
  public function setDisableExplanations($disableExplanations)
  {
    $this->disableExplanations = $disableExplanations;
  }
  /**
   * @return bool
   */
  public function getDisableExplanations()
  {
    return $this->disableExplanations;
  }
  /**
   * The display name of the DeployedModel. If not provided upon creation, the
   * Model's display_name is used.
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
   * If true, online prediction access logs are sent to Cloud Logging. These
   * logs are like standard server access logs, containing information like
   * timestamp and latency for each prediction request. Note that logs may incur
   * a cost, especially if your project receives prediction requests at a high
   * queries per second rate (QPS). Estimate your costs before enabling this
   * option.
   *
   * @param bool $enableAccessLogging
   */
  public function setEnableAccessLogging($enableAccessLogging)
  {
    $this->enableAccessLogging = $enableAccessLogging;
  }
  /**
   * @return bool
   */
  public function getEnableAccessLogging()
  {
    return $this->enableAccessLogging;
  }
  /**
   * Explanation configuration for this DeployedModel. When deploying a Model
   * using EndpointService.DeployModel, this value overrides the value of
   * Model.explanation_spec. All fields of explanation_spec are optional in the
   * request. If a field of explanation_spec is not populated, the value of the
   * same field of Model.explanation_spec is inherited. If the corresponding
   * Model.explanation_spec is not populated, all fields of the explanation_spec
   * will be used for the explanation configuration.
   *
   * @param GoogleCloudAiplatformV1ExplanationSpec $explanationSpec
   */
  public function setExplanationSpec(GoogleCloudAiplatformV1ExplanationSpec $explanationSpec)
  {
    $this->explanationSpec = $explanationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationSpec
   */
  public function getExplanationSpec()
  {
    return $this->explanationSpec;
  }
  /**
   * Configuration for faster model deployment.
   *
   * @param GoogleCloudAiplatformV1FasterDeploymentConfig $fasterDeploymentConfig
   */
  public function setFasterDeploymentConfig(GoogleCloudAiplatformV1FasterDeploymentConfig $fasterDeploymentConfig)
  {
    $this->fasterDeploymentConfig = $fasterDeploymentConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FasterDeploymentConfig
   */
  public function getFasterDeploymentConfig()
  {
    return $this->fasterDeploymentConfig;
  }
  /**
   * GDC pretrained / Gemini model name. The model name is a plain model name,
   * e.g. gemini-1.5-flash-002.
   *
   * @param string $gdcConnectedModel
   */
  public function setGdcConnectedModel($gdcConnectedModel)
  {
    $this->gdcConnectedModel = $gdcConnectedModel;
  }
  /**
   * @return string
   */
  public function getGdcConnectedModel()
  {
    return $this->gdcConnectedModel;
  }
  /**
   * Immutable. The ID of the DeployedModel. If not provided upon deployment,
   * Vertex AI will generate a value for this ID. This value should be 1-10
   * characters, and valid characters are `/[0-9]/`.
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
   * The resource name of the Model that this is the deployment of. Note that
   * the Model may be in a different location than the DeployedModel's Endpoint.
   * The resource name may contain version id or version alias to specify the
   * version. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` if no
   * version is specified, the default version will be deployed.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output only. The version ID of the model that is deployed.
   *
   * @param string $modelVersionId
   */
  public function setModelVersionId($modelVersionId)
  {
    $this->modelVersionId = $modelVersionId;
  }
  /**
   * @return string
   */
  public function getModelVersionId()
  {
    return $this->modelVersionId;
  }
  /**
   * Output only. Provide paths for users to send predict/explain/health
   * requests directly to the deployed model services running on Cloud via
   * private services access. This field is populated if network is configured.
   *
   * @param GoogleCloudAiplatformV1PrivateEndpoints $privateEndpoints
   */
  public function setPrivateEndpoints(GoogleCloudAiplatformV1PrivateEndpoints $privateEndpoints)
  {
    $this->privateEndpoints = $privateEndpoints;
  }
  /**
   * @return GoogleCloudAiplatformV1PrivateEndpoints
   */
  public function getPrivateEndpoints()
  {
    return $this->privateEndpoints;
  }
  /**
   * The service account that the DeployedModel's container runs as. Specify the
   * email address of the service account. If this service account is not
   * specified, the container runs as a service account that doesn't have access
   * to the resource project. Users deploying the Model must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
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
   * The resource name of the shared DeploymentResourcePool to deploy on.
   * Format: `projects/{project}/locations/{location}/deploymentResourcePools/{d
   * eployment_resource_pool}`
   *
   * @param string $sharedResources
   */
  public function setSharedResources($sharedResources)
  {
    $this->sharedResources = $sharedResources;
  }
  /**
   * @return string
   */
  public function getSharedResources()
  {
    return $this->sharedResources;
  }
  /**
   * Optional. Spec for configuring speculative decoding.
   *
   * @param GoogleCloudAiplatformV1SpeculativeDecodingSpec $speculativeDecodingSpec
   */
  public function setSpeculativeDecodingSpec(GoogleCloudAiplatformV1SpeculativeDecodingSpec $speculativeDecodingSpec)
  {
    $this->speculativeDecodingSpec = $speculativeDecodingSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1SpeculativeDecodingSpec
   */
  public function getSpeculativeDecodingSpec()
  {
    return $this->speculativeDecodingSpec;
  }
  /**
   * Output only. Runtime status of the deployed model.
   *
   * @param GoogleCloudAiplatformV1DeployedModelStatus $status
   */
  public function setStatus(GoogleCloudAiplatformV1DeployedModelStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedModelStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * System labels to apply to Model Garden deployments. System labels are
   * managed by Google for internal use only.
   *
   * @param string[] $systemLabels
   */
  public function setSystemLabels($systemLabels)
  {
    $this->systemLabels = $systemLabels;
  }
  /**
   * @return string[]
   */
  public function getSystemLabels()
  {
    return $this->systemLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployedModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployedModel');
