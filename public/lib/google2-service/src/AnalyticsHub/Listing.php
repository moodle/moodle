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

class Listing extends \Google\Collection
{
  /**
   * Unspecified. Defaults to DISCOVERY_TYPE_PRIVATE.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_UNSPECIFIED = 'DISCOVERY_TYPE_UNSPECIFIED';
  /**
   * The Data exchange/listing can be discovered in the 'Private' results list.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_PRIVATE = 'DISCOVERY_TYPE_PRIVATE';
  /**
   * The Data exchange/listing can be discovered in the 'Public' results list.
   */
  public const DISCOVERY_TYPE_DISCOVERY_TYPE_PUBLIC = 'DISCOVERY_TYPE_PUBLIC';
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
   * Subscribable state. Users with dataexchange.listings.subscribe permission
   * can subscribe to this listing.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  protected $collection_key = 'categories';
  /**
   * Optional. If true, the listing is only available to get the resource
   * metadata. Listing is non subscribable.
   *
   * @var bool
   */
  public $allowOnlyMetadataSharing;
  protected $bigqueryDatasetType = BigQueryDatasetSource::class;
  protected $bigqueryDatasetDataType = '';
  /**
   * Optional. Categories of the listing. Up to five categories are allowed.
   *
   * @var string[]
   */
  public $categories;
  protected $commercialInfoType = GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo::class;
  protected $commercialInfoDataType = '';
  protected $dataProviderType = DataProvider::class;
  protected $dataProviderDataType = '';
  /**
   * Optional. Short description of the listing. The description must not
   * contain Unicode non-characters and C0 and C1 control codes except tabs
   * (HT), new lines (LF), carriage returns (CR), and page breaks (FF). Default
   * value is an empty string. Max length: 2000 bytes.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Type of discovery of the listing on the discovery page.
   *
   * @var string
   */
  public $discoveryType;
  /**
   * Required. Human-readable display name of the listing. The display name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), ampersands (&) and can't start or end with spaces. Default
   * value is an empty string. Max length: 63 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Documentation describing the listing.
   *
   * @var string
   */
  public $documentation;
  /**
   * Optional. Base64 encoded image representing the listing. Max Size: 3.0MiB
   * Expected image dimensions are 512x512 pixels, however the API only performs
   * validation on size of the encoded data. Note: For byte fields, the contents
   * of the field are base64-encoded (which increases the size of the data by
   * 33-36%) when using JSON on the wire.
   *
   * @var string
   */
  public $icon;
  /**
   * Optional. By default, false. If true, the Listing has an email sharing
   * mandate enabled.
   *
   * @var bool
   */
  public $logLinkedDatasetQueryUserEmail;
  /**
   * Output only. The resource name of the listing. e.g.
   * `projects/myproject/locations/us/dataExchanges/123/listings/456`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Email or URL of the primary point of contact of the listing. Max
   * Length: 1000 bytes.
   *
   * @var string
   */
  public $primaryContact;
  protected $publisherType = Publisher::class;
  protected $publisherDataType = '';
  protected $pubsubTopicType = PubSubTopicSource::class;
  protected $pubsubTopicDataType = '';
  /**
   * Optional. Email or URL of the request access of the listing. Subscribers
   * can use this reference to request access. Max Length: 1000 bytes.
   *
   * @var string
   */
  public $requestAccess;
  /**
   * Output only. Listing shared asset type.
   *
   * @var string
   */
  public $resourceType;
  protected $restrictedExportConfigType = RestrictedExportConfig::class;
  protected $restrictedExportConfigDataType = '';
  /**
   * Output only. Current state of the listing.
   *
   * @var string
   */
  public $state;
  protected $storedProcedureConfigType = StoredProcedureConfig::class;
  protected $storedProcedureConfigDataType = '';

