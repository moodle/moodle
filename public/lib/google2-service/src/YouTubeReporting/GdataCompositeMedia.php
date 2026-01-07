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

namespace Google\Service\YouTubeReporting;

class GdataCompositeMedia extends \Google\Model
{
  /**
   * gdata
   */
  public const REFERENCE_TYPE_PATH = 'PATH';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_BLOB_REF = 'BLOB_REF';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_INLINE = 'INLINE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_BIGSTORE_REF = 'BIGSTORE_REF';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_COSMO_BINARY_REFERENCE = 'COSMO_BINARY_REFERENCE';
  /**
   * gdata
   *
   * @deprecated
   * @var string
   */
  public $blobRef;
  protected $blobstore2InfoType = GdataBlobstore2Info::class;
  protected $blobstore2InfoDataType = '';
  /**
   * gdata
   *
   * @var string
   */
  public $cosmoBinaryReference;
  /**
   * gdata
   *
   * @var string
   */
  public $crc32cHash;
  /**
   * gdata
   *
   * @var string
   */
  public $inline;
  /**
   * gdata
   *
   * @var string
   */
  public $length;
  /**
   * gdata
   *
   * @var string
   */
  public $md5Hash;
  protected $objectIdType = GdataObjectId::class;
  protected $objectIdDataType = '';
  /**
   * gdata
   *
   * @var string
   */
  public $path;
  /**
   * gdata
   *
   * @var string
   */
  public $referenceType;
  /**
   * gdata
   *
   * @var string
   */
  public $sha1Hash;

  /**
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
   * gdata
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
class_alias(GdataCompositeMedia::class, 'Google_Service_YouTubeReporting_GdataCompositeMedia');
