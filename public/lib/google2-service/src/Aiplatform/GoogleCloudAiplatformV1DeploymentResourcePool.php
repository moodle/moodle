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

class GoogleCloudAiplatformV1DeploymentResourcePool extends \Google\Model
{
  /**
   * Output only. Timestamp when this DeploymentResourcePool was created.
   *
   * @var string
   */
  public $createTime;
  protected $dedicatedResourcesType = GoogleCloudAiplatformV1DedicatedResources::class;
  protected $dedicatedResourcesDataType = '';
  /**
   * If the DeploymentResourcePool is deployed with custom-trained Models or
   * AutoML Tabular Models, the container(s) of the DeploymentResourcePool will
   * send `stderr` and `stdout` streams to Cloud Logging by default. Please note
   * that the logs incur cost, which are subject to [Cloud Logging
   * pricing](https://cloud.google.com/logging/pricing). User can disable
   * container logging by setting this flag to true.
   *
   * @var bool
   */
  public $disableContainerLogging;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Immutable. The resource name of the DeploymentResourcePool. Format: `projec
   * ts/{project}/locations/{location}/deploymentResourcePools/{deployment_resou
   * rce_pool}`
   *
   * @var string
   */
  public $name;
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
   * The service account that the DeploymentResourcePool's container(s) run as.
   * Specify the email address of the service account. If this service account
   * is not specified, the container(s) run as a service account that doesn't
   * have access to the resource project. Users deploying the Models to this
   * DeploymentResourcePool must have the `iam.serviceAccounts.actAs` permission
   * on this service account.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Output only. Timestamp when this DeploymentResourcePool was created.
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
   * Required. The underlying DedicatedResources that the DeploymentResourcePool
   * uses.
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
   * If the DeploymentResourcePool is deployed with custom-trained Models or
   * AutoML Tabular Models, the container(s) of the DeploymentResourcePool will
   * send `stderr` and `stdout` streams to Cloud Logging by default. Please note
   * that the logs incur cost, which are subject to [Cloud Logging
   * pricing](https://cloud.google.com/logging/pricing). User can disable
   * container logging by setting this flag to true.
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
   * Customer-managed encryption key spec for a DeploymentResourcePool. If set,
   * this DeploymentResourcePool will be secured by this key. Endpoints and the
   * DeploymentResourcePool they deploy in need to have the same EncryptionSpec.
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
   * Immutable. The resource name of the DeploymentResourcePool. Format: `projec
   * ts/{project}/locations/{location}/deploymentResourcePools/{deployment_resou
   * rce_pool}`
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
   * The service account that the DeploymentResourcePool's container(s) run as.
   * Specify the email address of the service account. If this service account
   * is not specified, the container(s) run as a service account that doesn't
   * have access to the resource project. Users deploying the Models to this
   * DeploymentResourcePool must have the `iam.serviceAccounts.actAs` permission
   * on this service account.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeploymentResourcePool::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeploymentResourcePool');