  /**
   * Optional. If true, the listing is only available to get the resource
   * metadata. Listing is non subscribable.
   *
   * @param bool $allowOnlyMetadataSharing
   */
  public function setAllowOnlyMetadataSharing($allowOnlyMetadataSharing)
  {
    $this->allowOnlyMetadataSharing = $allowOnlyMetadataSharing;
  }
  /**
   * @return bool
   */
  public function getAllowOnlyMetadataSharing()
  {
    return $this->allowOnlyMetadataSharing;
  }
  /**
   * Shared dataset i.e. BigQuery dataset source.
   *
   * @param BigQueryDatasetSource $bigqueryDataset
   */
  public function setBigqueryDataset(BigQueryDatasetSource $bigqueryDataset)
  {
    $this->bigqueryDataset = $bigqueryDataset;
  }
  /**
   * @return BigQueryDatasetSource
   */
  public function getBigqueryDataset()
  {
    return $this->bigqueryDataset;
  }
  /**
   * Optional. Categories of the listing. Up to five categories are allowed.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Output only. Commercial info contains the information about the commercial
   * data products associated with the listing.
   *
   * @param GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo $commercialInfo
   */
  public function setCommercialInfo(GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo $commercialInfo)
  {
    $this->commercialInfo = $commercialInfo;
  }
  /**
   * @return GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfo
   */
  public function getCommercialInfo()
  {
    return $this->commercialInfo;
  }
  /**
   * Optional. Details of the data provider who owns the source data.
   *
   * @param DataProvider $dataProvider
   */
  public function setDataProvider(DataProvider $dataProvider)
  {
    $this->dataProvider = $dataProvider;
  }
  /**
   * @return DataProvider
   */
  public function getDataProvider()
  {
    return $this->dataProvider;
  }
  /**
   * Optional. Short description of the listing. The description must not
   * contain Unicode non-characters and C0 and C1 control codes except tabs
   * (HT), new lines (LF), carriage returns (CR), and page breaks (FF). Default
   * value is an empty string. Max length: 2000 bytes.
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
   * Optional. Type of discovery of the listing on the discovery page.
   *
   * Accepted values: DISCOVERY_TYPE_UNSPECIFIED, DISCOVERY_TYPE_PRIVATE,
   * DISCOVERY_TYPE_PUBLIC
   *
   * @param self::DISCOVERY_TYPE_* $discoveryType
   */
  public function setDiscoveryType($discoveryType)
  {
    $this->discoveryType = $discoveryType;
  }
  /**
   * @return self::DISCOVERY_TYPE_*
   */
  public function getDiscoveryType()
  {
    return $this->discoveryType;
  }
  /**
   * Required. Human-readable display name of the listing. The display name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), ampersands (&) and can't start or end with spaces. Default
   * value is an empty string. Max length: 63 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Documentation describing the listing.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. Base64 encoded image representing the listing. Max Size: 3.0MiB
   * Expected image dimensions are 512x512 pixels, however the API only performs
   * validation on size of the encoded data. Note: For byte fields, the contents
   * of the field are base64-encoded (which increases the size of the data by
   * 33-36%) when using JSON on the wire.
   *
   * @param string $icon
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return string
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * Optional. By default, false. If true, the Listing has an email sharing
   * mandate enabled.
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
   * Output only. The resource name of the listing. e.g.
   * `projects/myproject/locations/us/dataExchanges/123/listings/456`
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
   * Optional. Email or URL of the primary point of contact of the listing. Max
   * Length: 1000 bytes.
   *
   * @param string $primaryContact
   */
  public function setPrimaryContact($primaryContact)
  {
    $this->primaryContact = $primaryContact;
  }
  /**
   * @return string
   */
  public function getPrimaryContact()
  {
    return $this->primaryContact;
  }
  /**
   * Optional. Details of the publisher who owns the listing and who can share
   * the source data.
   *
   * @param Publisher $publisher
   */
  public function setPublisher(Publisher $publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return Publisher
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * Pub/Sub topic source.
   *
   * @param PubSubTopicSource $pubsubTopic
   */
  public function setPubsubTopic(PubSubTopicSource $pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return PubSubTopicSource
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
  /**
   * Optional. Email or URL of the request access of the listing. Subscribers
   * can use this reference to request access. Max Length: 1000 bytes.
   *
   * @param string $requestAccess
   */
  public function setRequestAccess($requestAccess)
  {
    $this->requestAccess = $requestAccess;
  }
  /**
   * @return string
   */
  public function getRequestAccess()
  {
    return $this->requestAccess;
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
   * Optional. If set, restricted export configuration will be propagated and
   * enforced on the linked dataset.
   *
   * @param RestrictedExportConfig $restrictedExportConfig
   */
  public function setRestrictedExportConfig(RestrictedExportConfig $restrictedExportConfig)
  {
    $this->restrictedExportConfig = $restrictedExportConfig;
  }
  /**
   * @return RestrictedExportConfig
   */
  public function getRestrictedExportConfig()
  {
    return $this->restrictedExportConfig;
  }
  /**
   * Output only. Current state of the listing.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE
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
   * Optional. If set, stored procedure configuration will be propagated and
   * enforced on the linked dataset.
   *
   * @param StoredProcedureConfig $storedProcedureConfig
   */
  public function setStoredProcedureConfig(StoredProcedureConfig $storedProcedureConfig)
  {
    $this->storedProcedureConfig = $storedProcedureConfig;
  }
  /**
   * @return StoredProcedureConfig
   */
  public function getStoredProcedureConfig()
  {
    return $this->storedProcedureConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Listing::class, 'Google_Service_AnalyticsHub_Listing');
