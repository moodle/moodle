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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1ConfigVariable extends \Google\Model
{
  /**
   * Value is a bool.
   *
   * @var bool
   */
  public $boolValue;
  protected $encryptionKeyValueType = GoogleCloudConnectorsV1EncryptionKey::class;
  protected $encryptionKeyValueDataType = '';
  /**
   * Value is an integer
   *
   * @var string
   */
  public $intValue;
  /**
   * Optional. Key of the config variable.
   *
   * @var string
   */
  public $key;
  protected $secretValueType = GoogleCloudConnectorsV1Secret::class;
  protected $secretValueDataType = '';
  /**
   * Value is a string.
   *
   * @var string
   */
  public $stringValue;

  /**
   * Value is a bool.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * Value is a Encryption Key.
   *
   * @param GoogleCloudConnectorsV1EncryptionKey $encryptionKeyValue
   */
  public function setEncryptionKeyValue(GoogleCloudConnectorsV1EncryptionKey $encryptionKeyValue)
  {
    $this->encryptionKeyValue = $encryptionKeyValue;
  }
  /**
   * @return GoogleCloudConnectorsV1EncryptionKey
   */
  public function getEncryptionKeyValue()
  {
    return $this->encryptionKeyValue;
  }
  /**
   * Value is an integer
   *
   * @param string $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * Optional. Key of the config variable.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Value is a secret.
   *
   * @param GoogleCloudConnectorsV1Secret $secretValue
   */
  public function setSecretValue(GoogleCloudConnectorsV1Secret $secretValue)
  {
    $this->secretValue = $secretValue;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getSecretValue()
  {
    return $this->secretValue;
  }
  /**
   * Value is a string.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1ConfigVariable::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1ConfigVariable');
