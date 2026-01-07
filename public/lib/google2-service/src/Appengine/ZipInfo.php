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

class ZipInfo extends \Google\Model
{
  /**
   * An estimate of the number of files in a zip for a zip deployment. If set,
   * must be greater than or equal to the actual number of files. Used for
   * optimizing performance; if not provided, deployment may be slow.
   *
   * @var int
   */
  public $filesCount;
  /**
   * URL of the zip file to deploy from. Must be a URL to a resource in Google
   * Cloud Storage in the form 'http(s)://storage.googleapis.com//'.
   *
   * @var string
   */
  public $sourceUrl;

  /**
   * An estimate of the number of files in a zip for a zip deployment. If set,
   * must be greater than or equal to the actual number of files. Used for
   * optimizing performance; if not provided, deployment may be slow.
   *
   * @param int $filesCount
   */
  public function setFilesCount($filesCount)
  {
    $this->filesCount = $filesCount;
  }
  /**
   * @return int
   */
  public function getFilesCount()
  {
    return $this->filesCount;
  }
  /**
   * URL of the zip file to deploy from. Must be a URL to a resource in Google
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
class_alias(ZipInfo::class, 'Google_Service_Appengine_ZipInfo');
