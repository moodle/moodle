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

class GoogleCloudIntegrationsV1alphaClientConfig extends \Google\Model
{
  /**
   * Unspecified billing type
   */
  public const BILLING_TYPE_BILLING_TYPE_UNSPECIFIED = 'BILLING_TYPE_UNSPECIFIED';
  /**
   * A trial org provisioned through Apigee Provisioning Wizard
   */
  public const BILLING_TYPE_BILLING_TYPE_APIGEE_TRIALS = 'BILLING_TYPE_APIGEE_TRIALS';
  /**
   * Subscription based users of Apigee
   */
  public const BILLING_TYPE_BILLING_TYPE_APIGEE_SUBSCRIPTION = 'BILLING_TYPE_APIGEE_SUBSCRIPTION';
  /**
   * Consumption based users of IP
   */
  public const BILLING_TYPE_BILLING_TYPE_PAYG = 'BILLING_TYPE_PAYG';
  /**
   * The client state is unspecified
   */
  public const CLIENT_STATE_CLIENT_STATE_UNSPECIFIED = 'CLIENT_STATE_UNSPECIFIED';
  /**
   * The client is active and able to make calls to the IP APIs
   */
  public const CLIENT_STATE_CLIENT_STATE_ACTIVE = 'CLIENT_STATE_ACTIVE';
  /**
   * The client is disabled and will soon be deleted
   */
  public const CLIENT_STATE_CLIENT_STATE_DISABLED = 'CLIENT_STATE_DISABLED';
  /**
   * Indicates the billing type of the client
   *
   * @var string
   */
  public $billingType;
  /**
   * Indicates the activity state the client
   *
   * @var string
   */
  public $clientState;
  protected $cloudKmsConfigType = GoogleCloudIntegrationsV1alphaCloudKmsConfig::class;
  protected $cloudKmsConfigDataType = '';
  /**
   * The timestamp when the client was first created.
   *
   * @var string
   */
  public $createTime;
  protected $customerConfigType = GoogleCloudIntegrationsV1alphaCustomerConfig::class;
  protected $customerConfigDataType = '';
  /**
   * Description of what the client is used for
   *
   * @var string
   */
  public $description;
  /**
   * Optional.
   *
   * @var bool
   */
  public $enableHttpCall;
  /**
   * Optional. Indicates the client enables internal IP feature, this is
   * applicable for internal clients only.
   *
   * @var bool
   */
  public $enableInternalIp;
  /**
   * Optional.
   *
   * @var bool
   */
  public $enableManagedAiFeatures;
  /**
   * Optional.
   *
   * @var bool
   */
  public $enableVariableMasking;
  /**
   * Globally unique ID (project_id + region)
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Indicates the client is provisioned with CMEK or GMEK.
   *
   * @var bool
   */
  public $isGmek;
  /**
   * The service agent associated with this client
   *
   * @var string
   */
  public $p4ServiceAccount;
  /**
   * The GCP project id of the client associated with
   *
   * @var string
   */
  public $projectId;
  /**
   * The region the client is linked to.
   *
   * @var string
   */
  public $region;
  /**
   * @var string
   */
  public $runAsServiceAccount;

  /**
   * Indicates the billing type of the client
   *
   * Accepted values: BILLING_TYPE_UNSPECIFIED, BILLING_TYPE_APIGEE_TRIALS,
   * BILLING_TYPE_APIGEE_SUBSCRIPTION, BILLING_TYPE_PAYG
   *
   * @param self::BILLING_TYPE_* $billingType
   */
  public function setBillingType($billingType)
  {
    $this->billingType = $billingType;
  }
  /**
   * @return self::BILLING_TYPE_*
   */
  public function getBillingType()
  {
    return $this->billingType;
  }
  /**
   * Indicates the activity state the client
   *
   * Accepted values: CLIENT_STATE_UNSPECIFIED, CLIENT_STATE_ACTIVE,
   * CLIENT_STATE_DISABLED
   *
   * @param self::CLIENT_STATE_* $clientState
   */
  public function setClientState($clientState)
  {
    $this->clientState = $clientState;
  }
  /**
   * @return self::CLIENT_STATE_*
   */
  public function getClientState()
  {
    return $this->clientState;
  }
  /**
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
   * The timestamp when the client was first created.
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
   * Optional. Customer configuration information for the given client.
   *
   * @param GoogleCloudIntegrationsV1alphaCustomerConfig $customerConfig
   */
  public function setCustomerConfig(GoogleCloudIntegrationsV1alphaCustomerConfig $customerConfig)
  {
    $this->customerConfig = $customerConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCustomerConfig
   */
  public function getCustomerConfig()
  {
    return $this->customerConfig;
  }
  /**
   * Description of what the client is used for
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
   * Optional.
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
   * Optional. Indicates the client enables internal IP feature, this is
   * applicable for internal clients only.
   *
   * @param bool $enableInternalIp
   */
  public function setEnableInternalIp($enableInternalIp)
  {
    $this->enableInternalIp = $enableInternalIp;
  }
  /**
   * @return bool
   */
  public function getEnableInternalIp()
  {
    return $this->enableInternalIp;
  }
  /**
   * Optional.
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
   * Optional.
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
   * Globally unique ID (project_id + region)
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
   * Optional. Indicates the client is provisioned with CMEK or GMEK.
   *
   * @param bool $isGmek
   */
  public function setIsGmek($isGmek)
  {
    $this->isGmek = $isGmek;
  }
  /**
   * @return bool
   */
  public function getIsGmek()
  {
    return $this->isGmek;
  }
  /**
   * The service agent associated with this client
   *
   * @param string $p4ServiceAccount
   */
  public function setP4ServiceAccount($p4ServiceAccount)
  {
    $this->p4ServiceAccount = $p4ServiceAccount;
  }
  /**
   * @return string
   */
  public function getP4ServiceAccount()
  {
    return $this->p4ServiceAccount;
  }
  /**
   * The GCP project id of the client associated with
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The region the client is linked to.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
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
class_alias(GoogleCloudIntegrationsV1alphaClientConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaClientConfig');
