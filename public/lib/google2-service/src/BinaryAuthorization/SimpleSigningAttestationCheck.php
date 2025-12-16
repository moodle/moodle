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

class SimpleSigningAttestationCheck extends \Google\Collection
{
  protected $collection_key = 'containerAnalysisAttestationProjects';
  protected $attestationAuthenticatorsType = AttestationAuthenticator::class;
  protected $attestationAuthenticatorsDataType = 'array';
  /**
   * Optional. The projects where attestations are stored as Container Analysis
   * Occurrences, in the format `projects/[PROJECT_ID]`. Only one attestation
   * needs to successfully verify an image for this check to pass, so a single
   * verified attestation found in any of
   * `container_analysis_attestation_projects` is sufficient for the check to
   * pass. A project ID must be used, not a project number. When fetching
   * Occurrences from Container Analysis, only `AttestationOccurrence` kinds are
   * considered. In the future, additional Occurrence kinds may be added to the
   * query. Maximum number of `container_analysis_attestation_projects` allowed
   * in each `SimpleSigningAttestationCheck` is 10.
   *
   * @var string[]
   */
  public $containerAnalysisAttestationProjects;

  /**
   * Required. The authenticators required by this check to verify an
   * attestation. Typically this is one or more PKIX public keys for signature
   * verification. Only one authenticator needs to consider an attestation
   * verified in order for an attestation to be considered fully authenticated.
   * In otherwords, this list of authenticators is an "OR" of the authenticator
   * results. At least one authenticator is required.
   *
   * @param AttestationAuthenticator[] $attestationAuthenticators
   */
  public function setAttestationAuthenticators($attestationAuthenticators)
  {
    $this->attestationAuthenticators = $attestationAuthenticators;
  }
  /**
   * @return AttestationAuthenticator[]
   */
  public function getAttestationAuthenticators()
  {
    return $this->attestationAuthenticators;
  }
  /**
   * Optional. The projects where attestations are stored as Container Analysis
   * Occurrences, in the format `projects/[PROJECT_ID]`. Only one attestation
   * needs to successfully verify an image for this check to pass, so a single
   * verified attestation found in any of
   * `container_analysis_attestation_projects` is sufficient for the check to
   * pass. A project ID must be used, not a project number. When fetching
   * Occurrences from Container Analysis, only `AttestationOccurrence` kinds are
   * considered. In the future, additional Occurrence kinds may be added to the
   * query. Maximum number of `container_analysis_attestation_projects` allowed
   * in each `SimpleSigningAttestationCheck` is 10.
   *
   * @param string[] $containerAnalysisAttestationProjects
   */
  public function setContainerAnalysisAttestationProjects($containerAnalysisAttestationProjects)
  {
    $this->containerAnalysisAttestationProjects = $containerAnalysisAttestationProjects;
  }
  /**
   * @return string[]
   */
  public function getContainerAnalysisAttestationProjects()
  {
    return $this->containerAnalysisAttestationProjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SimpleSigningAttestationCheck::class, 'Google_Service_BinaryAuthorization_SimpleSigningAttestationCheck');
