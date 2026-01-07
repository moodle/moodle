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

namespace Google\Service\Walletobjects;

class TransitObjectUploadRotatingBarcodeValuesRequest extends \Google\Model
{
  protected $blobType = Media::class;
  protected $blobDataType = '';
  protected $mediaRequestInfoType = MediaRequestInfo::class;
  protected $mediaRequestInfoDataType = '';

  /**
   * A reference to the rotating barcode values payload that was uploaded.
   *
   * @param Media $blob
   */
  public function setBlob(Media $blob)
  {
    $this->blob = $blob;
  }
  /**
   * @return Media
   */
  public function getBlob()
  {
    return $this->blob;
  }
  /**
   * Extra information about the uploaded media.
   *
   * @param MediaRequestInfo $mediaRequestInfo
   */
  public function setMediaRequestInfo(MediaRequestInfo $mediaRequestInfo)
  {
    $this->mediaRequestInfo = $mediaRequestInfo;
  }
  /**
   * @return MediaRequestInfo
   */
  public function getMediaRequestInfo()
  {
    return $this->mediaRequestInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransitObjectUploadRotatingBarcodeValuesRequest::class, 'Google_Service_Walletobjects_TransitObjectUploadRotatingBarcodeValuesRequest');
