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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Api extends \Google\Collection
{
  protected $collection_key = 'versions';
  protected $apiFunctionalRequirementsType = GoogleCloudApihubV1AttributeValues::class;
  protected $apiFunctionalRequirementsDataType = '';
  protected $apiRequirementsType = GoogleCloudApihubV1AttributeValues::class;
  protected $apiRequirementsDataType = '';
  protected $apiStyleType = GoogleCloudApihubV1AttributeValues::class;
  protected $apiStyleDataType = '';
  protected $apiTechnicalRequirementsType = GoogleCloudApihubV1AttributeValues::class;
  protected $apiTechnicalRequirementsDataType = '';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  protected $businessUnitType = GoogleCloudApihubV1AttributeValues::class;
  protected $businessUnitDataType = '';
  /**
   * Output only. The time at which the API resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the API resource.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the API resource.
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  /**
   * Optional. Fingerprint of the API resource. This must be unique for each API
   * resource. It can neither be unset nor be updated to an existing fingerprint
   * of another API resource.
   *
   * @var string
   */
  public $fingerprint;
  protected $maturityLevelType = GoogleCloudApihubV1AttributeValues::class;
  protected $maturityLevelDataType = '';
  /**
   * Identifier. The name of the API resource in the API Hub. Format:
   * `projects/{project}/locations/{location}/apis/{api}`
   *
   * @var string
   */
  public $name;
  protected $ownerType = GoogleCloudApihubV1Owner::class;
  protected $ownerDataType = '';
  /**
   * Optional. The selected version for an API resource. This can be used when
   * special handling is needed on client side for particular version of the
   * API. Format is
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @var string
   */
  public $selectedVersion;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = 'array';
  protected $targetUserType = GoogleCloudApihubV1AttributeValues::class;
  protected $targetUserDataType = '';
  protected $teamType = GoogleCloudApihubV1AttributeValues::class;
  protected $teamDataType = '';
  /**
   * Output only. The time at which the API resource was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The list of versions present in an API resource. Note: An API
   * resource can be associated with more than 1 version. Format is
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @var string[]
   */
  public $versions;

  /**
   * Optional. The api functional requirements associated with the API resource.
   * Carinality is 1 for this attribute. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-api-functional-
   * requirements` attribute. The value of the attribute should be a proper URI,
   * and in case of Cloud Storage URI, it should point to a Cloud Storage
   * object, not a directory.
   *
   * @param GoogleCloudApihubV1AttributeValues $apiFunctionalRequirements
   */
  public function setApiFunctionalRequirements(GoogleCloudApihubV1AttributeValues $apiFunctionalRequirements)
  {
    $this->apiFunctionalRequirements = $apiFunctionalRequirements;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getApiFunctionalRequirements()
  {
    return $this->apiFunctionalRequirements;
  }
  /**
   * Optional. The api requirement doc associated with the API resource.
   * Carinality is 1 for this attribute. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-api-
   * requirements` attribute. The value of the attribute should be a proper URI,
   * and in case of Cloud Storage URI, it should point to a Cloud Storage
   * object, not a directory.
   *
   * @param GoogleCloudApihubV1AttributeValues $apiRequirements
   */
  public function setApiRequirements(GoogleCloudApihubV1AttributeValues $apiRequirements)
  {
    $this->apiRequirements = $apiRequirements;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getApiRequirements()
  {
    return $this->apiRequirements;
  }
  /**
   * Optional. The style of the API. This maps to the following system defined
   * attribute: `projects/{project}/locations/{location}/attributes/system-api-
   * style` attribute. The number of values for this attribute will be based on
   * the cardinality of the attribute. The same can be retrieved via
   * GetAttribute API. All values should be from the list of allowed values
   * defined for the attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $apiStyle
   */
  public function setApiStyle(GoogleCloudApihubV1AttributeValues $apiStyle)
  {
    $this->apiStyle = $apiStyle;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getApiStyle()
  {
    return $this->apiStyle;
  }
  /**
   * Optional. The api technical requirements associated with the API resource.
   * Carinality is 1 for this attribute. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-api-technical-
   * requirements` attribute. The value of the attribute should be a proper URI,
   * and in case of Cloud Storage URI, it should point to a Cloud Storage
   * object, not a directory.
   *
   * @param GoogleCloudApihubV1AttributeValues $apiTechnicalRequirements
   */
  public function setApiTechnicalRequirements(GoogleCloudApihubV1AttributeValues $apiTechnicalRequirements)
  {
    $this->apiTechnicalRequirements = $apiTechnicalRequirements;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getApiTechnicalRequirements()
  {
    return $this->apiTechnicalRequirements;
  }
  /**
   * Optional. The list of user defined attributes associated with the API
   * resource. The key is the attribute name. It will be of the format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`. The value
   * is the attribute values associated with the resource.
   *
   * @param GoogleCloudApihubV1AttributeValues[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. The business unit owning the API. This maps to the following
   * system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-business-unit`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $businessUnit
   */
  public function setBusinessUnit(GoogleCloudApihubV1AttributeValues $businessUnit)
  {
    $this->businessUnit = $businessUnit;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getBusinessUnit()
  {
    return $this->businessUnit;
  }
  /**
   * Output only. The time at which the API resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The description of the API resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the API resource.
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
   * Optional. The documentation for the API resource.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. Fingerprint of the API resource. This must be unique for each API
   * resource. It can neither be unset nor be updated to an existing fingerprint
   * of another API resource.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Optional. The maturity level of the API. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-maturity-level`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $maturityLevel
   */
  public function setMaturityLevel(GoogleCloudApihubV1AttributeValues $maturityLevel)
  {
    $this->maturityLevel = $maturityLevel;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getMaturityLevel()
  {
    return $this->maturityLevel;
  }
  /**
   * Identifier. The name of the API resource in the API Hub. Format:
   * `projects/{project}/locations/{location}/apis/{api}`
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
   * Optional. Owner details for the API resource.
   *
   * @param GoogleCloudApihubV1Owner $owner
   */
  public function setOwner(GoogleCloudApihubV1Owner $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return GoogleCloudApihubV1Owner
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * Optional. The selected version for an API resource. This can be used when
   * special handling is needed on client side for particular version of the
   * API. Format is
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @param string $selectedVersion
   */
  public function setSelectedVersion($selectedVersion)
  {
    $this->selectedVersion = $selectedVersion;
  }
  /**
   * @return string
   */
  public function getSelectedVersion()
  {
    return $this->selectedVersion;
  }
  /**
   * Output only. The list of sources and metadata from the sources of the API
   * resource.
   *
   * @param GoogleCloudApihubV1SourceMetadata[] $sourceMetadata
   */
  public function setSourceMetadata($sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return GoogleCloudApihubV1SourceMetadata[]
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * Optional. The target users for the API. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-target-user`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $targetUser
   */
  public function setTargetUser(GoogleCloudApihubV1AttributeValues $targetUser)
  {
    $this->targetUser = $targetUser;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getTargetUser()
  {
    return $this->targetUser;
  }
  /**
   * Optional. The team owning the API. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-team` attribute.
   * The number of values for this attribute will be based on the cardinality of
   * the attribute. The same can be retrieved via GetAttribute API. All values
   * should be from the list of allowed values defined for the attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $team
   */
  public function setTeam(GoogleCloudApihubV1AttributeValues $team)
  {
    $this->team = $team;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getTeam()
  {
    return $this->team;
  }
  /**
   * Output only. The time at which the API resource was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The list of versions present in an API resource. Note: An API
   * resource can be associated with more than 1 version. Format is
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @param string[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return string[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Api::class, 'Google_Service_APIhub_GoogleCloudApihubV1Api');
