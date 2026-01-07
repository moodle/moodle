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

class GoogleCloudApihubV1Version extends \Google\Collection
{
  protected $collection_key = 'specs';
  protected $accreditationType = GoogleCloudApihubV1AttributeValues::class;
  protected $accreditationDataType = '';
  /**
   * Output only. The operations contained in the API version. These operations
   * will be added to the version when a new spec is added or when an existing
   * spec is updated. Format is `projects/{project}/locations/{location}/apis/{a
   * pi}/versions/{version}/operations/{operation}`
   *
   * @var string[]
   */
  public $apiOperations;
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  protected $complianceType = GoogleCloudApihubV1AttributeValues::class;
  protected $complianceDataType = '';
  /**
   * Output only. The time at which the version was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The definitions contained in the API version. These
   * definitions will be added to the version when a new spec is added or when
   * an existing spec is updated. Format is `projects/{project}/locations/{locat
   * ion}/apis/{api}/versions/{version}/definitions/{definition}`
   *
   * @var string[]
   */
  public $definitions;
  /**
   * Optional. The deployments linked to this API version. Note: A particular
   * API version could be deployed to multiple deployments (for dev deployment,
   * UAT deployment, etc) Format is
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @var string[]
   */
  public $deployments;
  /**
   * Optional. The description of the version.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the version.
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  protected $lifecycleType = GoogleCloudApihubV1AttributeValues::class;
  protected $lifecycleDataType = '';
  /**
   * Identifier. The name of the version. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The selected deployment for a Version resource. This can be used
   * when special handling is needed on client side for a particular deployment
   * linked to the version. Format is
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @var string
   */
  public $selectedDeployment;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = 'array';
  /**
   * Output only. The specs associated with this version. Note that an API
   * version can be associated with multiple specs. Format is `projects/{project
   * }/locations/{location}/apis/{api}/versions/{version}/specs/{spec}`
   *
   * @var string[]
   */
  public $specs;
  /**
   * Output only. The time at which the version was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The accreditations associated with the API version. This maps to
   * the following system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-accreditation`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $accreditation
   */
  public function setAccreditation(GoogleCloudApihubV1AttributeValues $accreditation)
  {
    $this->accreditation = $accreditation;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getAccreditation()
  {
    return $this->accreditation;
  }
  /**
   * Output only. The operations contained in the API version. These operations
   * will be added to the version when a new spec is added or when an existing
   * spec is updated. Format is `projects/{project}/locations/{location}/apis/{a
   * pi}/versions/{version}/operations/{operation}`
   *
   * @param string[] $apiOperations
   */
  public function setApiOperations($apiOperations)
  {
    $this->apiOperations = $apiOperations;
  }
  /**
   * @return string[]
   */
  public function getApiOperations()
  {
    return $this->apiOperations;
  }
  /**
   * Optional. The list of user defined attributes associated with the Version
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
   * Optional. The compliance associated with the API version. This maps to the
   * following system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-compliance`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $compliance
   */
  public function setCompliance(GoogleCloudApihubV1AttributeValues $compliance)
  {
    $this->compliance = $compliance;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getCompliance()
  {
    return $this->compliance;
  }
  /**
   * Output only. The time at which the version was created.
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
   * Output only. The definitions contained in the API version. These
   * definitions will be added to the version when a new spec is added or when
   * an existing spec is updated. Format is `projects/{project}/locations/{locat
   * ion}/apis/{api}/versions/{version}/definitions/{definition}`
   *
   * @param string[] $definitions
   */
  public function setDefinitions($definitions)
  {
    $this->definitions = $definitions;
  }
  /**
   * @return string[]
   */
  public function getDefinitions()
  {
    return $this->definitions;
  }
  /**
   * Optional. The deployments linked to this API version. Note: A particular
   * API version could be deployed to multiple deployments (for dev deployment,
   * UAT deployment, etc) Format is
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @param string[] $deployments
   */
  public function setDeployments($deployments)
  {
    $this->deployments = $deployments;
  }
  /**
   * @return string[]
   */
  public function getDeployments()
  {
    return $this->deployments;
  }
  /**
   * Optional. The description of the version.
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
   * Required. The display name of the version.
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
   * Optional. The documentation of the version.
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
   * Optional. The lifecycle of the API version. This maps to the following
   * system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-lifecycle`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $lifecycle
   */
  public function setLifecycle(GoogleCloudApihubV1AttributeValues $lifecycle)
  {
    $this->lifecycle = $lifecycle;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getLifecycle()
  {
    return $this->lifecycle;
  }
  /**
   * Identifier. The name of the version. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
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
   * Optional. The selected deployment for a Version resource. This can be used
   * when special handling is needed on client side for a particular deployment
   * linked to the version. Format is
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @param string $selectedDeployment
   */
  public function setSelectedDeployment($selectedDeployment)
  {
    $this->selectedDeployment = $selectedDeployment;
  }
  /**
   * @return string
   */
  public function getSelectedDeployment()
  {
    return $this->selectedDeployment;
  }
  /**
   * Output only. The list of sources and metadata from the sources of the
   * version.
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
   * Output only. The specs associated with this version. Note that an API
   * version can be associated with multiple specs. Format is `projects/{project
   * }/locations/{location}/apis/{api}/versions/{version}/specs/{spec}`
   *
   * @param string[] $specs
   */
  public function setSpecs($specs)
  {
    $this->specs = $specs;
  }
  /**
   * @return string[]
   */
  public function getSpecs()
  {
    return $this->specs;
  }
  /**
   * Output only. The time at which the version was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Version::class, 'Google_Service_APIhub_GoogleCloudApihubV1Version');
