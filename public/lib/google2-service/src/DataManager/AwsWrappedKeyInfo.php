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

namespace Google\Service\DataManager;

class AwsWrappedKeyInfo extends \Google\Model
{
  /**
   * Unspecified key type. Should never be used.
   */
  public const KEY_TYPE_KEY_TYPE_UNSPECIFIED = 'KEY_TYPE_UNSPECIFIED';
  /**
   * Algorithm XChaCha20-Poly1305
   */
  public const KEY_TYPE_XCHACHA20_POLY1305 = 'XCHACHA20_POLY1305';
  /**
   * Required. The base64 encoded encrypted data encryption key.
   *
   * @var string
   */
  public $encryptedDek;
  /**
   * Required. The URI of the AWS KMS key used to decrypt the DEK. Should be in
   * the format of `arn:{partition}:kms:{region}:{account_id}:key/{key_id}` or
   * `aws-kms://arn:{partition}:kms:{region}:{account_id}:key/{key_id}`
   *
   * @var string
   */
  public $kekUri;
  /**
   * Required. The type of algorithm used to encrypt the data.
   *
   * @var string
   */
  public $keyType;
  /**
   * Required. The Amazon Resource Name of the IAM Role to assume for KMS
   * decryption access. Should be in the format of
   * `arn:{partition}:iam::{account_id}:role/{role_name}`
   *
   * @var string
   */
  public $roleArn;

  /**
   * Required. The base64 encoded encrypted data encryption key.
   *
   * @param string $encryptedDek
   */
  public function setEncryptedDek($encryptedDek)
  {
    $this->encryptedDek = $encryptedDek;
  }
  /**
   * @return string
   */
  public function getEncryptedDek()
  {
    return $this->encryptedDek;
  }
  /**
   * Required. The URI of the AWS KMS key used to decrypt the DEK. Should be in
   * the format of `arn:{partition}:kms:{region}:{account_id}:key/{key_id}` or
   * `aws-kms://arn:{partition}:kms:{region}:{account_id}:key/{key_id}`
   *
   * @param string $kekUri
   */
  public function setKekUri($kekUri)
  {
    $this->kekUri = $kekUri;
  }
  /**
   * @return string
   */
  public function getKekUri()
  {
    return $this->kekUri;
  }
  /**
   * Required. The type of algorithm used to encrypt the data.
   *
   * Accepted values: KEY_TYPE_UNSPECIFIED, XCHACHA20_POLY1305
   *
   * @param self::KEY_TYPE_* $keyType
   */
  public function setKeyType($keyType)
  {
    $this->keyType = $keyType;
  }
  /**
   * @return self::KEY_TYPE_*
   */
  public function getKeyType()
  {
    return $this->keyType;
  }
  /**
   * Required. The Amazon Resource Name of the IAM Role to assume for KMS
   * decryption access. Should be in the format of
   * `arn:{partition}:iam::{account_id}:role/{role_name}`
   *
   * @param string $roleArn
   */
  public function setRoleArn($roleArn)
  {
    $this->roleArn = $roleArn;
  }
  /**
   * @return string
   */
  public function getRoleArn()
  {
    return $this->roleArn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsWrappedKeyInfo::class, 'Google_Service_DataManager_AwsWrappedKeyInfo');
