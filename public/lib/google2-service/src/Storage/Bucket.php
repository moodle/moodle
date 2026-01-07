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

namespace Google\Service\Storage;

class Bucket extends \Google\Collection
{
  protected $collection_key = 'defaultObjectAcl';
  protected $aclType = BucketAccessControl::class;
  protected $aclDataType = 'array';
  protected $autoclassType = BucketAutoclass::class;
  protected $autoclassDataType = '';
  protected $billingType = BucketBilling::class;
  protected $billingDataType = '';
  protected $corsType = BucketCors::class;
  protected $corsDataType = 'array';
  protected $customPlacementConfigType = BucketCustomPlacementConfig::class;
  protected $customPlacementConfigDataType = '';
  /**
   * The default value for event-based hold on newly created objects in this
   * bucket. Event-based hold is a way to retain objects indefinitely until an
   * event occurs, signified by the hold's release. After being released, such
   * objects will be subject to bucket-level retention (if any). One sample use
   * case of this flag is for banks to hold loan documents for at least 3 years
   * after loan is paid in full. Here, bucket-level retention is 3 years and the
   * event is loan being paid in full. In this example, these objects will be
   * held intact for any number of years until the event has occurred (event-
   * based hold on the object is released) and then 3 more years after that.
   * That means retention duration of the objects begins from the moment event-
   * based hold transitioned from true to false. Objects under event-based hold
   * cannot be deleted, overwritten or archived until the hold is removed.
   *
   * @var bool
   */
  public $defaultEventBasedHold;
  protected $defaultObjectAclType = ObjectAccessControl::class;
  protected $defaultObjectAclDataType = 'array';
  protected $encryptionType = BucketEncryption::class;
  protected $encryptionDataType = '';
  /**
   * HTTP 1.1 Entity tag for the bucket.
   *
   * @var string
   */
  public $etag;
  /**
   * The generation of this bucket.
   *
   * @var string
   */
  public $generation;
  /**
   * The hard delete time of the bucket in RFC 3339 format.
   *
   * @var string
   */
  public $hardDeleteTime;
  protected $hierarchicalNamespaceType = BucketHierarchicalNamespace::class;
  protected $hierarchicalNamespaceDataType = '';
  protected $iamConfigurationType = BucketIamConfiguration::class;
  protected $iamConfigurationDataType = '';
  /**
   * The ID of the bucket. For buckets, the id and name properties are the same.
   *
   * @var string
   */
  public $id;
  protected $ipFilterType = BucketIpFilter::class;
  protected $ipFilterDataType = '';
  /**
   * The kind of item this is. For buckets, this is always storage#bucket.
   *
   * @var string
   */
  public $kind;
  /**
   * User-provided labels, in key/value pairs.
   *
   * @var string[]
   */
  public $labels;
  protected $lifecycleType = BucketLifecycle::class;
  protected $lifecycleDataType = '';
  /**
   * The location of the bucket. Object data for objects in the bucket resides
   * in physical storage within this region. Defaults to US. See the
   * [Developer's Guide](https://cloud.google.com/storage/docs/locations) for
   * the authoritative list.
   *
   * @var string
   */
  public $location;
  /**
   * The type of the bucket location.
   *
   * @var string
   */
  public $locationType;
  protected $loggingType = BucketLogging::class;
  protected $loggingDataType = '';
  /**
   * The metadata generation of this bucket.
   *
   * @var string
   */
  public $metageneration;
  /**
   * The name of the bucket.
   *
   * @var string
   */
  public $name;
  protected $objectRetentionType = BucketObjectRetention::class;
  protected $objectRetentionDataType = '';
  protected $ownerType = BucketOwner::class;
  protected $ownerDataType = '';
  /**
   * The project number of the project the bucket belongs to.
   *
   * @var string
   */
  public $projectNumber;
  protected $retentionPolicyType = BucketRetentionPolicy::class;
  protected $retentionPolicyDataType = '';
  /**
   * The Recovery Point Objective (RPO) of this bucket. Set to ASYNC_TURBO to
   * turn on Turbo Replication on a bucket.
   *
   * @var string
   */
  public $rpo;
  /**
   * Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPZI;
  /**
   * Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPZS;
  /**
   * The URI of this bucket.
   *
   * @var string
   */
  public $selfLink;
  protected $softDeletePolicyType = BucketSoftDeletePolicy::class;
  protected $softDeletePolicyDataType = '';
  /**
   * The soft delete time of the bucket in RFC 3339 format.
   *
   * @var string
   */
  public $softDeleteTime;
  /**
   * The bucket's default storage class, used whenever no storageClass is
   * specified for a newly-created object. This defines how objects in the
   * bucket are stored and determines the SLA and the cost of storage. Values
   * include MULTI_REGIONAL, REGIONAL, STANDARD, NEARLINE, COLDLINE, ARCHIVE,
   * and DURABLE_REDUCED_AVAILABILITY. If this value is not specified when the
   * bucket is created, it will default to STANDARD. For more information, see
   * [Storage Classes](https://cloud.google.com/storage/docs/storage-classes).
   *
   * @var string
   */
  public $storageClass;
  /**
   * The creation time of the bucket in RFC 3339 format.
   *
   * @var string
   */
  public $timeCreated;
  /**
   * The modification time of the bucket in RFC 3339 format.
   *
   * @var string
   */
  public $updated;
  protected $versioningType = BucketVersioning::class;
  protected $versioningDataType = '';
  protected $websiteType = BucketWebsite::class;
  protected $websiteDataType = '';

