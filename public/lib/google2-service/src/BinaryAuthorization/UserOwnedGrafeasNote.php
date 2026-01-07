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

class UserOwnedGrafeasNote extends \Google\Collection
{
  protected $collection_key = 'publicKeys';
  /**
   * Output only. This field will contain the service account email address that
   * this attestor will use as the principal when querying Container Analysis.
   * Attestor administrators must grant this service account the IAM role needed
   * to read attestations from the note_reference in Container Analysis
   * (`containeranalysis.notes.occurrences.viewer`). This email address is fixed
   * for the lifetime of the attestor, but callers should not make any other
   * assumptions about the service account email; future versions may use an
   * email based on a different naming pattern.
   *
   * @var string
   */
  public $delegationServiceAccountEmail;
  /**
   * Required. The Grafeas resource name of a Attestation.Authority Note,
   * created by the user, in the format: `projects/[PROJECT_ID]/notes`. This
   * field may not be updated. A project ID must be used, not a project number.
   * An attestation by this attestor is stored as a Grafeas
   * Attestation.Authority Occurrence that names a container image and that
   * links to this Note. Grafeas is an external dependency.
   *
   * @var string
   */
  public $noteReference;
  protected $publicKeysType = AttestorPublicKey::class;
  protected $publicKeysDataType = 'array';

  /**
   * Output only. This field will contain the service account email address that
   * this attestor will use as the principal when querying Container Analysis.
   * Attestor administrators must grant this service account the IAM role needed
   * to read attestations from the note_reference in Container Analysis
   * (`containeranalysis.notes.occurrences.viewer`). This email address is fixed
   * for the lifetime of the attestor, but callers should not make any other
   * assumptions about the service account email; future versions may use an
   * email based on a different naming pattern.
   *
   * @param string $delegationServiceAccountEmail
   */
  public function setDelegationServiceAccountEmail($delegationServiceAccountEmail)
  {
    $this->delegationServiceAccountEmail = $delegationServiceAccountEmail;
  }
  /**
   * @return string
   */
  public function getDelegationServiceAccountEmail()
  {
    return $this->delegationServiceAccountEmail;
  }
  /**
   * Required. The Grafeas resource name of a Attestation.Authority Note,
   * created by the user, in the format: `projects/[PROJECT_ID]/notes`. This
   * field may not be updated. A project ID must be used, not a project number.
   * An attestation by this attestor is stored as a Grafeas
   * Attestation.Authority Occurrence that names a container image and that
   * links to this Note. Grafeas is an external dependency.
   *
   * @param string $noteReference
   */
  public function setNoteReference($noteReference)
  {
    $this->noteReference = $noteReference;
  }
  /**
   * @return string
   */
  public function getNoteReference()
  {
    return $this->noteReference;
  }
  /**
   * Optional. Public keys that verify attestations signed by this attestor.
   * This field may be updated. If this field is non-empty, one of the specified
   * public keys must verify that an attestation was signed by this attestor for
   * the image specified in the admission request. If this field is empty, this
   * attestor always returns that no valid attestations exist.
   *
   * @param AttestorPublicKey[] $publicKeys
   */
  public function setPublicKeys($publicKeys)
  {
    $this->publicKeys = $publicKeys;
  }
  /**
   * @return AttestorPublicKey[]
   */
  public function getPublicKeys()
  {
    return $this->publicKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserOwnedGrafeasNote::class, 'Google_Service_BinaryAuthorization_UserOwnedGrafeasNote');
