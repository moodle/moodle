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

namespace Google\Service\AndroidManagement;

class ExternalData extends \Google\Model
{
  /**
   * The base-64 encoded SHA-256 hash of the content hosted at url. If the
   * content doesn't match this hash, Android Device Policy won't use the data.
   *
   * @var string
   */
  public $sha256Hash;
  /**
   * The absolute URL to the data, which must use either the http or https
   * scheme. Android Device Policy doesn't provide any credentials in the GET
   * request, so the URL must be publicly accessible. Including a long, random
   * component in the URL may be used to prevent attackers from discovering the
   * URL.
   *
   * @var string
   */
  public $url;

  /**
   * The base-64 encoded SHA-256 hash of the content hosted at url. If the
   * content doesn't match this hash, Android Device Policy won't use the data.
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
   * The absolute URL to the data, which must use either the http or https
   * scheme. Android Device Policy doesn't provide any credentials in the GET
   * request, so the URL must be publicly accessible. Including a long, random
   * component in the URL may be used to prevent attackers from discovering the
   * URL.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalData::class, 'Google_Service_AndroidManagement_ExternalData');
