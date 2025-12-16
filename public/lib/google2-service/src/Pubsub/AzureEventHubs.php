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

namespace Google\Service\Pubsub;

class AzureEventHubs extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Ingestion is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Permission denied encountered while consuming data from Event Hubs. This
   * can happen when `client_id`, or `tenant_id` are invalid. Or the right
   * permissions haven't been granted.
   */
  public const STATE_EVENT_HUBS_PERMISSION_DENIED = 'EVENT_HUBS_PERMISSION_DENIED';
  /**
   * Permission denied encountered while publishing to the topic.
   */
  public const STATE_PUBLISH_PERMISSION_DENIED = 'PUBLISH_PERMISSION_DENIED';
  /**
   * The provided Event Hubs namespace couldn't be found.
   */
  public const STATE_NAMESPACE_NOT_FOUND = 'NAMESPACE_NOT_FOUND';
  /**
   * The provided Event Hub couldn't be found.
   */
  public const STATE_EVENT_HUB_NOT_FOUND = 'EVENT_HUB_NOT_FOUND';
  /**
   * The provided Event Hubs subscription couldn't be found.
   */
  public const STATE_SUBSCRIPTION_NOT_FOUND = 'SUBSCRIPTION_NOT_FOUND';
  /**
   * The provided Event Hubs resource group couldn't be found.
   */
  public const STATE_RESOURCE_GROUP_NOT_FOUND = 'RESOURCE_GROUP_NOT_FOUND';
  /**
   * Optional. The client id of the Azure application that is being used to
   * authenticate Pub/Sub.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. The name of the Event Hub.
   *
   * @var string
   */
  public $eventHub;
  /**
   * Optional. The GCP service account to be used for Federated Identity
   * authentication.
   *
   * @var string
   */
  public $gcpServiceAccount;
  /**
   * Optional. The name of the Event Hubs namespace.
   *
   * @var string
   */
  public $namespace;
  /**
   * Optional. Name of the resource group within the azure subscription.
   *
   * @var string
   */
  public $resourceGroup;
  /**
   * Output only. An output-only field that indicates the state of the Event
   * Hubs ingestion source.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The Azure subscription id.
   *
   * @var string
   */
  public $subscriptionId;
  /**
   * Optional. The tenant id of the Azure application that is being used to
   * authenticate Pub/Sub.
   *
   * @var string
   */
  public $tenantId;

  /**
   * Optional. The client id of the Azure application that is being used to
   * authenticate Pub/Sub.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Optional. The name of the Event Hub.
   *
   * @param string $eventHub
   */
  public function setEventHub($eventHub)
  {
    $this->eventHub = $eventHub;
  }
  /**
   * @return string
   */
  public function getEventHub()
  {
    return $this->eventHub;
  }
  /**
   * Optional. The GCP service account to be used for Federated Identity
   * authentication.
   *
   * @param string $gcpServiceAccount
   */
  public function setGcpServiceAccount($gcpServiceAccount)
  {
    $this->gcpServiceAccount = $gcpServiceAccount;
  }
  /**
   * @return string
   */
  public function getGcpServiceAccount()
  {
    return $this->gcpServiceAccount;
  }
  /**
   * Optional. The name of the Event Hubs namespace.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
  /**
   * Optional. Name of the resource group within the azure subscription.
   *
   * @param string $resourceGroup
   */
  public function setResourceGroup($resourceGroup)
  {
    $this->resourceGroup = $resourceGroup;
  }
  /**
   * @return string
   */
  public function getResourceGroup()
  {
    return $this->resourceGroup;
  }
  /**
   * Output only. An output-only field that indicates the state of the Event
   * Hubs ingestion source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, EVENT_HUBS_PERMISSION_DENIED,
   * PUBLISH_PERMISSION_DENIED, NAMESPACE_NOT_FOUND, EVENT_HUB_NOT_FOUND,
   * SUBSCRIPTION_NOT_FOUND, RESOURCE_GROUP_NOT_FOUND
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
   * Optional. The Azure subscription id.
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
  /**
   * Optional. The tenant id of the Azure application that is being used to
   * authenticate Pub/Sub.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureEventHubs::class, 'Google_Service_Pubsub_AzureEventHubs');
