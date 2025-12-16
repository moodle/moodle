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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicySchema extends \Google\Collection
{
  protected $collection_key = 'validTargetResources';
  /**
   * Output only. Specific access restrictions related to this policy.
   *
   * @var string[]
   */
  public $accessRestrictions;
  protected $additionalTargetKeyNamesType = GoogleChromePolicyVersionsV1AdditionalTargetKeyName::class;
  protected $additionalTargetKeyNamesDataType = 'array';
  /**
   * Title of the category in which a setting belongs.
   *
   * @var string
   */
  public $categoryTitle;
  protected $definitionType = Proto2FileDescriptorProto::class;
  protected $definitionDataType = '';
  protected $fieldDescriptionsType = GoogleChromePolicyVersionsV1PolicySchemaFieldDescription::class;
  protected $fieldDescriptionsDataType = 'array';
  /**
   * Format: name=customers/{customer}/policySchemas/{schema_namespace}
   *
   * @var string
   */
  public $name;
  protected $noticesType = GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription::class;
  protected $noticesDataType = 'array';
  protected $policyApiLifecycleType = GoogleChromePolicyVersionsV1PolicyApiLifecycle::class;
  protected $policyApiLifecycleDataType = '';
  /**
   * Output only. Description about the policy schema for user consumption.
   *
   * @var string
   */
  public $policyDescription;
  /**
   * Output only. The fully qualified name of the policy schema. This value is
   * used to fill the field `policy_schema` in PolicyValue when calling
   * BatchInheritOrgUnitPolicies BatchModifyOrgUnitPolicies
   * BatchModifyGroupPolicies or BatchDeleteGroupPolicies.
   *
   * @var string
   */
  public $schemaName;
  /**
   * Output only. URI to related support article for this schema.
   *
   * @var string
   */
  public $supportUri;
  /**
   * Output only. List indicates that the policy will only apply to
   * devices/users on these platforms.
   *
   * @var string[]
   */
  public $supportedPlatforms;
  /**
   * Output only. Information about applicable target resources for the policy.
   *
   * @var string[]
   */
  public $validTargetResources;

  /**
   * Output only. Specific access restrictions related to this policy.
   *
   * @param string[] $accessRestrictions
   */
  public function setAccessRestrictions($accessRestrictions)
  {
    $this->accessRestrictions = $accessRestrictions;
  }
  /**
   * @return string[]
   */
  public function getAccessRestrictions()
  {
    return $this->accessRestrictions;
  }
  /**
   * Output only. Additional key names that will be used to identify the target
   * of the policy value. When specifying a `policyTargetKey`, each of the
   * additional keys specified here will have to be included in the
   * `additionalTargetKeys` map.
   *
   * @param GoogleChromePolicyVersionsV1AdditionalTargetKeyName[] $additionalTargetKeyNames
   */
  public function setAdditionalTargetKeyNames($additionalTargetKeyNames)
  {
    $this->additionalTargetKeyNames = $additionalTargetKeyNames;
  }
  /**
   * @return GoogleChromePolicyVersionsV1AdditionalTargetKeyName[]
   */
  public function getAdditionalTargetKeyNames()
  {
    return $this->additionalTargetKeyNames;
  }
  /**
   * Title of the category in which a setting belongs.
   *
   * @param string $categoryTitle
   */
  public function setCategoryTitle($categoryTitle)
  {
    $this->categoryTitle = $categoryTitle;
  }
  /**
   * @return string
   */
  public function getCategoryTitle()
  {
    return $this->categoryTitle;
  }
  /**
   * Schema definition using proto descriptor.
   *
   * @param Proto2FileDescriptorProto $definition
   */
  public function setDefinition(Proto2FileDescriptorProto $definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return Proto2FileDescriptorProto
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * Output only. Detailed description of each field that is part of the schema.
   * Fields are suggested to be displayed by the ordering in this list, not by
   * field number.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaFieldDescription[] $fieldDescriptions
   */
  public function setFieldDescriptions($fieldDescriptions)
  {
    $this->fieldDescriptions = $fieldDescriptions;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaFieldDescription[]
   */
  public function getFieldDescriptions()
  {
    return $this->fieldDescriptions;
  }
  /**
   * Format: name=customers/{customer}/policySchemas/{schema_namespace}
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
   * Output only. Special notice messages related to setting certain values in
   * certain fields in the schema.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription[] $notices
   */
  public function setNotices($notices)
  {
    $this->notices = $notices;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription[]
   */
  public function getNotices()
  {
    return $this->notices;
  }
  /**
   * Output only. Current lifecycle information.
   *
   * @param GoogleChromePolicyVersionsV1PolicyApiLifecycle $policyApiLifecycle
   */
  public function setPolicyApiLifecycle(GoogleChromePolicyVersionsV1PolicyApiLifecycle $policyApiLifecycle)
  {
    $this->policyApiLifecycle = $policyApiLifecycle;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyApiLifecycle
   */
  public function getPolicyApiLifecycle()
  {
    return $this->policyApiLifecycle;
  }
  /**
   * Output only. Description about the policy schema for user consumption.
   *
   * @param string $policyDescription
   */
  public function setPolicyDescription($policyDescription)
  {
    $this->policyDescription = $policyDescription;
  }
  /**
   * @return string
   */
  public function getPolicyDescription()
  {
    return $this->policyDescription;
  }
  /**
   * Output only. The fully qualified name of the policy schema. This value is
   * used to fill the field `policy_schema` in PolicyValue when calling
   * BatchInheritOrgUnitPolicies BatchModifyOrgUnitPolicies
   * BatchModifyGroupPolicies or BatchDeleteGroupPolicies.
   *
   * @param string $schemaName
   */
  public function setSchemaName($schemaName)
  {
    $this->schemaName = $schemaName;
  }
  /**
   * @return string
   */
  public function getSchemaName()
  {
    return $this->schemaName;
  }
  /**
   * Output only. URI to related support article for this schema.
   *
   * @param string $supportUri
   */
  public function setSupportUri($supportUri)
  {
    $this->supportUri = $supportUri;
  }
  /**
   * @return string
   */
  public function getSupportUri()
  {
    return $this->supportUri;
  }
  /**
   * Output only. List indicates that the policy will only apply to
   * devices/users on these platforms.
   *
   * @param string[] $supportedPlatforms
   */
  public function setSupportedPlatforms($supportedPlatforms)
  {
    $this->supportedPlatforms = $supportedPlatforms;
  }
  /**
   * @return string[]
   */
  public function getSupportedPlatforms()
  {
    return $this->supportedPlatforms;
  }
  /**
   * Output only. Information about applicable target resources for the policy.
   *
   * @param string[] $validTargetResources
   */
  public function setValidTargetResources($validTargetResources)
  {
    $this->validTargetResources = $validTargetResources;
  }
  /**
   * @return string[]
   */
  public function getValidTargetResources()
  {
    return $this->validTargetResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicySchema::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicySchema');
