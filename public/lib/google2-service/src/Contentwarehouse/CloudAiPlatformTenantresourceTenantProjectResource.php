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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceTenantProjectResource extends \Google\Collection
{
  protected $collection_key = 'tenantServiceAccounts';
  protected $cloudSqlInstancesType = CloudAiPlatformTenantresourceCloudSqlInstanceConfig::class;
  protected $cloudSqlInstancesDataType = 'array';
  protected $gcsBucketsType = CloudAiPlatformTenantresourceGcsBucketConfig::class;
  protected $gcsBucketsDataType = 'array';
  protected $iamPolicyBindingsType = CloudAiPlatformTenantresourceIamPolicyBinding::class;
  protected $iamPolicyBindingsDataType = 'array';
  protected $infraSpannerConfigsType = CloudAiPlatformTenantresourceInfraSpannerConfig::class;
  protected $infraSpannerConfigsDataType = 'array';
  /**
   * Input/Output [Required]. The tag that uniquely identifies a tenant project
   * within a tenancy unit. Note: for the same tenant project tag, all tenant
   * manager operations should be idempotent.
   *
   * @var string
   */
  public $tag;
  protected $tenantProjectConfigType = CloudAiPlatformTenantresourceTenantProjectConfig::class;
  protected $tenantProjectConfigDataType = '';
  /**
   * Output only. The tenant project ID that has been created.
   *
   * @var string
   */
  public $tenantProjectId;
  /**
   * Output only. The tenant project number that has been created.
   *
   * @var string
   */
  public $tenantProjectNumber;
  protected $tenantServiceAccountsType = CloudAiPlatformTenantresourceTenantServiceAccountIdentity::class;
  protected $tenantServiceAccountsDataType = 'array';

  /**
   * The CloudSQL instances that are provisioned under the tenant project.
   *
   * @param CloudAiPlatformTenantresourceCloudSqlInstanceConfig[] $cloudSqlInstances
   */
  public function setCloudSqlInstances($cloudSqlInstances)
  {
    $this->cloudSqlInstances = $cloudSqlInstances;
  }
  /**
   * @return CloudAiPlatformTenantresourceCloudSqlInstanceConfig[]
   */
  public function getCloudSqlInstances()
  {
    return $this->cloudSqlInstances;
  }
  /**
   * The GCS buckets that are provisioned under the tenant project.
   *
   * @param CloudAiPlatformTenantresourceGcsBucketConfig[] $gcsBuckets
   */
  public function setGcsBuckets($gcsBuckets)
  {
    $this->gcsBuckets = $gcsBuckets;
  }
  /**
   * @return CloudAiPlatformTenantresourceGcsBucketConfig[]
   */
  public function getGcsBuckets()
  {
    return $this->gcsBuckets;
  }
  /**
   * The dynamic IAM bindings that are granted under the tenant project. Note:
   * this should only add new bindings to the project if they don't exist and
   * the existing bindings won't be affected.
   *
   * @param CloudAiPlatformTenantresourceIamPolicyBinding[] $iamPolicyBindings
   */
  public function setIamPolicyBindings($iamPolicyBindings)
  {
    $this->iamPolicyBindings = $iamPolicyBindings;
  }
  /**
   * @return CloudAiPlatformTenantresourceIamPolicyBinding[]
   */
  public function getIamPolicyBindings()
  {
    return $this->iamPolicyBindings;
  }
  /**
   * The Infra Spanner databases that are provisioned under the tenant project.
   * Note: this is an experimental feature.
   *
   * @param CloudAiPlatformTenantresourceInfraSpannerConfig[] $infraSpannerConfigs
   */
  public function setInfraSpannerConfigs($infraSpannerConfigs)
  {
    $this->infraSpannerConfigs = $infraSpannerConfigs;
  }
  /**
   * @return CloudAiPlatformTenantresourceInfraSpannerConfig[]
   */
  public function getInfraSpannerConfigs()
  {
    return $this->infraSpannerConfigs;
  }
  /**
   * Input/Output [Required]. The tag that uniquely identifies a tenant project
   * within a tenancy unit. Note: for the same tenant project tag, all tenant
   * manager operations should be idempotent.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * The configurations of a tenant project.
   *
   * @param CloudAiPlatformTenantresourceTenantProjectConfig $tenantProjectConfig
   */
  public function setTenantProjectConfig(CloudAiPlatformTenantresourceTenantProjectConfig $tenantProjectConfig)
  {
    $this->tenantProjectConfig = $tenantProjectConfig;
  }
  /**
   * @return CloudAiPlatformTenantresourceTenantProjectConfig
   */
  public function getTenantProjectConfig()
  {
    return $this->tenantProjectConfig;
  }
  /**
   * Output only. The tenant project ID that has been created.
   *
   * @param string $tenantProjectId
   */
  public function setTenantProjectId($tenantProjectId)
  {
    $this->tenantProjectId = $tenantProjectId;
  }
  /**
   * @return string
   */
  public function getTenantProjectId()
  {
    return $this->tenantProjectId;
  }
  /**
   * Output only. The tenant project number that has been created.
   *
   * @param string $tenantProjectNumber
   */
  public function setTenantProjectNumber($tenantProjectNumber)
  {
    $this->tenantProjectNumber = $tenantProjectNumber;
  }
  /**
   * @return string
   */
  public function getTenantProjectNumber()
  {
    return $this->tenantProjectNumber;
  }
  /**
   * The service account identities (or enabled API service's P4SA) that are
   * expclicitly created under the tenant project (before JIT provisioning
   * during enabled API services).
   *
   * @param CloudAiPlatformTenantresourceTenantServiceAccountIdentity[] $tenantServiceAccounts
   */
  public function setTenantServiceAccounts($tenantServiceAccounts)
  {
    $this->tenantServiceAccounts = $tenantServiceAccounts;
  }
  /**
   * @return CloudAiPlatformTenantresourceTenantServiceAccountIdentity[]
   */
  public function getTenantServiceAccounts()
  {
    return $this->tenantServiceAccounts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceTenantProjectResource::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceTenantProjectResource');
