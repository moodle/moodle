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

namespace Google\Service\BinaryAuthorization;

class AttestationAuthenticator extends \Google\Model
{
  /**
   * Optional. A user-provided name for this `AttestationAuthenticator`. This
   * field has no effect on the policy evaluation behavior except to improve
   * readability of messages in evaluation results.
   *
   * @var string
   */
  public $displayName;
  protected $pkixPublicKeySetType = PkixPublicKeySet::class;
  protected $pkixPublicKeySetDataType = '';

  /**
   * Optional. A user-provided name for this `AttestationAuthenticator`. This
   * field has no effect on the policy evaluation behavior except to improve
   * readability of messages in evaluation results.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. A set of raw PKIX SubjectPublicKeyInfo format public keys. If any
   * public key in the set validates the attestation signature, then the
   * signature is considered authenticated (i.e. any one key is sufficient to
   * authenticate).
   *
   * @param PkixPublicKeySet $pkixPublicKeySet
   */
  public function setPkixPublicKeySet(PkixPublicKeySet $pkixPublicKeySet)
  {
    $this->pkixPublicKeySet = $pkixPublicKeySet;
  }
  /**
   * @return PkixPublicKeySet
   */
  public function getPkixPublicKeySet()
  {
    return $this->pkixPublicKeySet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttestationAuthenticator::class, 'Google_Service_BinaryAuthorization_AttestationAuthenticator');
