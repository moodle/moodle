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

namespace Google\Service\FirebaseAppDistribution;

class GdataBlobstore2Info extends \Google\Model
{
  /**
   * The blob generation id.
   *
   * @var string
   */
  public $blobGeneration;
  /**
   * The blob id, e.g., /blobstore/prod/playground/scotty
   *
   * @var string
   */
  public $blobId;
  /**
   * A serialized External Read Token passed from Bigstore -> Scotty for a GCS
   * download. This field must never be consumed outside of Bigstore, and is not
   * applicable to non-GCS media uploads.
   *
   * @var string
   */
  public $downloadExternalReadToken;
  /**
   * Read handle passed from Bigstore -> Scotty for a GCS download. This is a
   * signed, serialized blobstore2.ReadHandle proto which must never be set
   * outside of Bigstore, and is not applicable to non-GCS media downloads.
   *
   * @var string
   */
  public $downloadReadHandle;
  /**
   * The blob read token. Needed to read blobs that have not been replicated.
   * Might not be available until the final call.
   *
   * @var string
   */
  public $readToken;
  /**
   * Metadata passed from Blobstore -> Scotty for a new GCS upload. This is a
   * signed, serialized blobstore2.BlobMetadataContainer proto which must never
   * be consumed outside of Bigstore, and is not applicable to non-GCS media
   * uploads.
   *
   * @var string
   */
  public $uploadMetadataContainer;

  /**
   * The blob generation id.
   *
   * @param string $blobGeneration
   */
  public function setBlobGeneration($blobGeneration)
  {
    $this->blobGeneration = $blobGeneration;
  }
  /**
   * @return string
   */
  public function getBlobGeneration()
  {
    return $this->blobGeneration;
  }
  /**
   * The blob id, e.g., /blobstore/prod/playground/scotty
   *
   * @param string $blobId
   */
  public function setBlobId($blobId)
  {
    $this->blobId = $blobId;
  }
  /**
   * @return string
   */
  public function getBlobId()
  {
    return $this->blobId;
  }
  /**
   * A serialized External Read Token passed from Bigstore -> Scotty for a GCS
   * download. This field must never be consumed outside of Bigstore, and is not
   * applicable to non-GCS media uploads.
   *
   * @param string $downloadExternalReadToken
   */
  public function setDownloadExternalReadToken($downloadExternalReadToken)
  {
    $this->downloadExternalReadToken = $downloadExternalReadToken;
  }
  /**
   * @return string
   */
  public function getDownloadExternalReadToken()
  {
    return $this->downloadExternalReadToken;
  }
  /**
   * Read handle passed from Bigstore -> Scotty for a GCS download. This is a
   * signed, serialized blobstore2.ReadHandle proto which must never be set
   * outside of Bigstore, and is not applicable to non-GCS media downloads.
   *
   * @param string $downloadReadHandle
   */
  public function setDownloadReadHandle($downloadReadHandle)
  {
    $this->downloadReadHandle = $downloadReadHandle;
  }
  /**
   * @return string
   */
  public function getDownloadReadHandle()
  {
    return $this->downloadReadHandle;
  }
  /**
   * The blob read token. Needed to read blobs that have not been replicated.
   * Might not be available until the final call.
   *
   * @param string $readToken
   */
  public function setReadToken($readToken)
  {
    $this->readToken = $readToken;
  }
  /**
   * @return string
   */
  public function getReadToken()
  {
    return $this->readToken;
  }
  /**
   * Metadata passed from Blobstore -> Scotty for a new GCS upload. This is a
   * signed, serialized blobstore2.BlobMetadataContainer proto which must never
   * be consumed outside of Bigstore, and is not applicable to non-GCS media
   * uploads.
   *
   * @param string $uploadMetadataContainer
   */
  public function setUploadMetadataContainer($uploadMetadataContainer)
  {
    $this->uploadMetadataContainer = $uploadMetadataContainer;
  }
  /**
   * @return string
   */
  public function getUploadMetadataContainer()
  {
    return $this->uploadMetadataContainer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataBlobstore2Info::class, 'Google_Service_FirebaseAppDistribution_GdataBlobstore2Info');
