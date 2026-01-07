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

namespace Google\Service\CloudHealthcare;

class RevokeConsentRequest extends \Google\Model
{
  /**
   * Optional. The resource name of the Consent artifact that contains proof of
   * the user's revocation of the Consent, of the form `projects/{project_id}/lo
   * cations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_id
   * }/consentArtifacts/{consent_artifact_id}`.
   *
   * @var string
   */
  public $consentArtifact;

  /**
   * Optional. The resource name of the Consent artifact that contains proof of
   * the user's revocation of the Consent, of the form `projects/{project_id}/lo
   * cations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_id
   * }/consentArtifacts/{consent_artifact_id}`.
   *
   * @param string $consentArtifact
   */
  public function setConsentArtifact($consentArtifact)
  {
    $this->consentArtifact = $consentArtifact;
  }
  /**
   * @return string
   */
  public function getConsentArtifact()
  {
    return $this->consentArtifact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevokeConsentRequest::class, 'Google_Service_CloudHealthcare_RevokeConsentRequest');
