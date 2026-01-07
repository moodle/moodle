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

class ExpansionFile extends \Google\Model
{
  /**
   * If set, this field indicates that this APK has an expansion file uploaded
   * to it: this APK does not reference another APK's expansion file. The
   * field's value is the size of the uploaded expansion file in bytes.
   *
   * @var string
   */
  public $fileSize;
  /**
   * If set, this APK's expansion file references another APK's expansion file.
   * The file_size field will not be set.
   *
   * @var int
   */
  public $referencesVersion;

  /**
   * If set, this field indicates that this APK has an expansion file uploaded
   * to it: this APK does not reference another APK's expansion file. The
   * field's value is the size of the uploaded expansion file in bytes.
   *
   * @param string $fileSize
   */
  public function setFileSize($fileSize)
  {
    $this->fileSize = $fileSize;
  }
  /**
   * @return string
   */
  public function getFileSize()
  {
    return $this->fileSize;
  }
  /**
   * If set, this APK's expansion file references another APK's expansion file.
   * The file_size field will not be set.
   *
   * @param int $referencesVersion
   */
  public function setReferencesVersion($referencesVersion)
  {
    $this->referencesVersion = $referencesVersion;
  }
  /**
   * @return int
   */
  public function getReferencesVersion()
  {
    return $this->referencesVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExpansionFile::class, 'Google_Service_AndroidPublisher_ExpansionFile');
