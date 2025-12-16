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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification extends \Google\Collection
{
  protected $collection_key = 'encryptedLeakMatchPrefixes';
  /**
   * Output only. List of prefixes of the encrypted potential password leaks
   * that matched the given parameters. They must be compared with the client-
   * side decryption prefix of `reencrypted_user_credentials_hash`
   *
   * @var string[]
   */
  public $encryptedLeakMatchPrefixes;
  /**
   * Optional. Encrypted Scrypt hash of the canonicalized username+password. It
   * is re-encrypted by the server and returned through
   * `reencrypted_user_credentials_hash`.
   *
   * @var string
   */
  public $encryptedUserCredentialsHash;
  /**
   * Required. Exactly 26-bit prefix of the SHA-256 hash of the canonicalized
   * username. It is used to look up password leaks associated with that hash
   * prefix.
   *
   * @var string
   */
  public $lookupHashPrefix;
  /**
   * Output only. Corresponds to the re-encryption of the
   * `encrypted_user_credentials_hash` field. It is used to match potential
   * password leaks within `encrypted_leak_match_prefixes`.
   *
   * @var string
   */
  public $reencryptedUserCredentialsHash;

  /**
   * Output only. List of prefixes of the encrypted potential password leaks
   * that matched the given parameters. They must be compared with the client-
   * side decryption prefix of `reencrypted_user_credentials_hash`
   *
   * @param string[] $encryptedLeakMatchPrefixes
   */
  public function setEncryptedLeakMatchPrefixes($encryptedLeakMatchPrefixes)
  {
    $this->encryptedLeakMatchPrefixes = $encryptedLeakMatchPrefixes;
  }
  /**
   * @return string[]
   */
  public function getEncryptedLeakMatchPrefixes()
  {
    return $this->encryptedLeakMatchPrefixes;
  }
  /**
   * Optional. Encrypted Scrypt hash of the canonicalized username+password. It
   * is re-encrypted by the server and returned through
   * `reencrypted_user_credentials_hash`.
   *
   * @param string $encryptedUserCredentialsHash
   */
  public function setEncryptedUserCredentialsHash($encryptedUserCredentialsHash)
  {
    $this->encryptedUserCredentialsHash = $encryptedUserCredentialsHash;
  }
  /**
   * @return string
   */
  public function getEncryptedUserCredentialsHash()
  {
    return $this->encryptedUserCredentialsHash;
  }
  /**
   * Required. Exactly 26-bit prefix of the SHA-256 hash of the canonicalized
   * username. It is used to look up password leaks associated with that hash
   * prefix.
   *
   * @param string $lookupHashPrefix
   */
  public function setLookupHashPrefix($lookupHashPrefix)
  {
    $this->lookupHashPrefix = $lookupHashPrefix;
  }
  /**
   * @return string
   */
  public function getLookupHashPrefix()
  {
    return $this->lookupHashPrefix;
  }
  /**
   * Output only. Corresponds to the re-encryption of the
   * `encrypted_user_credentials_hash` field. It is used to match potential
   * password leaks within `encrypted_leak_match_prefixes`.
   *
   * @param string $reencryptedUserCredentialsHash
   */
  public function setReencryptedUserCredentialsHash($reencryptedUserCredentialsHash)
  {
    $this->reencryptedUserCredentialsHash = $reencryptedUserCredentialsHash;
  }
  /**
   * @return string
   */
  public function getReencryptedUserCredentialsHash()
  {
    return $this->reencryptedUserCredentialsHash;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1PrivatePasswordLeakVerification');
