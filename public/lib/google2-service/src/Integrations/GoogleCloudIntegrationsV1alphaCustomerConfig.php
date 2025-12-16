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

class GoogleCloudIntegrationsV1alphaCustomerConfig extends \Google\Model
{
  protected $cloudKmsConfigType = GoogleCloudIntegrationsV1alphaCloudKmsConfig::class;
  protected $cloudKmsConfigDataType = '';
  /**
   * Optional. Indicates if the client should be allowed to make HTTP calls.
   * True if http call feature should be turned on for this region.
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
   * Optional. True if variable masking feature should be turned on for this
   * region.
   *
   * @var bool
   */
  public $enableVariableMasking;
  /**
   * Optional. Run-as service account to be updated for the provisioned client.
   *
   * @var string
   */
  public $runAsServiceAccount;

  /**
   * Optional. Cloud KMS config for Auth Module to encrypt/decrypt credentials.
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
   * Optional. Indicates if the client should be allowed to make HTTP calls.
   * True if http call feature should be turned on for this region.
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
   * Optional. True if variable masking feature should be turned on for this
   * region.
   *
   * @param bool $enableVariableMasking
   */
  public function setEnableVariableMasking($enableVariableMasking)
  {
    $this->enableVariableMasking = $enableVariableMasking;
  }
  /**
   * @return bool
   */
  public function getEnableVariableMasking()
  {
    return $this->enableVariableMasking;
  }
  /**
   * Optional. Run-as service account to be updated for the provisioned client.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCustomerConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCustomerConfig');
