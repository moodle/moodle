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

namespace Google\Service\Directory;

class ChromeOsDevices extends \Google\Collection
{
  protected $collection_key = 'chromeosdevices';
  protected $chromeosdevicesType = ChromeOsDevice::class;
  protected $chromeosdevicesDataType = 'array';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;
  /**
   * Token used to access the next page of this result. To access the next page,
   * use this token's value in the `pageToken` query string of this request.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A list of Chrome OS Device objects.
   *
   * @param ChromeOsDevice[] $chromeosdevices
   */
  public function setChromeosdevices($chromeosdevices)
  {
    $this->chromeosdevices = $chromeosdevices;
  }
  /**
   * @return ChromeOsDevice[]
   */
  public function getChromeosdevices()
  {
    return $this->chromeosdevices;
  }
  /**
   * ETag of the resource.
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
   * Kind of resource this is.
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
   * Token used to access the next page of this result. To access the next page,
   * use this token's value in the `pageToken` query string of this request.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDevices::class, 'Google_Service_Directory_ChromeOsDevices');
