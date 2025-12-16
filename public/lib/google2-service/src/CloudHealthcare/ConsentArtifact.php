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

class ConsentArtifact extends \Google\Collection
{
  protected $collection_key = 'consentContentScreenshots';
  protected $consentContentScreenshotsType = Image::class;
  protected $consentContentScreenshotsDataType = 'array';
  /**
   * Optional. An string indicating the version of the consent information shown
   * to the user.
   *
   * @var string
   */
  public $consentContentVersion;
  protected $guardianSignatureType = Signature::class;
  protected $guardianSignatureDataType = '';
  /**
   * Optional. Metadata associated with the Consent artifact. For example, the
   * consent locale or user agent version.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Identifier. Resource name of the Consent artifact, of the form `projects/{p
   * roject_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/{con
   * sent_store_id}/consentArtifacts/{consent_artifact_id}`. Cannot be changed
   * after creation.
   *
   * @var string
   */
  public $name;
  /**
   * Required. User's UUID provided by the client.
   *
   * @var string
   */
  public $userId;
  protected $userSignatureType = Signature::class;
  protected $userSignatureDataType = '';
  protected $witnessSignatureType = Signature::class;
  protected $witnessSignatureDataType = '';

  /**
   * Optional. Screenshots, PDFs, or other binary information documenting the
   * user's consent.
   *
   * @param Image[] $consentContentScreenshots
   */
  public function setConsentContentScreenshots($consentContentScreenshots)
  {
    $this->consentContentScreenshots = $consentContentScreenshots;
  }
  /**
   * @return Image[]
   */
  public function getConsentContentScreenshots()
  {
    return $this->consentContentScreenshots;
  }
  /**
   * Optional. An string indicating the version of the consent information shown
   * to the user.
   *
   * @param string $consentContentVersion
   */
  public function setConsentContentVersion($consentContentVersion)
  {
    $this->consentContentVersion = $consentContentVersion;
  }
  /**
   * @return string
   */
  public function getConsentContentVersion()
  {
    return $this->consentContentVersion;
  }
  /**
   * Optional. A signature from a guardian.
   *
   * @param Signature $guardianSignature
   */
  public function setGuardianSignature(Signature $guardianSignature)
  {
    $this->guardianSignature = $guardianSignature;
  }
  /**
   * @return Signature
   */
  public function getGuardianSignature()
  {
    return $this->guardianSignature;
  }
  /**
   * Optional. Metadata associated with the Consent artifact. For example, the
   * consent locale or user agent version.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Identifier. Resource name of the Consent artifact, of the form `projects/{p
   * roject_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/{con
   * sent_store_id}/consentArtifacts/{consent_artifact_id}`. Cannot be changed
   * after creation.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. User's UUID provided by the client.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
  /**
   * Optional. User's signature.
   *
   * @param Signature $userSignature
   */
  public function setUserSignature(Signature $userSignature)
  {
    $this->userSignature = $userSignature;
  }
  /**
   * @return Signature
   */
  public function getUserSignature()
  {
    return $this->userSignature;
  }
  /**
   * Optional. A signature from a witness.
   *
   * @param Signature $witnessSignature
   */
  public function setWitnessSignature(Signature $witnessSignature)
  {
    $this->witnessSignature = $witnessSignature;
  }
  /**
   * @return Signature
   */
  public function getWitnessSignature()
  {
    return $this->witnessSignature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsentArtifact::class, 'Google_Service_CloudHealthcare_ConsentArtifact');
