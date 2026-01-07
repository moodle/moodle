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

class GoogleCloudAiplatformV1ServiceAccountSpec extends \Google\Model
{
  /**
   * Required. If true, custom user-managed service account is enforced to run
   * any workloads (for example, Vertex Jobs) on the resource. Otherwise, uses
   * the [Vertex AI Custom Code Service Agent](https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents).
   *
   * @var bool
   */
  public $enableCustomServiceAccount;
  /**
   * Optional. Required when all below conditions are met *
   * `enable_custom_service_account` is true; * any runtime is specified via
   * `ResourceRuntimeSpec` on creation time, for example, Ray The users must
   * have `iam.serviceAccounts.actAs` permission on this service account and
   * then the specified runtime containers will run as it. Do not set this field
   * if you want to submit jobs using custom service account to this
   * PersistentResource after creation, but only specify the `service_account`
   * inside the job.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Required. If true, custom user-managed service account is enforced to run
   * any workloads (for example, Vertex Jobs) on the resource. Otherwise, uses
   * the [Vertex AI Custom Code Service Agent](https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents).
   *
   * @param bool $enableCustomServiceAccount
   */
  public function setEnableCustomServiceAccount($enableCustomServiceAccount)
  {
    $this->enableCustomServiceAccount = $enableCustomServiceAccount;
  }
  /**
   * @return bool
   */
  public function getEnableCustomServiceAccount()
  {
    return $this->enableCustomServiceAccount;
  }
  /**
   * Optional. Required when all below conditions are met *
   * `enable_custom_service_account` is true; * any runtime is specified via
   * `ResourceRuntimeSpec` on creation time, for example, Ray The users must
   * have `iam.serviceAccounts.actAs` permission on this service account and
   * then the specified runtime containers will run as it. Do not set this field
   * if you want to submit jobs using custom service account to this
   * PersistentResource after creation, but only specify the `service_account`
   * inside the job.
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
class_alias(GoogleCloudAiplatformV1ServiceAccountSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ServiceAccountSpec');
