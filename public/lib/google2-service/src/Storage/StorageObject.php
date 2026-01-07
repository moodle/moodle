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

class StorageObject extends \Google\Collection
{
  protected $collection_key = 'acl';
  protected $aclType = ObjectAccessControl::class;
  protected $aclDataType = 'array';
  /**
   * The name of the bucket containing this object.
   *
   * @var string
   */
  public $bucket;
  /**
   * Cache-Control directive for the object data. If omitted, and the object is
   * accessible to all anonymous users, the default will be public, max-
   * age=3600.
   *
   * @var string
   */
  public $cacheControl;
  /**
   * Number of underlying components that make up this object. Components are
   * accumulated by compose operations.
   *
   * @var int
   */
  public $componentCount;
  /**
   * Content-Disposition of the object data.
   *
   * @var string
   */
  public $contentDisposition;
  /**
   * Content-Encoding of the object data.
   *
   * @var string
   */
  public $contentEncoding;
  /**
   * Content-Language of the object data.
   *
   * @var string
   */
  public $contentLanguage;
  /**
   * Content-Type of the object data. If an object is stored without a Content-
   * Type, it is served as application/octet-stream.
   *
   * @var string
   */
  public $contentType;
  protected $contextsType = StorageObjectContexts::class;
  protected $contextsDataType = '';
  /**
   * CRC32c checksum, as described in RFC 4960, Appendix B; encoded using base64
   * in big-endian byte order. For more information about using the CRC32c
   * checksum, see [Data Validation and Change
   * Detection](https://cloud.google.com/storage/docs/data-validation).
   *
   * @var string
   */
  public $crc32c;
  /**
   * A timestamp in RFC 3339 format specified by the user for an object.
   *
   * @var string
   */
  public $customTime;
  protected $customerEncryptionType = StorageObjectCustomerEncryption::class;
  protected $customerEncryptionDataType = '';
  /**
   * HTTP 1.1 Entity tag for the object.
   *
   * @var string
   */
  public $etag;
  /**
   * Whether an object is under event-based hold. Event-based hold is a way to
   * retain objects until an event occurs, which is signified by the hold's
   * release (i.e. this value is set to false). After being released (set to
   * false), such objects will be subject to bucket-level retention (if any).
   * One sample use case of this flag is for banks to hold loan documents for at
   * least 3 years after loan is paid in full. Here, bucket-level retention is 3
   * years and the event is the loan being paid in full. In this example, these
   * objects will be held intact for any number of years until the event has
   * occurred (event-based hold on the object is released) and then 3 more years
   * after that. That means retention duration of the objects begins from the
   * moment event-based hold transitioned from true to false.
   *
   * @var bool
   */
  public $eventBasedHold;
  /**
   * The content generation of this object. Used for object versioning.
   *
   * @var string
   */
  public $generation;
  /**
   * This is the time (in the future) when the soft-deleted object will no
   * longer be restorable. It is equal to the soft delete time plus the current
   * soft delete retention duration of the bucket.
   *
   * @var string
   */
  public $hardDeleteTime;
  /**
   * The ID of the object, including the bucket name, object name, and
   * generation number.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For objects, this is always storage#object.
   *
   * @var string
   */
  public $kind;
  /**
   * Not currently supported. Specifying the parameter causes the request to
   * fail with status code 400 - Bad Request.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * MD5 hash of the data; encoded using base64. For more information about
   * using the MD5 hash, see [Data Validation and Change
   * Detection](https://cloud.google.com/storage/docs/data-validation).
   *
   * @var string
   */
  public $md5Hash;
  /**
   * Media download link.
   *
   * @var string
   */
  public $mediaLink;
  /**
   * User-provided metadata, in key/value pairs.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The version of the metadata for this object at this generation. Used for
   * preconditions and for detecting changes in metadata. A metageneration
   * number is only meaningful in the context of a particular generation of a
   * particular object.
   *
   * @var string
   */
  public $metageneration;
  /**
   * The name of the object. Required if not specified by URL parameter.
   *
   * @var string
   */
  public $name;
  protected $ownerType = StorageObjectOwner::class;
  protected $ownerDataType = '';
  /**
   * Restore token used to differentiate deleted objects with the same name and
   * generation. This field is only returned for deleted objects in hierarchical
   * namespace buckets.
   *
   * @var string
   */
  public $restoreToken;
  protected $retentionType = StorageObjectRetention::class;
  protected $retentionDataType = '';
  /**
   * A server-determined value that specifies the earliest time that the
   * object's retention period expires. This value is in RFC 3339 format. Note
   * 1: This field is not provided for objects with an active event-based hold,
   * since retention expiration is unknown until the hold is removed. Note 2:
   * This value can be provided even when temporary hold is set (so that the
   * user can reason about policy without having to first unset the temporary
   * hold).
   *
   * @var string
   */
  public $retentionExpirationTime;
  /**
   * The link to this object.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Content-Length of the data in bytes.
   *
   * @var string
   */
  public $size;
  /**
   * The time at which the object became soft-deleted in RFC 3339 format.
   *
   * @var string
   */
  public $softDeleteTime;
  /**
   * Storage class of the object.
   *
   * @var string
   */
  public $storageClass;
  /**
   * Whether an object is under temporary hold. While this flag is set to true,
   * the object is protected against deletion and overwrites. A common use case
   * of this flag is regulatory investigations where objects need to be retained
   * while the investigation is ongoing. Note that unlike event-based hold,
   * temporary hold does not impact retention expiration time of an object.
   *
   * @var bool
   */
  public $temporaryHold;
  /**
   * The creation time of the object in RFC 3339 format.
   *
   * @var string
   */
  public $timeCreated;
  /**
   * The time at which the object became noncurrent in RFC 3339 format. Will be
   * returned if and only if this version of the object has been deleted.
   *
   * @var string
   */
  public $timeDeleted;
  /**
   * The time when the object was finalized.
   *
   * @var string
   */
  public $timeFinalized;
  /**
   * The time at which the object's storage class was last changed. When the
   * object is initially created, it will be set to timeCreated.
   *
   * @var string
   */
  public $timeStorageClassUpdated;
  /**
   * The modification time of the object metadata in RFC 3339 format. Set
   * initially to object creation time and then updated whenever any metadata of
   * the object changes. This includes changes made by a requester, such as
   * modifying custom metadata, as well as changes made by Cloud Storage on
   * behalf of a requester, such as changing the storage class based on an
   * Object Lifecycle Configuration.
   *
   * @var string
   */
  public $updated;

