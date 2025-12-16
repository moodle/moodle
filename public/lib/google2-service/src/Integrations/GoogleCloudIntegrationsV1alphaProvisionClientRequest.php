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

class GoogleCloudIntegrationsV1alphaProvisionClientRequest extends \Google\Model
{
  protected $cloudKmsConfigType = GoogleCloudIntegrationsV1alphaCloudKmsConfig::class;
  protected $cloudKmsConfigDataType = '';
  /**
   * Optional. Indicates if sample workflow should be created along with
   * provisioning
   *
   * @var bool
   */
  public $createSampleWorkflows;
  /**
   * Optional. Indicates if the client should be allowed to make HTTP calls.
   *
   * @var bool
   */
  public $enableHttpCall;
  /**
   * Optional. Indicates if the client should be allowed to use managed AI
   * features, i.e. using Cloud Companion APIs of the tenant project. This will
   * allow the customers to use features like Troubleshooting, OpenAPI spec
   * enrichment, etc. for free.
   *
   * @var bool
   */
  public $enableManagedAiFeatures;
  /**
   * Optional. Deprecated. Indicates provision with GMEK or CMEK. This field is
   * deprecated and the provision would always be GMEK if cloud_kms_config is
   * not present in the request.
   *
   * @deprecated
   * @var bool
   */
  public $provisionGmek;
  /**
   * Optional. User input run-as service account, if empty, will bring up a new
   * default service account
   *
   * @var string
   */
  public $runAsServiceAccount;
  /**
   * Optional. Indicates if skip CP provision or not
   *
   * @var bool
   */
  public $skipCpProvision;

  /**
   * Optional. OPTIONAL: Cloud KMS config for AuthModule to encrypt/decrypt
   * credentials.
   *
   * @param GoogleCloudIntegrationsV1alphaCloudKmsConfig $cloudKmsConfig
   */
  public function setCloudKmsConfig(GoogleCloudIntegrationsV1alphaCloudKmsConfig $cloudKmsConfig)
  {
    $this->cloudKmsConfig = $cloudKmsConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCloudKmsConfig
   */
  public function getCloudKmsConfig()
  {
    return $this->cloudKmsConfig;
  }
  /**
   * Optional. Indicates if sample workflow should be created along with
   * provisioning
   *
   * @param bool $createSampleWorkflows
   */
  public function setCreateSampleWorkflows($createSampleWorkflows)
  {
    $this->createSampleWorkflows = $createSampleWorkflows;
  }
  /**
   * @return bool
   */
  public function getCreateSampleWorkflows()
  {
    return $this->createSampleWorkflows;
  }
  /**
   * Optional. Indicates if the client should be allowed to make HTTP calls.
   *
   * @param bool $enableHttpCall
   */
  public function setEnableHttpCall($enableHttpCall)
  {
    $this->enableHttpCall = $enableHttpCall;
  }
  /**
   * @return bool
   */
  public function getEnableHttpCall()
  {
    return $this->enableHttpCall;
  }
  /**
   * Optional. Indicates if the client should be allowed to use managed AI
   * features, i.e. using Cloud Companion APIs of the tenant project. This will
   * allow the customers to use features like Troubleshooting, OpenAPI spec
   * enrichment, etc. for free.
   *
   * @param bool $enableManagedAiFeatures
   */
  public function setEnableManagedAiFeatures($enableManagedAiFeatures)
  {
    $this->enableManagedAiFeatures = $enableManagedAiFeatures;
  }
  /**
   * @return bool
   */
  public function getEnableManagedAiFeatures()
  {
    return $this->enableManagedAiFeatures;
  }
  /**
   * Optional. Deprecated. Indicates provision with GMEK or CMEK. This field is
   * deprecated and the provision would always be GMEK if cloud_kms_config is
   * not present in the request.
   *
   * @deprecated
   * @param bool $provisionGmek
   */
  public function setProvisionGmek($provisionGmek)
  {
    $this->provisionGmek = $provisionGmek;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getProvisionGmek()
  {
    return $this->provisionGmek;
  }
  /**
   * Optional. User input run-as service account, if empty, will bring up a new
   * default service account
   *
   * @param string $runAsServiceAccount
   */
  public function setRunAsServiceAccount($runAsServiceAccount)
  {
    $this->runAsServiceAccount = $runAsServiceAccount;
  }
  /**
   * @return string
   */
  public function getRunAsServiceAccount()
  {
    return $this->runAsServiceAccount;
  }
  /**
   * Optional. Indicates if skip CP provision or not
   *
   * @param bool $skipCpProvision
   */
  public function setSkipCpProvision($skipCpProvision)
  {
    $this->skipCpProvision = $skipCpProvision;
  }
  /**
   * @return bool
   */
  public function getSkipCpProvision()
  {
    return $this->skipCpProvision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaProvisionClientRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaProvisionClientRequest');
