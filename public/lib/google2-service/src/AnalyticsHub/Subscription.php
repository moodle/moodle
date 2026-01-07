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

namespace Google\Service\AnalyticsHub;

class Subscription extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const RESOURCE_TYPE_SHARED_RESOURCE_TYPE_UNSPECIFIED = 'SHARED_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * BigQuery Dataset Asset.
   */
  public const RESOURCE_TYPE_BIGQUERY_DATASET = 'BIGQUERY_DATASET';
  /**
   * Pub/Sub Topic Asset.
   */
  public const RESOURCE_TYPE_PUBSUB_TOPIC = 'PUBSUB_TOPIC';
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * This subscription is active and the data is accessible.
   */
  public const STATE_STATE_ACTIVE = 'STATE_ACTIVE';
  /**
   * The data referenced by this subscription is out of date and should be
   * refreshed. This can happen when a data provider adds or removes datasets.
   */
  public const STATE_STATE_STALE = 'STATE_STALE';
  /**
   * This subscription has been cancelled or revoked and the data is no longer
   * accessible.
   */
  public const STATE_STATE_INACTIVE = 'STATE_INACTIVE';
  protected $collection_key = 'linkedResources';
  protected $commercialInfoType = GoogleCloudBigqueryAnalyticshubV1SubscriptionCommercialInfo::class;
  protected $commercialInfoDataType = '';
  /**
   * Output only. Timestamp when the subscription was created.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. Resource name of the source Data Exchange. e.g.
   * projects/123/locations/us/dataExchanges/456
   *
   * @var string
   */
  public $dataExchange;
  protected $destinationDatasetType = DestinationDataset::class;
  protected $destinationDatasetDataType = '';
  /**
   * Output only. Timestamp when the subscription was last modified.
   *
   * @var string
   */
  public $lastModifyTime;
  protected $linkedDatasetMapType = LinkedResource::class;
  protected $linkedDatasetMapDataType = 'map';
  protected $linkedResourcesType = LinkedResource::class;
  protected $linkedResourcesDataType = 'array';
  /**
   * Output only. Resource name of the source Listing. e.g.
   * projects/123/locations/us/dataExchanges/456/listings/789
   *
   * @var string
   */
  public $listing;
  /**
   * Output only. By default, false. If true, the Subscriber agreed to the email
   * sharing mandate that is enabled for DataExchange/Listing.
   *
   * @var bool
   */
  public $logLinkedDatasetQueryUserEmail;
  /**
   * Output only. The resource name of the subscription. e.g.
   * `projects/myproject/locations/us/subscriptions/123`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Display name of the project of this subscription.
   *
   * @var string
   */
  public $organizationDisplayName;
  /**
   * Output only. Organization of the project this subscription belongs to.
   *
   * @var string
   */
  public $organizationId;
  /**
   * Output only. Listing shared asset type.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Output only. Current state of the subscription.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Email of the subscriber.
   *
   * @var string
   */
  public $subscriberContact;

  /**
   * Output only. This is set if this is a commercial subscription i.e. if this
   * subscription was created from subscribing to a commercial listing.
   *
   * @param GoogleCloudBigqueryAnalyticshubV1SubscriptionCommercialInfo $commercialInfo
   */
  public function setCommercialInfo(GoogleCloudBigqueryAnalyticshubV1SubscriptionCommercialInfo $commercialInfo)
  {
    $this->commercialInfo = $commercialInfo;
  }
  /**
   * @return GoogleCloudBigqueryAnalyticshubV1SubscriptionCommercialInfo
   */
  public function getCommercialInfo()
  {
    return $this->commercialInfo;
  }
  /**
   * Output only. Timestamp when the subscription was created.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. Resource name of the source Data Exchange. e.g.
   * projects/123/locations/us/dataExchanges/456
   *
   * @param string $dataExchange
   */
  public function setDataExchange($dataExchange)
  {
    $this->dataExchange = $dataExchange;
  }
  /**
   * @return string
   */
  public function getDataExchange()
  {
    return $this->dataExchange;
  }
  /**
   * Optional. BigQuery destination dataset to create for the subscriber.
   *
   * @param DestinationDataset $destinationDataset
   */
  public function setDestinationDataset(DestinationDataset $destinationDataset)
  {
    $this->destinationDataset = $destinationDataset;
  }
  /**
   * @return DestinationDataset
   */
  public function getDestinationDataset()
  {
    return $this->destinationDataset;
  }
  /**
   * Output only. Timestamp when the subscription was last modified.
   *
   * @param string $lastModifyTime
   */
  public function setLastModifyTime($lastModifyTime)
  {
    $this->lastModifyTime = $lastModifyTime;
  }
  /**
   * @return string
   */
  public function getLastModifyTime()
  {
    return $this->lastModifyTime;
  }
  /**
   * Output only. Map of listing resource names to associated linked resource,
   * e.g. projects/123/locations/us/dataExchanges/456/listings/789 ->
   * projects/123/datasets/my_dataset For listing-level subscriptions, this is a
   * map of size 1. Only contains values if state == STATE_ACTIVE.
   *
   * @param LinkedResource[] $linkedDatasetMap
   */
  public function setLinkedDatasetMap($linkedDatasetMap)
  {
    $this->linkedDatasetMap = $linkedDatasetMap;
  }
  /**
   * @return LinkedResource[]
   */
  public function getLinkedDatasetMap()
  {
    return $this->linkedDatasetMap;
  }
  /**
   * Output only. Linked resources created in the subscription. Only contains
   * values if state = STATE_ACTIVE.
   *
   * @param LinkedResource[] $linkedResources
   */
  public function setLinkedResources($linkedResources)
  {
    $this->linkedResources = $linkedResources;
  }
  /**
   * @return LinkedResource[]
   */
  public function getLinkedResources()
  {
    return $this->linkedResources;
  }
  /**
   * Output only. Resource name of the source Listing. e.g.
   * projects/123/locations/us/dataExchanges/456/listings/789
   *
   * @param string $listing
   */
  public function setListing($listing)
  {
    $this->listing = $listing;
  }
  /**
   * @return string
   */
  public function getListing()
  {
    return $this->listing;
  }
  /**
   * Output only. By default, false. If true, the Subscriber agreed to the email
   * sharing mandate that is enabled for DataExchange/Listing.
   *
   * @param bool $logLinkedDatasetQueryUserEmail
   */
  public function setLogLinkedDatasetQueryUserEmail($logLinkedDatasetQueryUserEmail)
  {
    $this->logLinkedDatasetQueryUserEmail = $logLinkedDatasetQueryUserEmail;
  }
  /**
   * @return bool
   */
  public function getLogLinkedDatasetQueryUserEmail()
  {
    return $this->logLinkedDatasetQueryUserEmail;
  }
  /**
   * Output only. The resource name of the subscription. e.g.
   * `projects/myproject/locations/us/subscriptions/123`.
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
   * Output only. Display name of the project of this subscription.
   *
   * @param string $organizationDisplayName
   */
  public function setOrganizationDisplayName($organizationDisplayName)
  {
    $this->organizationDisplayName = $organizationDisplayName;
  }
  /**
   * @return string
   */
  public function getOrganizationDisplayName()
  {
    return $this->organizationDisplayName;
  }
  /**
   * Output only. Organization of the project this subscription belongs to.
   *
   * @param string $organizationId
   */
  public function setOrganizationId($organizationId)
  {
    $this->organizationId = $organizationId;
  }
  /**
   * @return string
   */
  public function getOrganizationId()
  {
    return $this->organizationId;
  }
  /**
   * Output only. Listing shared asset type.
   *
   * Accepted values: SHARED_RESOURCE_TYPE_UNSPECIFIED, BIGQUERY_DATASET,
   * PUBSUB_TOPIC
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Output only. Current state of the subscription.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_ACTIVE, STATE_STALE,
   * STATE_INACTIVE
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
   * Output only. Email of the subscriber.
   *
   * @param string $subscriberContact
   */
  public function setSubscriberContact($subscriberContact)
  {
    $this->subscriberContact = $subscriberContact;
  }
  /**
   * @return string
   */
  public function getSubscriberContact()
  {
    return $this->subscriberContact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_AnalyticsHub_Subscription');
