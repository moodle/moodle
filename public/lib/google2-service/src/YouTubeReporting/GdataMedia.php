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

class GdataMedia extends \Google\Collection
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
  public const REFERENCE_TYPE_GET_MEDIA = 'GET_MEDIA';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_COMPOSITE_MEDIA = 'COMPOSITE_MEDIA';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_BIGSTORE_REF = 'BIGSTORE_REF';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_DIFF_VERSION_RESPONSE = 'DIFF_VERSION_RESPONSE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_DIFF_CHECKSUMS_RESPONSE = 'DIFF_CHECKSUMS_RESPONSE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_DIFF_DOWNLOAD_RESPONSE = 'DIFF_DOWNLOAD_RESPONSE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_DIFF_UPLOAD_REQUEST = 'DIFF_UPLOAD_REQUEST';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_DIFF_UPLOAD_RESPONSE = 'DIFF_UPLOAD_RESPONSE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_COSMO_BINARY_REFERENCE = 'COSMO_BINARY_REFERENCE';
  /**
   * gdata
   */
  public const REFERENCE_TYPE_ARBITRARY_BYTES = 'ARBITRARY_BYTES';
  protected $collection_key = 'compositeMedia';
  /**
   * gdata
   *
   * @deprecated
   * @var string
   */
  public $algorithm;
  /**
   * gdata
   *
   * @deprecated
   * @var string
   */
  public $bigstoreObjectRef;
  /**
   * gdata
   *
   * @deprecated
   * @var string
   */
  public $blobRef;
  protected $blobstore2InfoType = GdataBlobstore2Info::class;
  protected $blobstore2InfoDataType = '';
  protected $compositeMediaType = GdataCompositeMedia::class;
  protected $compositeMediaDataType = 'array';
  /**
   * gdata
   *
   * @var string
   */
  public $contentType;
  protected $contentTypeInfoType = GdataContentTypeInfo::class;
  protected $contentTypeInfoDataType = '';
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
  protected $diffChecksumsResponseType = GdataDiffChecksumsResponse::class;
  protected $diffChecksumsResponseDataType = '';
  protected $diffDownloadResponseType = GdataDiffDownloadResponse::class;
  protected $diffDownloadResponseDataType = '';
  protected $diffUploadRequestType = GdataDiffUploadRequest::class;
  protected $diffUploadRequestDataType = '';
  protected $diffUploadResponseType = GdataDiffUploadResponse::class;
  protected $diffUploadResponseDataType = '';
  protected $diffVersionResponseType = GdataDiffVersionResponse::class;
  protected $diffVersionResponseDataType = '';
  protected $downloadParametersType = GdataDownloadParameters::class;
  protected $downloadParametersDataType = '';
  /**
   * gdata
   *
   * @var string
   */
  public $filename;
  /**
   * gdata
   *
   * @deprecated
   * @var string
   */
  public $hash;
  /**
   * gdata
   *
   * @var bool
   */
  public $hashVerified;
  /**
   * gdata
   *
   * @var string
   */
  public $inline;
  /**
   * gdata
   *
   * @var bool
   */
  public $isPotentialRetry;
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
  /**
   * gdata
   *
   * @var string
   */
  public $mediaId;
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
   * @var string
   */
  public $sha256Hash;
  /**
   * gdata
   *
   * @var string
   */
  public $timestamp;
  /**
   * gdata
   *
   * @var string
   */
  public $token;

  /**
   * gdata
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
   * gdata
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
   * @param GdataCompositeMedia[] $compositeMedia
   */
  public function setCompositeMedia($compositeMedia)
  {
    $this->compositeMedia = $compositeMedia;
  }
  /**
   * @return GdataCompositeMedia[]
   */
  public function getCompositeMedia()
  {
    return $this->compositeMedia;
  }
  /**
   * gdata
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
   * gdata
   *
   * @param GdataContentTypeInfo $contentTypeInfo
   */
  public function setContentTypeInfo(GdataContentTypeInfo $contentTypeInfo)
  {
    $this->contentTypeInfo = $contentTypeInfo;
  }
  /**
   * @return GdataContentTypeInfo
   */
  public function getContentTypeInfo()
  {
    return $this->contentTypeInfo;
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
   * @param GdataDiffChecksumsResponse $diffChecksumsResponse
   */
  public function setDiffChecksumsResponse(GdataDiffChecksumsResponse $diffChecksumsResponse)
  {
    $this->diffChecksumsResponse = $diffChecksumsResponse;
  }
  /**
   * @return GdataDiffChecksumsResponse
   */
  public function getDiffChecksumsResponse()
  {
    return $this->diffChecksumsResponse;
  }
  /**
   * gdata
   *
   * @param GdataDiffDownloadResponse $diffDownloadResponse
   */
  public function setDiffDownloadResponse(GdataDiffDownloadResponse $diffDownloadResponse)
  {
    $this->diffDownloadResponse = $diffDownloadResponse;
  }
  /**
   * @return GdataDiffDownloadResponse
   */
  public function getDiffDownloadResponse()
  {
    return $this->diffDownloadResponse;
  }
  /**
   * gdata
   *
   * @param GdataDiffUploadRequest $diffUploadRequest
   */
  public function setDiffUploadRequest(GdataDiffUploadRequest $diffUploadRequest)
  {
    $this->diffUploadRequest = $diffUploadRequest;
  }
  /**
   * @return GdataDiffUploadRequest
   */
  public function getDiffUploadRequest()
  {
    return $this->diffUploadRequest;
  }
  /**
   * gdata
   *
   * @param GdataDiffUploadResponse $diffUploadResponse
   */
  public function setDiffUploadResponse(GdataDiffUploadResponse $diffUploadResponse)
  {
    $this->diffUploadResponse = $diffUploadResponse;
  }
  /**
   * @return GdataDiffUploadResponse
   */
  public function getDiffUploadResponse()
  {
    return $this->diffUploadResponse;
  }
  /**
   * gdata
   *
   * @param GdataDiffVersionResponse $diffVersionResponse
   */
  public function setDiffVersionResponse(GdataDiffVersionResponse $diffVersionResponse)
  {
    $this->diffVersionResponse = $diffVersionResponse;
  }
  /**
   * @return GdataDiffVersionResponse
   */
  public function getDiffVersionResponse()
  {
    return $this->diffVersionResponse;
  }
  /**
   * gdata
   *
   * @param GdataDownloadParameters $downloadParameters
   */
  public function setDownloadParameters(GdataDownloadParameters $downloadParameters)
  {
    $this->downloadParameters = $downloadParameters;
  }
  /**
   * @return GdataDownloadParameters
   */
  public function getDownloadParameters()
  {
    return $this->downloadParameters;
  }
  /**
   * gdata
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
   * gdata
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
   * gdata
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
  /**
   * gdata
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
   * gdata
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
   * gdata
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
class_alias(GdataMedia::class, 'Google_Service_YouTubeReporting_GdataMedia');
