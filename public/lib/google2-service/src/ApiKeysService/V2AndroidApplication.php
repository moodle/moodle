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

namespace Google\Service\ApiKeysService;

class V2AndroidApplication extends \Google\Model
{
  /**
   * The package name of the application.
   *
   * @var string
   */
  public $packageName;
  /**
   * The SHA1 fingerprint of the application. For example, both sha1 formats are
   * acceptable : DA:39:A3:EE:5E:6B:4B:0D:32:55:BF:EF:95:60:18:90:AF:D8:07:09 or
   * DA39A3EE5E6B4B0D3255BFEF95601890AFD80709. Output format is the latter.
   *
   * @var string
   */
  public $sha1Fingerprint;

  /**
   * The package name of the application.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * The SHA1 fingerprint of the application. For example, both sha1 formats are
   * acceptable : DA:39:A3:EE:5E:6B:4B:0D:32:55:BF:EF:95:60:18:90:AF:D8:07:09 or
   * DA39A3EE5E6B4B0D3255BFEF95601890AFD80709. Output format is the latter.
   *
   * @param string $sha1Fingerprint
   */
  public function setSha1Fingerprint($sha1Fingerprint)
  {
    $this->sha1Fingerprint = $sha1Fingerprint;
  }
  /**
   * @return string
   */
  public function getSha1Fingerprint()
  {
    return $this->sha1Fingerprint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V2AndroidApplication::class, 'Google_Service_ApiKeysService_V2AndroidApplication');
