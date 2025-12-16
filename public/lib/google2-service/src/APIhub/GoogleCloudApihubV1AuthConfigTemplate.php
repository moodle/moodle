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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1AuthConfigTemplate extends \Google\Collection
{
  protected $collection_key = 'supportedAuthTypes';
  protected $serviceAccountType = GoogleCloudApihubV1GoogleServiceAccountConfig::class;
  protected $serviceAccountDataType = '';
  /**
   * Required. The list of authentication types supported by the plugin.
   *
   * @var string[]
   */
  public $supportedAuthTypes;

  /**
   * Optional. The service account of the plugin hosting service. This service
   * account should be granted the required permissions on the Auth Config
   * parameters provided while creating the plugin instances corresponding to
   * this plugin. For example, if the plugin instance auth config requires a
   * secret manager secret, the service account should be granted the
   * secretmanager.versions.access permission on the corresponding secret, if
   * the plugin instance auth config contains a service account, the service
   * account should be granted the iam.serviceAccounts.getAccessToken permission
   * on the corresponding service account.
   *
   * @param GoogleCloudApihubV1GoogleServiceAccountConfig $serviceAccount
   */
  public function setServiceAccount(GoogleCloudApihubV1GoogleServiceAccountConfig $serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return GoogleCloudApihubV1GoogleServiceAccountConfig
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Required. The list of authentication types supported by the plugin.
   *
   * @param string[] $supportedAuthTypes
   */
  public function setSupportedAuthTypes($supportedAuthTypes)
  {
    $this->supportedAuthTypes = $supportedAuthTypes;
  }
  /**
   * @return string[]
   */
  public function getSupportedAuthTypes()
  {
    return $this->supportedAuthTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1AuthConfigTemplate::class, 'Google_Service_APIhub_GoogleCloudApihubV1AuthConfigTemplate');
