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

namespace Google\Service\Apigateway;

class ApigatewayApiConfig extends \Google\Collection
{
  /**
   * API Config does not have a state yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * API Config is being created and deployed to the API Controller.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * API Config is ready for use by Gateways.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * API Config creation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * API Config is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * API Config is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * API Config settings are being activated in downstream systems. API Configs
   * in this state cannot be used by Gateways.
   */
  public const STATE_ACTIVATING = 'ACTIVATING';
  protected $collection_key = 'openapiDocuments';
  /**
   * Output only. Created time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The Google Cloud IAM Service Account that Gateways serving this
   * config should use to authenticate to other services. This may either be the
   * Service Account's email (`{ACCOUNT_ID}@{PROJECT}.iam.gserviceaccount.com`)
   * or its full resource name (`projects/{PROJECT}/accounts/{UNIQUE_ID}`). This
   * is most often used when the service is a GCP resource such as a Cloud Run
   * Service or an IAP-secured service.
   *
   * @var string
   */
  public $gatewayServiceAccount;
  protected $grpcServicesType = ApigatewayApiConfigGrpcServiceDefinition::class;
  protected $grpcServicesDataType = 'array';
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  protected $managedServiceConfigsType = ApigatewayApiConfigFile::class;
  protected $managedServiceConfigsDataType = 'array';
  /**
   * Output only. Resource name of the API Config. Format:
   * projects/{project}/locations/global/apis/{api}/configs/{api_config}
   *
   * @var string
   */
  public $name;
  protected $openapiDocumentsType = ApigatewayApiConfigOpenApiDocument::class;
  protected $openapiDocumentsDataType = 'array';
  /**
   * Output only. The ID of the associated Service Config (
   * https://cloud.google.com/service-infrastructure/docs/glossary#config).
   *
   * @var string
   */
  public $serviceConfigId;
  /**
   * Output only. State of the API Config.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Updated time.
   *
   * @var string
   */
  public $updateTime;

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
   * Optional. Display name.
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
   * Immutable. The Google Cloud IAM Service Account that Gateways serving this
   * config should use to authenticate to other services. This may either be the
   * Service Account's email (`{ACCOUNT_ID}@{PROJECT}.iam.gserviceaccount.com`)
   * or its full resource name (`projects/{PROJECT}/accounts/{UNIQUE_ID}`). This
   * is most often used when the service is a GCP resource such as a Cloud Run
   * Service or an IAP-secured service.
   *
   * @param string $gatewayServiceAccount
   */
  public function setGatewayServiceAccount($gatewayServiceAccount)
  {
    $this->gatewayServiceAccount = $gatewayServiceAccount;
  }
  /**
   * @return string
   */
  public function getGatewayServiceAccount()
  {
    return $this->gatewayServiceAccount;
  }
  /**
   * Optional. gRPC service definition files. If specified, openapi_documents
   * must not be included.
   *
   * @param ApigatewayApiConfigGrpcServiceDefinition[] $grpcServices
   */
  public function setGrpcServices($grpcServices)
  {
    $this->grpcServices = $grpcServices;
  }
  /**
   * @return ApigatewayApiConfigGrpcServiceDefinition[]
   */
  public function getGrpcServices()
  {
    return $this->grpcServices;
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
   * Optional. Service Configuration files. At least one must be included when
   * using gRPC service definitions. See
   * https://cloud.google.com/endpoints/docs/grpc/grpc-service-
   * config#service_configuration_overview for the expected file contents. If
   * multiple files are specified, the files are merged with the following
   * rules: * All singular scalar fields are merged using "last one wins"
   * semantics in the order of the files uploaded. * Repeated fields are
   * concatenated. * Singular embedded messages are merged using these rules for
   * nested fields.
   *
   * @param ApigatewayApiConfigFile[] $managedServiceConfigs
   */
  public function setManagedServiceConfigs($managedServiceConfigs)
  {
    $this->managedServiceConfigs = $managedServiceConfigs;
  }
  /**
   * @return ApigatewayApiConfigFile[]
   */
  public function getManagedServiceConfigs()
  {
    return $this->managedServiceConfigs;
  }
  /**
   * Output only. Resource name of the API Config. Format:
   * projects/{project}/locations/global/apis/{api}/configs/{api_config}
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
   * Optional. OpenAPI specification documents. If specified, grpc_services and
   * managed_service_configs must not be included.
   *
   * @param ApigatewayApiConfigOpenApiDocument[] $openapiDocuments
   */
  public function setOpenapiDocuments($openapiDocuments)
  {
    $this->openapiDocuments = $openapiDocuments;
  }
  /**
   * @return ApigatewayApiConfigOpenApiDocument[]
   */
  public function getOpenapiDocuments()
  {
    return $this->openapiDocuments;
  }
  /**
   * Output only. The ID of the associated Service Config (
   * https://cloud.google.com/service-infrastructure/docs/glossary#config).
   *
   * @param string $serviceConfigId
   */
  public function setServiceConfigId($serviceConfigId)
  {
    $this->serviceConfigId = $serviceConfigId;
  }
  /**
   * @return string
   */
  public function getServiceConfigId()
  {
    return $this->serviceConfigId;
  }
  /**
   * Output only. State of the API Config.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, DELETING,
   * UPDATING, ACTIVATING
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
class_alias(ApigatewayApiConfig::class, 'Google_Service_Apigateway_ApigatewayApiConfig');
