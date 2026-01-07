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

namespace Google\Service\Appengine;

class FileInfo extends \Google\Model
{
  /**
   * The MIME type of the file.Defaults to the value from Google Cloud Storage.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The SHA1 hash of the file, in hex.
   *
   * @var string
   */
  public $sha1Sum;
  /**
   * URL source to use to fetch this file. Must be a URL to a resource in Google
   * Cloud Storage in the form 'http(s)://storage.googleapis.com//'.
   *
   * @var string
   */
  public $sourceUrl;

  /**
   * The MIME type of the file.Defaults to the value from Google Cloud Storage.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The SHA1 hash of the file, in hex.
   *
   * @param string $sha1Sum
   */
  public function setSha1Sum($sha1Sum)
  {
    $this->sha1Sum = $sha1Sum;
  }
  /**
   * @return string
   */
  public function getSha1Sum()
  {
    return $this->sha1Sum;
  }
  /**
   * URL source to use to fetch this file. Must be a URL to a resource in Google
   * Cloud Storage in the form 'http(s)://storage.googleapis.com//'.
   *
   * @param string $sourceUrl
   */
  public function setSourceUrl($sourceUrl)
  {
    $this->sourceUrl = $sourceUrl;
  }
  /**
   * @return string
   */
  public function getSourceUrl()
  {
    return $this->sourceUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileInfo::class, 'Google_Service_Appengine_FileInfo');
