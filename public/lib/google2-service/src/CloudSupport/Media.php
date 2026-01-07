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

namespace Google\Service\CloudSupport;

class Media extends \Google\Collection
{
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_PATH = 'PATH';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_BLOB_REF = 'BLOB_REF';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_INLINE = 'INLINE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_GET_MEDIA = 'GET_MEDIA';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_COMPOSITE_MEDIA = 'COMPOSITE_MEDIA';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_BIGSTORE_REF = 'BIGSTORE_REF';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_DIFF_VERSION_RESPONSE = 'DIFF_VERSION_RESPONSE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_DIFF_CHECKSUMS_RESPONSE = 'DIFF_CHECKSUMS_RESPONSE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_DIFF_DOWNLOAD_RESPONSE = 'DIFF_DOWNLOAD_RESPONSE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_DIFF_UPLOAD_REQUEST = 'DIFF_UPLOAD_REQUEST';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_DIFF_UPLOAD_RESPONSE = 'DIFF_UPLOAD_RESPONSE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_COSMO_BINARY_REFERENCE = 'COSMO_BINARY_REFERENCE';
  /**
   * # gdata.* are outside protos with mising documentation
   */
  public const REFERENCE_TYPE_ARBITRARY_BYTES = 'ARBITRARY_BYTES';
  protected $collection_key = 'compositeMedia';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @var string
   */
  public $algorithm;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @var string
   */
  public $bigstoreObjectRef;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @var string
   */
  public $blobRef;
  protected $blobstore2InfoType = Blobstore2Info::class;
  protected $blobstore2InfoDataType = '';
  protected $compositeMediaType = CompositeMedia::class;
  protected $compositeMediaDataType = 'array';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $contentType;
  protected $contentTypeInfoType = ContentTypeInfo::class;
  protected $contentTypeInfoDataType = '';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $cosmoBinaryReference;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $crc32cHash;
  protected $diffChecksumsResponseType = DiffChecksumsResponse::class;
  protected $diffChecksumsResponseDataType = '';
  protected $diffDownloadResponseType = DiffDownloadResponse::class;
  protected $diffDownloadResponseDataType = '';
  protected $diffUploadRequestType = DiffUploadRequest::class;
  protected $diffUploadRequestDataType = '';
  protected $diffUploadResponseType = DiffUploadResponse::class;
  protected $diffUploadResponseDataType = '';
  protected $diffVersionResponseType = DiffVersionResponse::class;
  protected $diffVersionResponseDataType = '';
  protected $downloadParametersType = DownloadParameters::class;
  protected $downloadParametersDataType = '';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $filename;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @var string
   */
  public $hash;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var bool
   */
  public $hashVerified;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $inline;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var bool
   */
  public $isPotentialRetry;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $length;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $md5Hash;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $mediaId;
  protected $objectIdType = ObjectId::class;
  protected $objectIdDataType = '';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $path;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $referenceType;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $sha1Hash;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $sha256Hash;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $timestamp;
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $token;

  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @param string $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @param string $bigstoreObjectRef
   */
  public function setBigstoreObjectRef($bigstoreObjectRef)
  {
    $this->bigstoreObjectRef = $bigstoreObjectRef;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBigstoreObjectRef()
  {
    return $this->bigstoreObjectRef;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * @param Blobstore2Info $blobstore2Info
   */
  public function setBlobstore2Info(Blobstore2Info $blobstore2Info)
  {
    $this->blobstore2Info = $blobstore2Info;
  }
  /**
   * @return Blobstore2Info
   */
  public function getBlobstore2Info()
  {
    return $this->blobstore2Info;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param CompositeMedia[] $compositeMedia
   */
  public function setCompositeMedia($compositeMedia)
  {
    $this->compositeMedia = $compositeMedia;
  }
  /**
   * @return CompositeMedia[]
   */
  public function getCompositeMedia()
  {
    return $this->compositeMedia;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * @param ContentTypeInfo $contentTypeInfo
   */
  public function setContentTypeInfo(ContentTypeInfo $contentTypeInfo)
  {
    $this->contentTypeInfo = $contentTypeInfo;
  }
  /**
   * @return ContentTypeInfo
   */
  public function getContentTypeInfo()
  {
    return $this->contentTypeInfo;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * @param DiffChecksumsResponse $diffChecksumsResponse
   */
  public function setDiffChecksumsResponse(DiffChecksumsResponse $diffChecksumsResponse)
  {
    $this->diffChecksumsResponse = $diffChecksumsResponse;
  }
  /**
   * @return DiffChecksumsResponse
   */
  public function getDiffChecksumsResponse()
  {
    return $this->diffChecksumsResponse;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param DiffDownloadResponse $diffDownloadResponse
   */
  public function setDiffDownloadResponse(DiffDownloadResponse $diffDownloadResponse)
  {
    $this->diffDownloadResponse = $diffDownloadResponse;
  }
  /**
   * @return DiffDownloadResponse
   */
  public function getDiffDownloadResponse()
  {
    return $this->diffDownloadResponse;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param DiffUploadRequest $diffUploadRequest
   */
  public function setDiffUploadRequest(DiffUploadRequest $diffUploadRequest)
  {
    $this->diffUploadRequest = $diffUploadRequest;
  }
  /**
   * @return DiffUploadRequest
   */
  public function getDiffUploadRequest()
  {
    return $this->diffUploadRequest;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param DiffUploadResponse $diffUploadResponse
   */
  public function setDiffUploadResponse(DiffUploadResponse $diffUploadResponse)
  {
    $this->diffUploadResponse = $diffUploadResponse;
  }
  /**
   * @return DiffUploadResponse
   */
  public function getDiffUploadResponse()
  {
    return $this->diffUploadResponse;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param DiffVersionResponse $diffVersionResponse
   */
  public function setDiffVersionResponse(DiffVersionResponse $diffVersionResponse)
  {
    $this->diffVersionResponse = $diffVersionResponse;
  }
  /**
   * @return DiffVersionResponse
   */
  public function getDiffVersionResponse()
  {
    return $this->diffVersionResponse;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param DownloadParameters $downloadParameters
   */
  public function setDownloadParameters(DownloadParameters $downloadParameters)
  {
    $this->downloadParameters = $downloadParameters;
  }
  /**
   * @return DownloadParameters
   */
  public function getDownloadParameters()
  {
    return $this->downloadParameters;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @deprecated
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param bool $hashVerified
   */
  public function setHashVerified($hashVerified)
  {
    $this->hashVerified = $hashVerified;
  }
  /**
   * @return bool
   */
  public function getHashVerified()
  {
    return $this->hashVerified;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * @param bool $isPotentialRetry
   */
  public function setIsPotentialRetry($isPotentialRetry)
  {
    $this->isPotentialRetry = $isPotentialRetry;
  }
  /**
   * @return bool
   */
  public function getIsPotentialRetry()
  {
    return $this->isPotentialRetry;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * @param string $mediaId
   */
  public function setMediaId($mediaId)
  {
    $this->mediaId = $mediaId;
  }
  /**
   * @return string
   */
  public function getMediaId()
  {
    return $this->mediaId;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param ObjectId $objectId
   */
  public function setObjectId(ObjectId $objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return ObjectId
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
   * # gdata.* are outside protos with mising documentation
   *
   * Accepted values: PATH, BLOB_REF, INLINE, GET_MEDIA, COMPOSITE_MEDIA,
   * BIGSTORE_REF, DIFF_VERSION_RESPONSE, DIFF_CHECKSUMS_RESPONSE,
   * DIFF_DOWNLOAD_RESPONSE, DIFF_UPLOAD_REQUEST, DIFF_UPLOAD_RESPONSE,
   * COSMO_BINARY_REFERENCE, ARBITRARY_BYTES
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
   * # gdata.* are outside protos with mising documentation
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
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param string $sha256Hash
   */
  public function setSha256Hash($sha256Hash)
  {
    $this->sha256Hash = $sha256Hash;
  }
  /**
   * @return string
   */
  public function getSha256Hash()
  {
    return $this->sha256Hash;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_CloudSupport_Media');
