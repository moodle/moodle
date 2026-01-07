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

namespace Google\Service\Looker;

class ExportInstanceRequest extends \Google\Model
{
  protected $encryptionConfigType = ExportEncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * The path to the folder in Google Cloud Storage where the export will be
   * stored. The URI is in the form `gs://bucketName/folderName`.
   *
   * @var string
   */
  public $gcsUri;

  /**
   * Required. Encryption configuration (CMEK). For CMEK enabled instances it
   * should be same as looker CMEK.
   *
   * @param ExportEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(ExportEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return ExportEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * The path to the folder in Google Cloud Storage where the export will be
   * stored. The URI is in the form `gs://bucketName/folderName`.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportInstanceRequest::class, 'Google_Service_Looker_ExportInstanceRequest');
