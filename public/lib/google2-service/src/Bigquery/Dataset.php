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

namespace Google\Service\Bigquery;

class Dataset extends \Google\Collection
{
  /**
   * Unspecified will default to using ROUND_HALF_AWAY_FROM_ZERO.
   */
  public const DEFAULT_ROUNDING_MODE_ROUNDING_MODE_UNSPECIFIED = 'ROUNDING_MODE_UNSPECIFIED';
  /**
   * ROUND_HALF_AWAY_FROM_ZERO rounds half values away from zero when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5, 1.6, 1.7, 1.8, 1.9 => 2
   */
  public const DEFAULT_ROUNDING_MODE_ROUND_HALF_AWAY_FROM_ZERO = 'ROUND_HALF_AWAY_FROM_ZERO';
  /**
   * ROUND_HALF_EVEN rounds half values to the nearest even value when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5 => 2 1.6, 1.7, 1.8, 1.9 => 2 2.5 => 2
   */
  public const DEFAULT_ROUNDING_MODE_ROUND_HALF_EVEN = 'ROUND_HALF_EVEN';
  /**
   * Value not set.
   */
  public const STORAGE_BILLING_MODEL_STORAGE_BILLING_MODEL_UNSPECIFIED = 'STORAGE_BILLING_MODEL_UNSPECIFIED';
  /**
   * Billing for logical bytes.
   */
  public const STORAGE_BILLING_MODEL_LOGICAL = 'LOGICAL';
  /**
   * Billing for physical bytes.
   */
  public const STORAGE_BILLING_MODEL_PHYSICAL = 'PHYSICAL';
  protected $collection_key = 'tags';
  protected $accessType = DatasetAccess::class;
  protected $accessDataType = 'array';
  /**
   * Output only. The time when this dataset was created, in milliseconds since
   * the epoch.
   *
   * @var string
   */
  public $creationTime;
  protected $datasetReferenceType = DatasetReference::class;
  protected $datasetReferenceDataType = '';
  /**
   * Optional. Defines the default collation specification of future tables
   * created in the dataset. If a table is created in this dataset without
   * table-level default collation, then the table inherits the dataset default
   * collation, which is applied to the string fields that do not have explicit
   * collation specified. A change to this field affects only tables created
   * afterwards, and does not alter the existing tables. The following values
   * are supported: * 'und:ci': undetermined locale, case insensitive. * '':
   * empty string. Default to case-sensitive behavior.
   *
   * @var string
   */
  public $defaultCollation;
  protected $defaultEncryptionConfigurationType = EncryptionConfiguration::class;
  protected $defaultEncryptionConfigurationDataType = '';
  /**
   * This default partition expiration, expressed in milliseconds. When new
   * time-partitioned tables are created in a dataset where this property is
   * set, the table will inherit this value, propagated as the
   * `TimePartitioning.expirationMs` property on the new table. If you set
   * `TimePartitioning.expirationMs` explicitly when creating a table, the
   * `defaultPartitionExpirationMs` of the containing dataset is ignored. When
   * creating a partitioned table, if `defaultPartitionExpirationMs` is set, the
   * `defaultTableExpirationMs` value is ignored and the table will not be
   * inherit a table expiration deadline.
   *
   * @var string
   */
  public $defaultPartitionExpirationMs;
  /**
   * Optional. Defines the default rounding mode specification of new tables
   * created within this dataset. During table creation, if this field is
   * specified, the table within this dataset will inherit the default rounding
   * mode of the dataset. Setting the default rounding mode on a table overrides
   * this option. Existing tables in the dataset are unaffected. If columns are
   * defined during that table creation, they will immediately inherit the
   * table's default rounding mode, unless otherwise specified.
   *
   * @var string
   */
  public $defaultRoundingMode;
  /**
   * Optional. The default lifetime of all tables in the dataset, in
   * milliseconds. The minimum lifetime value is 3600000 milliseconds (one
   * hour). To clear an existing default expiration with a PATCH request, set to
   * 0. Once this property is set, all newly-created tables in the dataset will
   * have an expirationTime property set to the creation time plus the value in
   * this property, and changing the value will only affect new tables, not
   * existing ones. When the expirationTime for a given table is reached, that
   * table will be deleted automatically. If a table's expirationTime is
   * modified or removed before the table expires, or if you provide an explicit
   * expirationTime when creating a table, that value takes precedence over the
   * default expiration time indicated by this property.
   *
   * @var string
   */
  public $defaultTableExpirationMs;
  /**
   * Optional. A user-friendly description of the dataset.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. A hash of the resource.
   *
   * @var string
   */
  public $etag;
  protected $externalCatalogDatasetOptionsType = ExternalCatalogDatasetOptions::class;
  protected $externalCatalogDatasetOptionsDataType = '';
  protected $externalDatasetReferenceType = ExternalDatasetReference::class;
  protected $externalDatasetReferenceDataType = '';
  /**
   * Optional. A descriptive name for the dataset.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * Output only. The fully-qualified unique name of the dataset in the format
   * projectId:datasetId. The dataset name without the project name is given in
   * the datasetId field. When creating a new dataset, leave this field blank,
   * and instead specify the datasetId field.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. TRUE if the dataset and its table names are case-insensitive,
   * otherwise FALSE. By default, this is FALSE, which means the dataset and its
   * table names are case-sensitive. This field does not affect routine
   * references.
   *
   * @var bool
   */
  public $isCaseInsensitive;
  /**
   * Output only. The resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * The labels associated with this dataset. You can use these to organize and
   * group your datasets. You can set this property when inserting or updating a
   * dataset. See [Creating and Updating Dataset
   * Labels](https://cloud.google.com/bigquery/docs/creating-managing-
   * labels#creating_and_updating_dataset_labels) for more information.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The date when this dataset was last modified, in milliseconds
   * since the epoch.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $linkedDatasetMetadataType = LinkedDatasetMetadata::class;
  protected $linkedDatasetMetadataDataType = '';
  protected $linkedDatasetSourceType = LinkedDatasetSource::class;
  protected $linkedDatasetSourceDataType = '';
  /**
   * The geographic location where the dataset should reside. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. Defines the time travel window in hours. The value can be from 48
   * to 168 hours (2 to 7 days). The default value is 168 hours if this is not
   * set.
   *
   * @var string
   */
  public $maxTimeTravelHours;
  /**
   * Optional. The [tags](https://cloud.google.com/bigquery/docs/tags) attached
   * to this dataset. Tag keys are globally unique. Tag key is expected to be in
   * the namespaced format, for example "123456789012/environment" where
   * 123456789012 is the ID of the parent organization or project resource for
   * this tag key. Tag value is expected to be the short name, for example
   * "Production". See [Tag definitions](https://cloud.google.com/iam/docs/tags-
   * access-control#definitions) for more details.
   *
   * @var string[]
   */
  public $resourceTags;
  protected $restrictionsType = RestrictionConfig::class;
  protected $restrictionsDataType = '';
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
   * Output only. A URL that can be used to access the resource again. You can
   * use this URL in Get or Update requests to the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Optional. Updates storage_billing_model for the dataset.
   *
   * @var string
   */
  public $storageBillingModel;
  protected $tagsType = DatasetTags::class;
  protected $tagsDataType = 'array';
  /**
   * Output only. Same as `type` in `ListFormatDataset`. The type of the
   * dataset, one of: * DEFAULT - only accessible by owner and authorized
   * accounts, * PUBLIC - accessible by everyone, * LINKED - linked dataset, *
   * EXTERNAL - dataset with definition in external metadata catalog.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. An array of objects that define dataset access for one or more
   * entities. You can set this property when inserting or updating a dataset in
   * order to control who is allowed to access the data. If unspecified at
   * dataset creation time, BigQuery adds default dataset access for the
   * following entities: access.specialGroup: projectReaders; access.role:
   * READER; access.specialGroup: projectWriters; access.role: WRITER;
   * access.specialGroup: projectOwners; access.role: OWNER; access.userByEmail:
   * [dataset creator email]; access.role: OWNER; If you patch a dataset, then
   * this field is overwritten by the patched dataset's access field. To add
   * entities, you must supply the entire existing access array in addition to
   * any new entities that you want to add.
   *
   * @param DatasetAccess[] $access
   */
  public function setAccess($access)
  {
    $this->access = $access;
  }
  /**
   * @return DatasetAccess[]
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * Output only. The time when this dataset was created, in milliseconds since
   * the epoch.
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
   * Required. A reference that identifies the dataset.
   *
   * @param DatasetReference $datasetReference
   */
  public function setDatasetReference(DatasetReference $datasetReference)
  {
    $this->datasetReference = $datasetReference;
  }
  /**
   * @return DatasetReference
   */
  public function getDatasetReference()
  {
    return $this->datasetReference;
  }
  /**
   * Optional. Defines the default collation specification of future tables
   * created in the dataset. If a table is created in this dataset without
   * table-level default collation, then the table inherits the dataset default
   * collation, which is applied to the string fields that do not have explicit
   * collation specified. A change to this field affects only tables created
   * afterwards, and does not alter the existing tables. The following values
   * are supported: * 'und:ci': undetermined locale, case insensitive. * '':
   * empty string. Default to case-sensitive behavior.
   *
   * @param string $defaultCollation
   */
  public function setDefaultCollation($defaultCollation)
  {
    $this->defaultCollation = $defaultCollation;
  }
  /**
   * @return string
   */
  public function getDefaultCollation()
  {
    return $this->defaultCollation;
  }
  /**
   * The default encryption key for all tables in the dataset. After this
   * property is set, the encryption key of all newly-created tables in the
   * dataset is set to this value unless the table creation request or query
   * explicitly overrides the key.
   *
   * @param EncryptionConfiguration $defaultEncryptionConfiguration
   */
  public function setDefaultEncryptionConfiguration(EncryptionConfiguration $defaultEncryptionConfiguration)
  {
    $this->defaultEncryptionConfiguration = $defaultEncryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getDefaultEncryptionConfiguration()
  {
    return $this->defaultEncryptionConfiguration;
  }
  /**
   * This default partition expiration, expressed in milliseconds. When new
   * time-partitioned tables are created in a dataset where this property is
   * set, the table will inherit this value, propagated as the
   * `TimePartitioning.expirationMs` property on the new table. If you set
   * `TimePartitioning.expirationMs` explicitly when creating a table, the
   * `defaultPartitionExpirationMs` of the containing dataset is ignored. When
   * creating a partitioned table, if `defaultPartitionExpirationMs` is set, the
   * `defaultTableExpirationMs` value is ignored and the table will not be
   * inherit a table expiration deadline.
   *
   * @param string $defaultPartitionExpirationMs
   */
  public function setDefaultPartitionExpirationMs($defaultPartitionExpirationMs)
  {
    $this->defaultPartitionExpirationMs = $defaultPartitionExpirationMs;
  }
  /**
   * @return string
   */
  public function getDefaultPartitionExpirationMs()
  {
    return $this->defaultPartitionExpirationMs;
  }
  /**
   * Optional. Defines the default rounding mode specification of new tables
   * created within this dataset. During table creation, if this field is
   * specified, the table within this dataset will inherit the default rounding
   * mode of the dataset. Setting the default rounding mode on a table overrides
   * this option. Existing tables in the dataset are unaffected. If columns are
   * defined during that table creation, they will immediately inherit the
   * table's default rounding mode, unless otherwise specified.
   *
   * Accepted values: ROUNDING_MODE_UNSPECIFIED, ROUND_HALF_AWAY_FROM_ZERO,
   * ROUND_HALF_EVEN
   *
   * @param self::DEFAULT_ROUNDING_MODE_* $defaultRoundingMode
   */
  public function setDefaultRoundingMode($defaultRoundingMode)
  {
    $this->defaultRoundingMode = $defaultRoundingMode;
  }
  /**
   * @return self::DEFAULT_ROUNDING_MODE_*
   */
  public function getDefaultRoundingMode()
  {
    return $this->defaultRoundingMode;
  }
  /**
   * Optional. The default lifetime of all tables in the dataset, in
   * milliseconds. The minimum lifetime value is 3600000 milliseconds (one
   * hour). To clear an existing default expiration with a PATCH request, set to
   * 0. Once this property is set, all newly-created tables in the dataset will
   * have an expirationTime property set to the creation time plus the value in
   * this property, and changing the value will only affect new tables, not
   * existing ones. When the expirationTime for a given table is reached, that
   * table will be deleted automatically. If a table's expirationTime is
   * modified or removed before the table expires, or if you provide an explicit
   * expirationTime when creating a table, that value takes precedence over the
   * default expiration time indicated by this property.
   *
   * @param string $defaultTableExpirationMs
   */
  public function setDefaultTableExpirationMs($defaultTableExpirationMs)
  {
    $this->defaultTableExpirationMs = $defaultTableExpirationMs;
  }
  /**
   * @return string
   */
  public function getDefaultTableExpirationMs()
  {
    return $this->defaultTableExpirationMs;
  }
  /**
   * Optional. A user-friendly description of the dataset.
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
   * Output only. A hash of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Options defining open source compatible datasets living in the
   * BigQuery catalog. Contains metadata of open source database, schema or
   * namespace represented by the current dataset.
   *
   * @param ExternalCatalogDatasetOptions $externalCatalogDatasetOptions
   */
  public function setExternalCatalogDatasetOptions(ExternalCatalogDatasetOptions $externalCatalogDatasetOptions)
  {
    $this->externalCatalogDatasetOptions = $externalCatalogDatasetOptions;
  }
  /**
   * @return ExternalCatalogDatasetOptions
   */
  public function getExternalCatalogDatasetOptions()
  {
    return $this->externalCatalogDatasetOptions;
  }
  /**
   * Optional. Reference to a read-only external dataset defined in data
   * catalogs outside of BigQuery. Filled out when the dataset type is EXTERNAL.
   *
   * @param ExternalDatasetReference $externalDatasetReference
   */
  public function setExternalDatasetReference(ExternalDatasetReference $externalDatasetReference)
  {
    $this->externalDatasetReference = $externalDatasetReference;
  }
  /**
   * @return ExternalDatasetReference
   */
  public function getExternalDatasetReference()
  {
    return $this->externalDatasetReference;
  }
  /**
   * Optional. A descriptive name for the dataset.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * Output only. The fully-qualified unique name of the dataset in the format
   * projectId:datasetId. The dataset name without the project name is given in
   * the datasetId field. When creating a new dataset, leave this field blank,
   * and instead specify the datasetId field.
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
   * Optional. TRUE if the dataset and its table names are case-insensitive,
   * otherwise FALSE. By default, this is FALSE, which means the dataset and its
   * table names are case-sensitive. This field does not affect routine
   * references.
   *
   * @param bool $isCaseInsensitive
   */
  public function setIsCaseInsensitive($isCaseInsensitive)
  {
    $this->isCaseInsensitive = $isCaseInsensitive;
  }
  /**
   * @return bool
   */
  public function getIsCaseInsensitive()
  {
    return $this->isCaseInsensitive;
  }
  /**
   * Output only. The resource type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The labels associated with this dataset. You can use these to organize and
   * group your datasets. You can set this property when inserting or updating a
   * dataset. See [Creating and Updating Dataset
   * Labels](https://cloud.google.com/bigquery/docs/creating-managing-
   * labels#creating_and_updating_dataset_labels) for more information.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The date when this dataset was last modified, in milliseconds
   * since the epoch.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Output only. Metadata about the LinkedDataset. Filled out when the dataset
   * type is LINKED.
   *
   * @param LinkedDatasetMetadata $linkedDatasetMetadata
   */
  public function setLinkedDatasetMetadata(LinkedDatasetMetadata $linkedDatasetMetadata)
  {
    $this->linkedDatasetMetadata = $linkedDatasetMetadata;
  }
  /**
   * @return LinkedDatasetMetadata
   */
  public function getLinkedDatasetMetadata()
  {
    return $this->linkedDatasetMetadata;
  }
  /**
   * Optional. The source dataset reference when the dataset is of type LINKED.
   * For all other dataset types it is not set. This field cannot be updated
   * once it is set. Any attempt to update this field using Update and Patch API
   * Operations will be ignored.
   *
   * @param LinkedDatasetSource $linkedDatasetSource
   */
  public function setLinkedDatasetSource(LinkedDatasetSource $linkedDatasetSource)
  {
    $this->linkedDatasetSource = $linkedDatasetSource;
  }
  /**
   * @return LinkedDatasetSource
   */
  public function getLinkedDatasetSource()
  {
    return $this->linkedDatasetSource;
  }
  /**
   * The geographic location where the dataset should reside. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. Defines the time travel window in hours. The value can be from 48
   * to 168 hours (2 to 7 days). The default value is 168 hours if this is not
   * set.
   *
   * @param string $maxTimeTravelHours
   */
  public function setMaxTimeTravelHours($maxTimeTravelHours)
  {
    $this->maxTimeTravelHours = $maxTimeTravelHours;
  }
  /**
   * @return string
   */
  public function getMaxTimeTravelHours()
  {
    return $this->maxTimeTravelHours;
  }
  /**
   * Optional. The [tags](https://cloud.google.com/bigquery/docs/tags) attached
   * to this dataset. Tag keys are globally unique. Tag key is expected to be in
   * the namespaced format, for example "123456789012/environment" where
   * 123456789012 is the ID of the parent organization or project resource for
   * this tag key. Tag value is expected to be the short name, for example
   * "Production". See [Tag definitions](https://cloud.google.com/iam/docs/tags-
   * access-control#definitions) for more details.
   *
   * @param string[] $resourceTags
   */
  public function setResourceTags($resourceTags)
  {
    $this->resourceTags = $resourceTags;
  }
  /**
   * @return string[]
   */
  public function getResourceTags()
  {
    return $this->resourceTags;
  }
  /**
   * Optional. Output only. Restriction config for all tables and dataset. If
   * set, restrict certain accesses on the dataset and all its tables based on
   * the config. See [Data
   * egress](https://cloud.google.com/bigquery/docs/analytics-hub-
   * introduction#data_egress) for more details.
   *
   * @param RestrictionConfig $restrictions
   */
  public function setRestrictions(RestrictionConfig $restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return RestrictionConfig
   */
  public function getRestrictions()
  {
    return $this->restrictions;
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
   * Output only. A URL that can be used to access the resource again. You can
   * use this URL in Get or Update requests to the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Optional. Updates storage_billing_model for the dataset.
   *
   * Accepted values: STORAGE_BILLING_MODEL_UNSPECIFIED, LOGICAL, PHYSICAL
   *
   * @param self::STORAGE_BILLING_MODEL_* $storageBillingModel
   */
  public function setStorageBillingModel($storageBillingModel)
  {
    $this->storageBillingModel = $storageBillingModel;
  }
  /**
   * @return self::STORAGE_BILLING_MODEL_*
   */
  public function getStorageBillingModel()
  {
    return $this->storageBillingModel;
  }
  /**
   * Output only. Tags for the dataset. To provide tags as inputs, use the
   * `resourceTags` field.
   *
   * @deprecated
   * @param DatasetTags[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @deprecated
   * @return DatasetTags[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. Same as `type` in `ListFormatDataset`. The type of the
   * dataset, one of: * DEFAULT - only accessible by owner and authorized
   * accounts, * PUBLIC - accessible by everyone, * LINKED - linked dataset, *
   * EXTERNAL - dataset with definition in external metadata catalog.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dataset::class, 'Google_Service_Bigquery_Dataset');
