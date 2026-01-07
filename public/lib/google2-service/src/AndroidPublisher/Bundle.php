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

namespace Google\Service\AndroidPublisher;

class Bundle extends \Google\Model
{
  /**
   * A sha1 hash of the upload payload, encoded as a hex string and matching the
   * output of the sha1sum command.
   *
   * @var string
   */
  public $sha1;
  /**
   * A sha256 hash of the upload payload, encoded as a hex string and matching
   * the output of the sha256sum command.
   *
   * @var string
   */
  public $sha256;
  /**
   * The version code of the Android App Bundle, as specified in the Android App
   * Bundle's base module APK manifest file.
   *
   * @var int
   */
  public $versionCode;

  /**
   * A sha1 hash of the upload payload, encoded as a hex string and matching the
   * output of the sha1sum command.
   *
   * @param string $sha1
   */
  public function setSha1($sha1)
  {
    $this->sha1 = $sha1;
  }
  /**
   * @return string
   */
  public function getSha1()
  {
    return $this->sha1;
  }
  /**
   * A sha256 hash of the upload payload, encoded as a hex string and matching
   * the output of the sha256sum command.
   *
   * @param string $sha256
   */
  public function setSha256($sha256)
  {
    $this->sha256 = $sha256;
  }
  /**
   * @return string
   */
  public function getSha256()
  {
    return $this->sha256;
  }
  /**
   * The version code of the Android App Bundle, as specified in the Android App
   * Bundle's base module APK manifest file.
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bundle::class, 'Google_Service_AndroidPublisher_Bundle');
