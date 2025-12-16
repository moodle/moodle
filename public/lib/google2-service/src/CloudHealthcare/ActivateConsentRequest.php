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

class ActivateConsentRequest extends \Google\Model
{
  /**
   * Required. The resource name of the Consent artifact that contains
   * documentation of the user's consent, of the form `projects/{project_id}/loc
   * ations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_id}
   * /consentArtifacts/{consent_artifact_id}`. If the draft Consent had a
   * Consent artifact, this Consent artifact overwrites it.
   *
   * @var string
   */
  public $consentArtifact;
  /**
   * Timestamp in UTC of when this Consent is considered expired.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The time to live for this Consent from when it is marked as active.
   *
   * @var string
   */
  public $ttl;

  /**
   * Required. The resource name of the Consent artifact that contains
   * documentation of the user's consent, of the form `projects/{project_id}/loc
   * ations/{location_id}/datasets/{dataset_id}/consentStores/{consent_store_id}
   * /consentArtifacts/{consent_artifact_id}`. If the draft Consent had a
   * Consent artifact, this Consent artifact overwrites it.
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
  /**
   * Timestamp in UTC of when this Consent is considered expired.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The time to live for this Consent from when it is marked as active.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivateConsentRequest::class, 'Google_Service_CloudHealthcare_ActivateConsentRequest');
