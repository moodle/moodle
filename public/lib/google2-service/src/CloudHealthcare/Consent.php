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

class Consent extends \Google\Collection
{
  /**
   * No state specified. Treated as ACTIVE only at the time of resource
   * creation.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Consent is active and is considered when evaluating a user's consent on
   * resources.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The archived state is currently not being used.
   */
  public const STATE_ARCHIVED = 'ARCHIVED';
  /**
   * A revoked Consent is not considered when evaluating a user's consent on
   * resources.
   */
  public const STATE_REVOKED = 'REVOKED';
  /**
   * A draft Consent is not considered when evaluating a user's consent on
   * resources unless explicitly specified.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * When a draft Consent is rejected by a user, it is set to a rejected state.
   * A rejected Consent is not considered when evaluating a user's consent on
   * resources.
   */
  public const STATE_REJECTED = 'REJECTED';
  protected $collection_key = 'policies';
  /**
   * Required. The resource name of the Consent artifact that contains proof of
   * the end user's consent, of the form `projects/{project_id}/locations/{locat
   * ion_id}/datasets/{dataset_id}/consentStores/{consent_store_id}/consentArtif
   * acts/{consent_artifact_id}`.
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
   * Optional. User-supplied key-value pairs used to organize Consent resources.
   * Metadata keys must: - be between 1 and 63 characters long - have a UTF-8
   * encoding of maximum 128 bytes - begin with a letter - consist of up to 63
   * characters including lowercase letters, numeric characters, underscores,
   * and dashes Metadata values must be: - be between 1 and 63 characters long -
   * have a UTF-8 encoding of maximum 128 bytes - consist of up to 63 characters
   * including lowercase letters, numeric characters, underscores, and dashes No
   * more than 64 metadata entries can be associated with a given consent.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Identifier. Resource name of the Consent, of the form `projects/{project_id
   * }/locations/{location_id}/datasets/{dataset_id}/consentStores/{consent_stor
   * e_id}/consents/{consent_id}`. Cannot be changed after creation.
   *
   * @var string
   */
  public $name;
  protected $policiesType = GoogleCloudHealthcareV1ConsentPolicy::class;
  protected $policiesDataType = 'array';
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. The revision ID of the Consent. The format is an 8-character
   * hexadecimal string. Refer to a specific revision of a Consent by appending
   * `@{revision_id}` to the Consent's resource name.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Required. Indicates the current state of this Consent.
   *
   * @var string
   */
  public $state;
  /**
   * Input only. The time to live for this Consent from when it is created.
   *
   * @var string
   */
  public $ttl;
  /**
   * Required. User's UUID provided by the client.
   *
   * @var string
   */
  public $userId;

  /**
   * Required. The resource name of the Consent artifact that contains proof of
   * the end user's consent, of the form `projects/{project_id}/locations/{locat
   * ion_id}/datasets/{dataset_id}/consentStores/{consent_store_id}/consentArtif
   * acts/{consent_artifact_id}`.
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
   * Optional. User-supplied key-value pairs used to organize Consent resources.
   * Metadata keys must: - be between 1 and 63 characters long - have a UTF-8
   * encoding of maximum 128 bytes - begin with a letter - consist of up to 63
   * characters including lowercase letters, numeric characters, underscores,
   * and dashes Metadata values must be: - be between 1 and 63 characters long -
   * have a UTF-8 encoding of maximum 128 bytes - consist of up to 63 characters
   * including lowercase letters, numeric characters, underscores, and dashes No
   * more than 64 metadata entries can be associated with a given consent.
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
   * Identifier. Resource name of the Consent, of the form `projects/{project_id
   * }/locations/{location_id}/datasets/{dataset_id}/consentStores/{consent_stor
   * e_id}/consents/{consent_id}`. Cannot be changed after creation.
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
   * Optional. Represents a user's consent in terms of the resources that can be
   * accessed and under what conditions.
   *
   * @param GoogleCloudHealthcareV1ConsentPolicy[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return GoogleCloudHealthcareV1ConsentPolicy[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. The revision ID of the Consent. The format is an 8-character
   * hexadecimal string. Refer to a specific revision of a Consent by appending
   * `@{revision_id}` to the Consent's resource name.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Required. Indicates the current state of this Consent.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, ARCHIVED, REVOKED, DRAFT,
   * REJECTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Input only. The time to live for this Consent from when it is created.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Consent::class, 'Google_Service_CloudHealthcare_Consent');
