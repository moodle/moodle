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

namespace Google\Service\MigrationCenterAPI;

class UploadFileInfo extends \Google\Model
{
  /**
   * Output only. The headers that were used to sign the URI.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Output only. Upload URI for the file.
   *
   * @var string
   */
  public $signedUri;
  /**
   * Output only. Expiration time of the upload URI.
   *
   * @var string
   */
  public $uriExpirationTime;

  /**
   * Output only. The headers that were used to sign the URI.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Output only. Upload URI for the file.
   *
   * @param string $signedUri
   */
  public function setSignedUri($signedUri)
  {
    $this->signedUri = $signedUri;
  }
  /**
   * @return string
   */
  public function getSignedUri()
  {
    return $this->signedUri;
  }
  /**
   * Output only. Expiration time of the upload URI.
   *
   * @param string $uriExpirationTime
   */
  public function setUriExpirationTime($uriExpirationTime)
  {
    $this->uriExpirationTime = $uriExpirationTime;
  }
  /**
   * @return string
   */
  public function getUriExpirationTime()
  {
    return $this->uriExpirationTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UploadFileInfo::class, 'Google_Service_MigrationCenterAPI_UploadFileInfo');
