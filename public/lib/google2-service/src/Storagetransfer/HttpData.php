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

namespace Google\Service\Storagetransfer;

class HttpData extends \Google\Model
{
  /**
   * Required. The URL that points to the file that stores the object list
   * entries. This file must allow public access. The URL is either an
   * HTTP/HTTPS address (e.g. `https://example.com/urllist.tsv`) or a Cloud
   * Storage path (e.g. `gs://my-bucket/urllist.tsv`).
   *
   * @var string
   */
  public $listUrl;

  /**
   * Required. The URL that points to the file that stores the object list
   * entries. This file must allow public access. The URL is either an
   * HTTP/HTTPS address (e.g. `https://example.com/urllist.tsv`) or a Cloud
   * Storage path (e.g. `gs://my-bucket/urllist.tsv`).
   *
   * @param string $listUrl
   */
  public function setListUrl($listUrl)
  {
    $this->listUrl = $listUrl;
  }
  /**
   * @return string
   */
  public function getListUrl()
  {
    return $this->listUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpData::class, 'Google_Service_Storagetransfer_HttpData');
