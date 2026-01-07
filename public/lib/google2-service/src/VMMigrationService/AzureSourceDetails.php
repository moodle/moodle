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

namespace Google\Service\VMMigrationService;

class AzureSourceDetails extends \Google\Model
{
  /**
   * The state is unknown. This is used for API compatibility only and is not
   * used by the system.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The state was not sampled by the health checks yet.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The source is available but might not be usable yet due to invalid
   * credentials or another reason. The error message will contain further
   * details.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The source exists and its credentials were verified.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Immutable. The Azure location (region) that the source VMs will be migrated
   * from.
   *
   * @var string
   */
  public $azureLocation;
  protected $clientSecretCredsType = ClientSecretCredentials::class;
  protected $clientSecretCredsDataType = '';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * User specified tags to add to every M2VM generated resource in Azure. These
   * tags will be set in addition to the default tags that are set as part of
   * the migration process. The tags must not begin with the reserved prefix
   * `m4ce` or `m2vm`.
   *
   * @var string[]
   */
  public $migrationResourcesUserTags;
  /**
   * Output only. The ID of the Azure resource group that contains all resources
   * related to the migration process of this source.
   *
   * @var string
   */
  public $resourceGroupId;
  /**
   * Output only. State of the source as determined by the health check.
   *
   * @var string
   */
  public $state;
  /**
   * Immutable. Azure subscription ID.
   *
   * @var string
   */
  public $subscriptionId;

  /**
   * Immutable. The Azure location (region) that the source VMs will be migrated
   * from.
   *
   * @param string $azureLocation
   */
  public function setAzureLocation($azureLocation)
  {
    $this->azureLocation = $azureLocation;
  }
  /**
   * @return string
   */
  public function getAzureLocation()
  {
    return $this->azureLocation;
  }
  /**
   * Azure Credentials using tenant ID, client ID and secret.
   *
   * @param ClientSecretCredentials $clientSecretCreds
   */
  public function setClientSecretCreds(ClientSecretCredentials $clientSecretCreds)
  {
    $this->clientSecretCreds = $clientSecretCreds;
  }
  /**
   * @return ClientSecretCredentials
   */
  public function getClientSecretCreds()
  {
    return $this->clientSecretCreds;
  }
  /**
   * Output only. Provides details on the state of the Source in case of an
   * error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * User specified tags to add to every M2VM generated resource in Azure. These
   * tags will be set in addition to the default tags that are set as part of
   * the migration process. The tags must not begin with the reserved prefix
   * `m4ce` or `m2vm`.
   *
   * @param string[] $migrationResourcesUserTags
   */
  public function setMigrationResourcesUserTags($migrationResourcesUserTags)
  {
    $this->migrationResourcesUserTags = $migrationResourcesUserTags;
  }
  /**
   * @return string[]
   */
  public function getMigrationResourcesUserTags()
  {
    return $this->migrationResourcesUserTags;
  }
  /**
   * Output only. The ID of the Azure resource group that contains all resources
   * related to the migration process of this source.
   *
   * @param string $resourceGroupId
   */
  public function setResourceGroupId($resourceGroupId)
  {
    $this->resourceGroupId = $resourceGroupId;
  }
  /**
   * @return string
   */
  public function getResourceGroupId()
  {
    return $this->resourceGroupId;
  }
  /**
   * Output only. State of the source as determined by the health check.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, FAILED, ACTIVE
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
   * Immutable. Azure subscription ID.
   *
   * @param string $subscriptionId
   */
  public function setSubscriptionId($subscriptionId)
  {
    $this->subscriptionId = $subscriptionId;
  }
  /**
   * @return string
   */
  public function getSubscriptionId()
  {
    return $this->subscriptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureSourceDetails::class, 'Google_Service_VMMigrationService_AzureSourceDetails');