  /**
   * Access controls on the bucket.
   *
   * @param BucketAccessControl[] $acl
   */
  public function setAcl($acl)
  {
    $this->acl = $acl;
  }
  /**
   * @return BucketAccessControl[]
   */
  public function getAcl()
  {
    return $this->acl;
  }
  /**
   * The bucket's Autoclass configuration.
   *
   * @param BucketAutoclass $autoclass
   */
  public function setAutoclass(BucketAutoclass $autoclass)
  {
    $this->autoclass = $autoclass;
  }
  /**
   * @return BucketAutoclass
   */
  public function getAutoclass()
  {
    return $this->autoclass;
  }
  /**
   * The bucket's billing configuration.
   *
   * @param BucketBilling $billing
   */
  public function setBilling(BucketBilling $billing)
  {
    $this->billing = $billing;
  }
  /**
   * @return BucketBilling
   */
  public function getBilling()
  {
    return $this->billing;
  }
  /**
   * The bucket's Cross-Origin Resource Sharing (CORS) configuration.
   *
   * @param BucketCors[] $cors
   */
  public function setCors($cors)
  {
    $this->cors = $cors;
  }
  /**
   * @return BucketCors[]
   */
  public function getCors()
  {
    return $this->cors;
  }
  /**
   * The bucket's custom placement configuration for Custom Dual Regions.
   *
   * @param BucketCustomPlacementConfig $customPlacementConfig
   */
  public function setCustomPlacementConfig(BucketCustomPlacementConfig $customPlacementConfig)
  {
    $this->customPlacementConfig = $customPlacementConfig;
  }
  /**
   * @return BucketCustomPlacementConfig
   */
  public function getCustomPlacementConfig()
  {
    return $this->customPlacementConfig;
  }
  /**
   * The default value for event-based hold on newly created objects in this
   * bucket. Event-based hold is a way to retain objects indefinitely until an
   * event occurs, signified by the hold's release. After being released, such
   * objects will be subject to bucket-level retention (if any). One sample use
   * case of this flag is for banks to hold loan documents for at least 3 years
   * after loan is paid in full. Here, bucket-level retention is 3 years and the
   * event is loan being paid in full. In this example, these objects will be
   * held intact for any number of years until the event has occurred (event-
   * based hold on the object is released) and then 3 more years after that.
   * That means retention duration of the objects begins from the moment event-
   * based hold transitioned from true to false. Objects under event-based hold
   * cannot be deleted, overwritten or archived until the hold is removed.
   *
   * @param bool $defaultEventBasedHold
   */
  public function setDefaultEventBasedHold($defaultEventBasedHold)
  {
    $this->defaultEventBasedHold = $defaultEventBasedHold;
  }
  /**
   * @return bool
   */
  public function getDefaultEventBasedHold()
  {
    return $this->defaultEventBasedHold;
  }
  /**
   * Default access controls to apply to new objects when no ACL is provided.
   *
   * @param ObjectAccessControl[] $defaultObjectAcl
   */
  public function setDefaultObjectAcl($defaultObjectAcl)
  {
    $this->defaultObjectAcl = $defaultObjectAcl;
  }
  /**
   * @return ObjectAccessControl[]
   */
  public function getDefaultObjectAcl()
  {
    return $this->defaultObjectAcl;
  }
  /**
   * Encryption configuration for a bucket.
   *
   * @param BucketEncryption $encryption
   */
  public function setEncryption(BucketEncryption $encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return BucketEncryption
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * HTTP 1.1 Entity tag for the bucket.
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
   * The generation of this bucket.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * The hard delete time of the bucket in RFC 3339 format.
   *
   * @param string $hardDeleteTime
   */
  public function setHardDeleteTime($hardDeleteTime)
  {
    $this->hardDeleteTime = $hardDeleteTime;
  }
  /**
   * @return string
   */
  public function getHardDeleteTime()
  {
    return $this->hardDeleteTime;
  }
  /**
   * The bucket's hierarchical namespace configuration.
   *
   * @param BucketHierarchicalNamespace $hierarchicalNamespace
   */
  public function setHierarchicalNamespace(BucketHierarchicalNamespace $hierarchicalNamespace)
  {
    $this->hierarchicalNamespace = $hierarchicalNamespace;
  }
  /**
   * @return BucketHierarchicalNamespace
   */
  public function getHierarchicalNamespace()
  {
    return $this->hierarchicalNamespace;
  }
  /**
   * The bucket's IAM configuration.
   *
   * @param BucketIamConfiguration $iamConfiguration
   */
  public function setIamConfiguration(BucketIamConfiguration $iamConfiguration)
  {
    $this->iamConfiguration = $iamConfiguration;
  }
  /**
   * @return BucketIamConfiguration
   */
  public function getIamConfiguration()
  {
    return $this->iamConfiguration;
  }
  /**
   * The ID of the bucket. For buckets, the id and name properties are the same.
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
   * The bucket's IP filter configuration. Specifies the network sources that
   * are allowed to access the operations on the bucket, as well as its
   * underlying objects. Only enforced when the mode is set to 'Enabled'.
   *
   * @param BucketIpFilter $ipFilter
   */
  public function setIpFilter(BucketIpFilter $ipFilter)
  {
    $this->ipFilter = $ipFilter;
  }
  /**
   * @return BucketIpFilter
   */
  public function getIpFilter()
  {
    return $this->ipFilter;
  }
  /**
   * The kind of item this is. For buckets, this is always storage#bucket.
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
   * User-provided labels, in key/value pairs.
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
   * The bucket's lifecycle configuration. See [Lifecycle
   * Management](https://cloud.google.com/storage/docs/lifecycle) for more
   * information.
   *
   * @param BucketLifecycle $lifecycle
   */
  public function setLifecycle(BucketLifecycle $lifecycle)
  {
    $this->lifecycle = $lifecycle;
  }
  /**
   * @return BucketLifecycle
   */
  public function getLifecycle()
  {
    return $this->lifecycle;
  }
  /**
   * The location of the bucket. Object data for objects in the bucket resides
   * in physical storage within this region. Defaults to US. See the
   * [Developer's Guide](https://cloud.google.com/storage/docs/locations) for
   * the authoritative list.
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
   * The type of the bucket location.
   *
   * @param string $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return string
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
  /**
   * The bucket's logging configuration, which defines the destination bucket
   * and optional name prefix for the current bucket's logs.
   *
   * @param BucketLogging $logging
   */
  public function setLogging(BucketLogging $logging)
  {
    $this->logging = $logging;
  }
  /**
   * @return BucketLogging
   */
  public function getLogging()
  {
    return $this->logging;
  }
  /**
   * The metadata generation of this bucket.
   *
   * @param string $metageneration
   */
  public function setMetageneration($metageneration)
  {
    $this->metageneration = $metageneration;
  }
  /**
   * @return string
   */
  public function getMetageneration()
  {
    return $this->metageneration;
  }
  /**
   * The name of the bucket.
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
   * The bucket's object retention config.
   *
   * @param BucketObjectRetention $objectRetention
   */
  public function setObjectRetention(BucketObjectRetention $objectRetention)
  {
    $this->objectRetention = $objectRetention;
  }
  /**
   * @return BucketObjectRetention
   */
  public function getObjectRetention()
  {
    return $this->objectRetention;
  }
  /**
   * The owner of the bucket. This is always the project team's owner group.
   *
   * @param BucketOwner $owner
   */
  public function setOwner(BucketOwner $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return BucketOwner
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * The project number of the project the bucket belongs to.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * The bucket's retention policy. The retention policy enforces a minimum
   * retention time for all objects contained in the bucket, based on their
   * creation time. Any attempt to overwrite or delete objects younger than the
   * retention period will result in a PERMISSION_DENIED error. An unlocked
   * retention policy can be modified or removed from the bucket via a
   * storage.buckets.update operation. A locked retention policy cannot be
   * removed or shortened in duration for the lifetime of the bucket. Attempting
   * to remove or decrease period of a locked retention policy will result in a
   * PERMISSION_DENIED error.
   *
   * @param BucketRetentionPolicy $retentionPolicy
   */
  public function setRetentionPolicy(BucketRetentionPolicy $retentionPolicy)
  {
    $this->retentionPolicy = $retentionPolicy;
  }
  /**
   * @return BucketRetentionPolicy
   */
  public function getRetentionPolicy()
  {
    return $this->retentionPolicy;
  }
  /**
   * The Recovery Point Objective (RPO) of this bucket. Set to ASYNC_TURBO to
   * turn on Turbo Replication on a bucket.
   *
   * @param string $rpo
   */
  public function setRpo($rpo)
  {
    $this->rpo = $rpo;
  }
  /**
   * @return string
   */
  public function getRpo()
  {
    return $this->rpo;
  }
  /**
   * Reserved for future use.
   *
   * @param bool $satisfiesPZI
   */
  public function setSatisfiesPZI($satisfiesPZI)
  {
    $this->satisfiesPZI = $satisfiesPZI;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPZI()
  {
    return $this->satisfiesPZI;
  }
  /**
   * Reserved for future use.
   *
   * @param bool $satisfiesPZS
   */
  public function setSatisfiesPZS($satisfiesPZS)
  {
    $this->satisfiesPZS = $satisfiesPZS;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPZS()
  {
    return $this->satisfiesPZS;
  }
  /**
   * The URI of this bucket.
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
   * The bucket's soft delete policy, which defines the period of time that
   * soft-deleted objects will be retained, and cannot be permanently deleted.
   *
   * @param BucketSoftDeletePolicy $softDeletePolicy
   */
  public function setSoftDeletePolicy(BucketSoftDeletePolicy $softDeletePolicy)
  {
    $this->softDeletePolicy = $softDeletePolicy;
  }
  /**
   * @return BucketSoftDeletePolicy
   */
  public function getSoftDeletePolicy()
  {
    return $this->softDeletePolicy;
  }
  /**
   * The soft delete time of the bucket in RFC 3339 format.
   *
   * @param string $softDeleteTime
   */
  public function setSoftDeleteTime($softDeleteTime)
  {
    $this->softDeleteTime = $softDeleteTime;
  }
  /**
   * @return string
   */
  public function getSoftDeleteTime()
  {
    return $this->softDeleteTime;
  }
  /**
   * The bucket's default storage class, used whenever no storageClass is
   * specified for a newly-created object. This defines how objects in the
   * bucket are stored and determines the SLA and the cost of storage. Values
   * include MULTI_REGIONAL, REGIONAL, STANDARD, NEARLINE, COLDLINE, ARCHIVE,
   * and DURABLE_REDUCED_AVAILABILITY. If this value is not specified when the
   * bucket is created, it will default to STANDARD. For more information, see
   * [Storage Classes](https://cloud.google.com/storage/docs/storage-classes).
   *
   * @param string $storageClass
   */
  public function setStorageClass($storageClass)
  {
    $this->storageClass = $storageClass;
  }
  /**
   * @return string
   */
  public function getStorageClass()
  {
    return $this->storageClass;
  }
  /**
   * The creation time of the bucket in RFC 3339 format.
   *
   * @param string $timeCreated
   */
  public function setTimeCreated($timeCreated)
  {
    $this->timeCreated = $timeCreated;
  }
  /**
   * @return string
   */
  public function getTimeCreated()
  {
    return $this->timeCreated;
  }
  /**
   * The modification time of the bucket in RFC 3339 format.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * The bucket's versioning configuration.
   *
   * @param BucketVersioning $versioning
   */
  public function setVersioning(BucketVersioning $versioning)
  {
    $this->versioning = $versioning;
  }
  /**
   * @return BucketVersioning
   */
  public function getVersioning()
  {
    return $this->versioning;
  }
  /**
   * The bucket's website configuration, controlling how the service behaves
   * when accessing bucket contents as a web site. See the [Static Website
   * Examples](https://cloud.google.com/storage/docs/static-website) for more
   * information.
   *
   * @param BucketWebsite $website
   */
  public function setWebsite(BucketWebsite $website)
  {
    $this->website = $website;
  }
  /**
   * @return BucketWebsite
   */
  public function getWebsite()
  {
    return $this->website;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bucket::class, 'Google_Service_Storage_Bucket');
