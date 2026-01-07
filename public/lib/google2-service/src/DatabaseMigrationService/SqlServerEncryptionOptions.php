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

namespace Google\Service\DatabaseMigrationService;

class SqlServerEncryptionOptions extends \Google\Model
{
  /**
   * Required. Path to the Certificate (.cer) in Cloud Storage, in the form
   * `gs://bucketName/fileName`. The instance must have write permissions to the
   * bucket and read access to the file.
   *
   * @var string
   */
  public $certPath;
  /**
   * Required. Input only. Password that encrypts the private key.
   *
   * @var string
   */
  public $pvkPassword;
  /**
   * Required. Path to the Certificate Private Key (.pvk) in Cloud Storage, in
   * the form `gs://bucketName/fileName`. The instance must have write
   * permissions to the bucket and read access to the file.
   *
   * @var string
   */
  public $pvkPath;

  /**
   * Required. Path to the Certificate (.cer) in Cloud Storage, in the form
   * `gs://bucketName/fileName`. The instance must have write permissions to the
   * bucket and read access to the file.
   *
   * @param string $certPath
   */
  public function setCertPath($certPath)
  {
    $this->certPath = $certPath;
  }
  /**
   * @return string
   */
  public function getCertPath()
  {
    return $this->certPath;
  }
  /**
   * Required. Input only. Password that encrypts the private key.
   *
   * @param string $pvkPassword
   */
  public function setPvkPassword($pvkPassword)
  {
    $this->pvkPassword = $pvkPassword;
  }
  /**
   * @return string
   */
  public function getPvkPassword()
  {
    return $this->pvkPassword;
  }
  /**
   * Required. Path to the Certificate Private Key (.pvk) in Cloud Storage, in
   * the form `gs://bucketName/fileName`. The instance must have write
   * permissions to the bucket and read access to the file.
   *
   * @param string $pvkPath
   */
  public function setPvkPath($pvkPath)
  {
    $this->pvkPath = $pvkPath;
  }
  /**
   * @return string
   */
  public function getPvkPath()
  {
    return $this->pvkPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerEncryptionOptions::class, 'Google_Service_DatabaseMigrationService_SqlServerEncryptionOptions');
