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

namespace Google\Service\Compute;

class ShieldedInstanceIdentity extends \Google\Model
{
  protected $eccP256EncryptionKeyType = ShieldedInstanceIdentityEntry::class;
  protected $eccP256EncryptionKeyDataType = '';
  protected $eccP256SigningKeyType = ShieldedInstanceIdentityEntry::class;
  protected $eccP256SigningKeyDataType = '';
  protected $encryptionKeyType = ShieldedInstanceIdentityEntry::class;
  protected $encryptionKeyDataType = '';
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#shieldedInstanceIdentity for shielded Instance identity
   * entry.
   *
   * @var string
   */
  public $kind;
  protected $signingKeyType = ShieldedInstanceIdentityEntry::class;
  protected $signingKeyDataType = '';

  /**
   * An Endorsement Key (EK) made by the ECC P256 algorithm issued to the
   * Shielded Instance's vTPM.
   *
   * @param ShieldedInstanceIdentityEntry $eccP256EncryptionKey
   */
  public function setEccP256EncryptionKey(ShieldedInstanceIdentityEntry $eccP256EncryptionKey)
  {
    $this->eccP256EncryptionKey = $eccP256EncryptionKey;
  }
  /**
   * @return ShieldedInstanceIdentityEntry
   */
  public function getEccP256EncryptionKey()
  {
    return $this->eccP256EncryptionKey;
  }
  /**
   * An Attestation Key (AK) made by the ECC P256 algorithm issued to the
   * Shielded Instance's vTPM.
   *
   * @param ShieldedInstanceIdentityEntry $eccP256SigningKey
   */
  public function setEccP256SigningKey(ShieldedInstanceIdentityEntry $eccP256SigningKey)
  {
    $this->eccP256SigningKey = $eccP256SigningKey;
  }
  /**
   * @return ShieldedInstanceIdentityEntry
   */
  public function getEccP256SigningKey()
  {
    return $this->eccP256SigningKey;
  }
  /**
   * An Endorsement Key (EK) made by the RSA 2048 algorithm issued to the
   * Shielded Instance's vTPM.
   *
   * @param ShieldedInstanceIdentityEntry $encryptionKey
   */
  public function setEncryptionKey(ShieldedInstanceIdentityEntry $encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return ShieldedInstanceIdentityEntry
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#shieldedInstanceIdentity for shielded Instance identity
   * entry.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * An Attestation Key (AK) made by the RSA 2048 algorithm issued to the
   * Shielded Instance's vTPM.
   *
   * @param ShieldedInstanceIdentityEntry $signingKey
   */
  public function setSigningKey(ShieldedInstanceIdentityEntry $signingKey)
  {
    $this->signingKey = $signingKey;
  }
  /**
   * @return ShieldedInstanceIdentityEntry
   */
  public function getSigningKey()
  {
    return $this->signingKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShieldedInstanceIdentity::class, 'Google_Service_Compute_ShieldedInstanceIdentity');