  /**
   * Access controls on the object.
   *
   * @param ObjectAccessControl[] $acl
   */
  public function setAcl($acl)
  {
    $this->acl = $acl;
  }
  /**
   * @return ObjectAccessControl[]
   */
  public function getAcl()
  {
    return $this->acl;
  }
  /**
   * The name of the bucket containing this object.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Cache-Control directive for the object data. If omitted, and the object is
   * accessible to all anonymous users, the default will be public, max-
   * age=3600.
   *
   * @param string $cacheControl
   */
  public function setCacheControl($cacheControl)
  {
    $this->cacheControl = $cacheControl;
  }
  /**
   * @return string
   */
  public function getCacheControl()
  {
    return $this->cacheControl;
  }
  /**
   * Number of underlying components that make up this object. Components are
   * accumulated by compose operations.
   *
   * @param int $componentCount
   */
  public function setComponentCount($componentCount)
  {
    $this->componentCount = $componentCount;
  }
  /**
   * @return int
   */
  public function getComponentCount()
  {
    return $this->componentCount;
  }
  /**
   * Content-Disposition of the object data.
   *
   * @param string $contentDisposition
   */
  public function setContentDisposition($contentDisposition)
  {
    $this->contentDisposition = $contentDisposition;
  }
  /**
   * @return string
   */
  public function getContentDisposition()
  {
    return $this->contentDisposition;
  }
  /**
   * Content-Encoding of the object data.
   *
   * @param string $contentEncoding
   */
  public function setContentEncoding($contentEncoding)
  {
    $this->contentEncoding = $contentEncoding;
  }
  /**
   * @return string
   */
  public function getContentEncoding()
  {
    return $this->contentEncoding;
  }
  /**
   * Content-Language of the object data.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * Content-Type of the object data. If an object is stored without a Content-
   * Type, it is served as application/octet-stream.
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * User-defined or system-defined object contexts. Each object context is a
   * key-payload pair, where the key provides the identification and the payload
   * holds the associated value and additional metadata.
   *
   * @param StorageObjectContexts $contexts
   */
  public function setContexts(StorageObjectContexts $contexts)
  {
    $this->contexts = $contexts;
  }
  /**
   * @return StorageObjectContexts
   */
  public function getContexts()
  {
    return $this->contexts;
  }
  /**
   * CRC32c checksum, as described in RFC 4960, Appendix B; encoded using base64
   * in big-endian byte order. For more information about using the CRC32c
   * checksum, see [Data Validation and Change
   * Detection](https://cloud.google.com/storage/docs/data-validation).
   *
   * @param string $crc32c
   */
  public function setCrc32c($crc32c)
  {
    $this->crc32c = $crc32c;
  }
  /**
   * @return string
   */
  public function getCrc32c()
  {
    return $this->crc32c;
  }
  /**
   * A timestamp in RFC 3339 format specified by the user for an object.
   *
   * @param string $customTime
   */
  public function setCustomTime($customTime)
  {
    $this->customTime = $customTime;
  }
  /**
   * @return string
   */
  public function getCustomTime()
  {
    return $this->customTime;
  }
  /**
   * Metadata of customer-supplied encryption key, if the object is encrypted by
   * such a key.
   *
   * @param StorageObjectCustomerEncryption $customerEncryption
   */
  public function setCustomerEncryption(StorageObjectCustomerEncryption $customerEncryption)
  {
    $this->customerEncryption = $customerEncryption;
  }
  /**
   * @return StorageObjectCustomerEncryption
   */
  public function getCustomerEncryption()
  {
    return $this->customerEncryption;
  }
  /**
   * HTTP 1.1 Entity tag for the object.
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
   * Whether an object is under event-based hold. Event-based hold is a way to
   * retain objects until an event occurs, which is signified by the hold's
   * release (i.e. this value is set to false). After being released (set to
   * false), such objects will be subject to bucket-level retention (if any).
   * One sample use case of this flag is for banks to hold loan documents for at
   * least 3 years after loan is paid in full. Here, bucket-level retention is 3
   * years and the event is the loan being paid in full. In this example, these
   * objects will be held intact for any number of years until the event has
   * occurred (event-based hold on the object is released) and then 3 more years
   * after that. That means retention duration of the objects begins from the
   * moment event-based hold transitioned from true to false.
   *
   * @param bool $eventBasedHold
   */
  public function setEventBasedHold($eventBasedHold)
  {
    $this->eventBasedHold = $eventBasedHold;
  }
  /**
   * @return bool
   */
  public function getEventBasedHold()
  {
    return $this->eventBasedHold;
  }
  /**
   * The content generation of this object. Used for object versioning.
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
   * This is the time (in the future) when the soft-deleted object will no
   * longer be restorable. It is equal to the soft delete time plus the current
   * soft delete retention duration of the bucket.
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
   * The ID of the object, including the bucket name, object name, and
   * generation number.
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
   * The kind of item this is. For objects, this is always storage#object.
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
   * Not currently supported. Specifying the parameter causes the request to
   * fail with status code 400 - Bad Request.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * MD5 hash of the data; encoded using base64. For more information about
   * using the MD5 hash, see [Data Validation and Change
   * Detection](https://cloud.google.com/storage/docs/data-validation).
   *
   * @param string $md5Hash
   */
  public function setMd5Hash($md5Hash)
  {
    $this->md5Hash = $md5Hash;
  }
  /**
   * @return string
   */
  public function getMd5Hash()
  {
    return $this->md5Hash;
  }
  /**
   * Media download link.
   *
   * @param string $mediaLink
   */
  public function setMediaLink($mediaLink)
  {
    $this->mediaLink = $mediaLink;
  }
  /**
   * @return string
   */
  public function getMediaLink()
  {
    return $this->mediaLink;
  }
  /**
   * User-provided metadata, in key/value pairs.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The version of the metadata for this object at this generation. Used for
   * preconditions and for detecting changes in metadata. A metageneration
   * number is only meaningful in the context of a particular generation of a
   * particular object.
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
   * The name of the object. Required if not specified by URL parameter.
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
   * The owner of the object. This will always be the uploader of the object.
   *
   * @param StorageObjectOwner $owner
   */
  public function setOwner(StorageObjectOwner $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return StorageObjectOwner
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * Restore token used to differentiate deleted objects with the same name and
   * generation. This field is only returned for deleted objects in hierarchical
   * namespace buckets.
   *
   * @param string $restoreToken
   */
  public function setRestoreToken($restoreToken)
  {
    $this->restoreToken = $restoreToken;
  }
  /**
   * @return string
   */
  public function getRestoreToken()
  {
    return $this->restoreToken;
  }
  /**
   * A collection of object level retention parameters.
   *
   * @param StorageObjectRetention $retention
   */
  public function setRetention(StorageObjectRetention $retention)
  {
    $this->retention = $retention;
  }
  /**
   * @return StorageObjectRetention
   */
  public function getRetention()
  {
    return $this->retention;
  }
  /**
   * A server-determined value that specifies the earliest time that the
   * object's retention period expires. This value is in RFC 3339 format. Note
   * 1: This field is not provided for objects with an active event-based hold,
   * since retention expiration is unknown until the hold is removed. Note 2:
   * This value can be provided even when temporary hold is set (so that the
   * user can reason about policy without having to first unset the temporary
   * hold).
   *
   * @param string $retentionExpirationTime
   */
  public function setRetentionExpirationTime($retentionExpirationTime)
  {
    $this->retentionExpirationTime = $retentionExpirationTime;
  }
  /**
   * @return string
   */
  public function getRetentionExpirationTime()
  {
    return $this->retentionExpirationTime;
  }
  /**
   * The link to this object.
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
   * Content-Length of the data in bytes.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * The time at which the object became soft-deleted in RFC 3339 format.
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
   * Storage class of the object.
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
   * Whether an object is under temporary hold. While this flag is set to true,
   * the object is protected against deletion and overwrites. A common use case
   * of this flag is regulatory investigations where objects need to be retained
   * while the investigation is ongoing. Note that unlike event-based hold,
   * temporary hold does not impact retention expiration time of an object.
   *
   * @param bool $temporaryHold
   */
  public function setTemporaryHold($temporaryHold)
  {
    $this->temporaryHold = $temporaryHold;
  }
  /**
   * @return bool
   */
  public function getTemporaryHold()
  {
    return $this->temporaryHold;
  }
  /**
   * The creation time of the object in RFC 3339 format.
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
   * The time at which the object became noncurrent in RFC 3339 format. Will be
   * returned if and only if this version of the object has been deleted.
   *
   * @param string $timeDeleted
   */
  public function setTimeDeleted($timeDeleted)
  {
    $this->timeDeleted = $timeDeleted;
  }
  /**
   * @return string
   */
  public function getTimeDeleted()
  {
    return $this->timeDeleted;
  }
  /**
   * The time when the object was finalized.
   *
   * @param string $timeFinalized
   */
  public function setTimeFinalized($timeFinalized)
  {
    $this->timeFinalized = $timeFinalized;
  }
  /**
   * @return string
   */
  public function getTimeFinalized()
  {
    return $this->timeFinalized;
  }
  /**
   * The time at which the object's storage class was last changed. When the
   * object is initially created, it will be set to timeCreated.
   *
   * @param string $timeStorageClassUpdated
   */
  public function setTimeStorageClassUpdated($timeStorageClassUpdated)
  {
    $this->timeStorageClassUpdated = $timeStorageClassUpdated;
  }
  /**
   * @return string
   */
  public function getTimeStorageClassUpdated()
  {
    return $this->timeStorageClassUpdated;
  }
  /**
   * The modification time of the object metadata in RFC 3339 format. Set
   * initially to object creation time and then updated whenever any metadata of
   * the object changes. This includes changes made by a requester, such as
   * modifying custom metadata, as well as changes made by Cloud Storage on
   * behalf of a requester, such as changing the storage class based on an
   * Object Lifecycle Configuration.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageObject::class, 'Google_Service_Storage_StorageObject');
