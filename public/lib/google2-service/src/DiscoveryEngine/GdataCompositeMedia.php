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

namespace Google\Service\DiscoveryEngine;

class GdataCompositeMedia extends \Google\Model
{
  /**
   * Reference contains a GFS path or a local path.
   */
  public const REFERENCE_TYPE_PATH = 'PATH';
  /**
   * Reference points to a blobstore object. This could be either a v1 blob_ref
   * or a v2 blobstore2_info. Clients should check blobstore2_info first, since
   * v1 is being deprecated.
   */
  public const REFERENCE_TYPE_BLOB_REF = 'BLOB_REF';
  /**
   * Data is included into this proto buffer
   */
  public const REFERENCE_TYPE_INLINE = 'INLINE';
  /**
   * Reference points to a bigstore object
   */
  public const REFERENCE_TYPE_BIGSTORE_REF = 'BIGSTORE_REF';
  /**
   * Indicates the data is stored in cosmo_binary_reference.
   */
  public const REFERENCE_TYPE_COSMO_BINARY_REFERENCE = 'COSMO_BINARY_REFERENCE';
  /**
   * Blobstore v1 reference, set if reference_type is BLOBSTORE_REF This should
   * be the byte representation of a blobstore.BlobRef. Since Blobstore is
   * deprecating v1, use blobstore2_info instead. For now, any v2 blob will also
   * be represented in this field as v1 BlobRef.
   *
   * @deprecated
   * @var string
   */
  public $blobRef;
  protected $blobstore2InfoType = GdataBlobstore2Info::class;
  protected $blobstore2InfoDataType = '';
  /**
   * A binary data reference for a media download. Serves as a technology-
   * agnostic binary reference in some Google infrastructure. This value is a
   * serialized storage_cosmo.BinaryReference proto. Storing it as bytes is a
   * hack to get around the fact that the cosmo proto (as well as others it
   * includes) doesn't support JavaScript. This prevents us from including the
   * actual type of this field.
   *
   * @var string
   */
  public $cosmoBinaryReference;
  /**
   * crc32.c hash for the payload.
   *
   * @var string
   */
  public $crc32cHash;
  /**
   * Media data, set if reference_type is INLINE
   *
   * @var string
   */
  public $inline;
  /**
   * Size of the data, in bytes
   *
   * @var string
   */
  public $length;
  /**
   * MD5 hash for the payload.
   *
   * @var string
   */
  public $md5Hash;
  protected $objectIdType = GdataObjectId::class;
  protected $objectIdDataType = '';
  /**
   * Path to the data, set if reference_type is PATH
   *
   * @var string
   */
  public $path;
  /**
   * Describes what the field reference contains.
   *
   * @var string
   */
  public $referenceType;
  /**
   * SHA-1 hash for the payload.
   *
   * @var string
   */
  public $sha1Hash;

  /**
   * Blobstore v1 reference, set if reference_type is BLOBSTORE_REF This should
   * be the byte representation of a blobstore.BlobRef. Since Blobstore is
   * deprecating v1, use blobstore2_info instead. For now, any v2 blob will also
   * be represented in this field as v1 BlobRef.
   *
   * @deprecated
   * @param string $blobRef
   */
  public function setBlobRef($blobRef)
  {
    $this->blobRef = $blobRef;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBlobRef()
  {
    return $this->blobRef;
  }
  /**
   * Blobstore v2 info, set if reference_type is BLOBSTORE_REF and it refers to
   * a v2 blob.
   *
   * @param GdataBlobstore2Info $blobstore2Info
   */
  public function setBlobstore2Info(GdataBlobstore2Info $blobstore2Info)
  {
    $this->blobstore2Info = $blobstore2Info;
  }
  /**
   * @return GdataBlobstore2Info
   */
  public function getBlobstore2Info()
  {
    return $this->blobstore2Info;
  }
  /**
   * A binary data reference for a media download. Serves as a technology-
   * agnostic binary reference in some Google infrastructure. This value is a
   * serialized storage_cosmo.BinaryReference proto. Storing it as bytes is a
   * hack to get around the fact that the cosmo proto (as well as others it
   * includes) doesn't support JavaScript. This prevents us from including the
   * actual type of this field.
   *
   * @param string $cosmoBinaryReference
   */
  public function setCosmoBinaryReference($cosmoBinaryReference)
  {
    $this->cosmoBinaryReference = $cosmoBinaryReference;
  }
  /**
   * @return string
   */
  public function getCosmoBinaryReference()
  {
    return $this->cosmoBinaryReference;
  }
  /**
   * crc32.c hash for the payload.
   *
   * @param string $crc32cHash
   */
  public function setCrc32cHash($crc32cHash)
  {
    $this->crc32cHash = $crc32cHash;
  }
  /**
   * @return string
   */
  public function getCrc32cHash()
  {
    return $this->crc32cHash;
  }
  /**
   * Media data, set if reference_type is INLINE
   *
   * @param string $inline
   */
  public function setInline($inline)
  {
    $this->inline = $inline;
  }
  /**
   * @return string
   */
  public function getInline()
  {
    return $this->inline;
  }
  /**
   * Size of the data, in bytes
   *
   * @param string $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return string
   */
  public function getLength()
  {
    return $this->length;
  }
  /**
   * MD5 hash for the payload.
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
   * Reference to a TI Blob, set if reference_type is BIGSTORE_REF.
   *
   * @param GdataObjectId $objectId
   */
  public function setObjectId(GdataObjectId $objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return GdataObjectId
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * Path to the data, set if reference_type is PATH
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
   * Describes what the field reference contains.
   *
   * Accepted values: PATH, BLOB_REF, INLINE, BIGSTORE_REF,
   * COSMO_BINARY_REFERENCE
   *
   * @param self::REFERENCE_TYPE_* $referenceType
   */
  public function setReferenceType($referenceType)
  {
    $this->referenceType = $referenceType;
  }
  /**
   * @return self::REFERENCE_TYPE_*
   */
  public function getReferenceType()
  {
    return $this->referenceType;
  }
  /**
   * SHA-1 hash for the payload.
   *
   * @param string $sha1Hash
   */
  public function setSha1Hash($sha1Hash)
  {
    $this->sha1Hash = $sha1Hash;
  }
  /**
   * @return string
   */
  public function getSha1Hash()
  {
    return $this->sha1Hash;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataCompositeMedia::class, 'Google_Service_DiscoveryEngine_GdataCompositeMedia');
