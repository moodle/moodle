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

namespace Google\Service\Storagetransfer;

class AzureBlobStorageData extends \Google\Model
{
  protected $azureCredentialsType = AzureCredentials::class;
  protected $azureCredentialsDataType = '';
  /**
   * Required. The container to transfer from the Azure Storage account.
   *
   * @var string
   */
  public $container;
  /**
   * Optional. The Resource name of a secret in Secret Manager. The Azure SAS
   * token must be stored in Secret Manager in JSON format: { "sas_token" :
   * "SAS_TOKEN" } GoogleServiceAccount must be granted
   * `roles/secretmanager.secretAccessor` for the resource. See [Configure
   * access to a source: Microsoft Azure Blob Storage]
   * (https://cloud.google.com/storage-transfer/docs/source-microsoft-
   * azure#secret_manager) for more information. If `credentials_secret` is
   * specified, do not specify azure_credentials. Format:
   * `projects/{project_number}/secrets/{secret_name}`
   *
   * @var string
   */
  public $credentialsSecret;
  protected $federatedIdentityConfigType = FederatedIdentityConfig::class;
  protected $federatedIdentityConfigDataType = '';
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'.
   *
   * @var string
   */
  public $path;
  /**
   * Service Directory Service to be used as the endpoint for transfers from a
   * custom VPC. Format: `projects/{project_id}/locations/{location}/namespaces/
   * {namespace}/services/{service}`
   *
   * @var string
   */
  public $privateNetworkService;
  /**
   * Required. The name of the Azure Storage account.
   *
   * @var string
   */
  public $storageAccount;

  /**
   * Required. Input only. Credentials used to authenticate API requests to
   * Azure. For information on our data retention policy for user credentials,
   * see [User credentials](/storage-transfer/docs/data-retention#user-
   * credentials).
   *
   * @param AzureCredentials $azureCredentials
   */
  public function setAzureCredentials(AzureCredentials $azureCredentials)
  {
    $this->azureCredentials = $azureCredentials;
  }
  /**
   * @return AzureCredentials
   */
  public function getAzureCredentials()
  {
    return $this->azureCredentials;
  }
  /**
   * Required. The container to transfer from the Azure Storage account.
   *
   * @param string $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }
  /**
   * @return string
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Optional. The Resource name of a secret in Secret Manager. The Azure SAS
   * token must be stored in Secret Manager in JSON format: { "sas_token" :
   * "SAS_TOKEN" } GoogleServiceAccount must be granted
   * `roles/secretmanager.secretAccessor` for the resource. See [Configure
   * access to a source: Microsoft Azure Blob Storage]
   * (https://cloud.google.com/storage-transfer/docs/source-microsoft-
   * azure#secret_manager) for more information. If `credentials_secret` is
   * specified, do not specify azure_credentials. Format:
   * `projects/{project_number}/secrets/{secret_name}`
   *
   * @param string $credentialsSecret
   */
  public function setCredentialsSecret($credentialsSecret)
  {
    $this->credentialsSecret = $credentialsSecret;
  }
  /**
   * @return string
   */
  public function getCredentialsSecret()
  {
    return $this->credentialsSecret;
  }
  /**
   * Optional. Federated identity config of a user registered Azure application.
   * If `federated_identity_config` is specified, do not specify
   * azure_credentials or credentials_secret.
   *
   * @param FederatedIdentityConfig $federatedIdentityConfig
   */
  public function setFederatedIdentityConfig(FederatedIdentityConfig $federatedIdentityConfig)
  {
    $this->federatedIdentityConfig = $federatedIdentityConfig;
  }
  /**
   * @return FederatedIdentityConfig
   */
  public function getFederatedIdentityConfig()
  {
    return $this->federatedIdentityConfig;
  }
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Service Directory Service to be used as the endpoint for transfers from a
   * custom VPC. Format: `projects/{project_id}/locations/{location}/namespaces/
   * {namespace}/services/{service}`
   *
   * @param string $privateNetworkService
   */
  public function setPrivateNetworkService($privateNetworkService)
  {
    $this->privateNetworkService = $privateNetworkService;
  }
  /**
   * @return string
   */
  public function getPrivateNetworkService()
  {
    return $this->privateNetworkService;
  }
  /**
   * Required. The name of the Azure Storage account.
   *
   * @param string $storageAccount
   */
  public function setStorageAccount($storageAccount)
  {
    $this->storageAccount = $storageAccount;
  }
  /**
   * @return string
   */
  public function getStorageAccount()
  {
    return $this->storageAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureBlobStorageData::class, 'Google_Service_Storagetransfer_AzureBlobStorageData');
