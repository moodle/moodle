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

namespace Google\Service\CloudResourceManager;

class TagKey extends \Google\Model
{
  /**
   * Unspecified purpose.
   */
  public const PURPOSE_PURPOSE_UNSPECIFIED = 'PURPOSE_UNSPECIFIED';
  /**
   * Purpose for Compute Engine firewalls. A corresponding `purpose_data` should
   * be set for the network the tag is intended for. The key should be `network`
   * and the value should be in ## either of these two formats: `https://www.goo
   * gleapis.com/compute/{compute_version}/projects/{project_id}/global/networks
   * /{network_id}` - `{project_id}/{network_name}` ## Examples:
   * `https://www.googleapis.com/compute/staging_v1/projects/fail-closed-load-
   * testing/global/networks/6992953698831725600` - `fail-closed-load-
   * testing/load-testing-network`
   */
  public const PURPOSE_GCE_FIREWALL = 'GCE_FIREWALL';
  /**
   * Purpose for data governance. Tag Values created under a key with this
   * purpose may have Tag Value children. No `purpose_data` should be set.
   */
  public const PURPOSE_DATA_GOVERNANCE = 'DATA_GOVERNANCE';
  /**
   * Optional. Regular expression constraint for freeform tag values. If
   * present, it implicitly allows freeform values (constrained by the regex).
   *
   * @var string
   */
  public $allowedValuesRegex;
  /**
   * Output only. Creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-assigned description of the TagKey. Must not exceed 256
   * characters. Read-write.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Entity tag which users can pass to prevent race conditions. This
   * field is always set in server responses. See UpdateTagKeyRequest for
   * details.
   *
   * @var string
   */
  public $etag;
  /**
   * Immutable. The resource name for a TagKey. Must be in the format
   * `tagKeys/{tag_key_id}`, where `tag_key_id` is the generated numeric id for
   * the TagKey.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. Namespaced name of the TagKey.
   *
   * @var string
   */
  public $namespacedName;
  /**
   * Immutable. The resource name of the TagKey's parent. A TagKey can be
   * parented by an Organization or a Project. For a TagKey parented by an
   * Organization, its parent must be in the form `organizations/{org_id}`. For
   * a TagKey parented by a Project, its parent can be in the form
   * `projects/{project_id}` or `projects/{project_number}`.
   *
   * @var string
   */
  public $parent;
  /**
   * Optional. A purpose denotes that this Tag is intended for use in policies
   * of a specific policy engine, and will involve that policy engine in
   * management operations involving this Tag. A purpose does not grant a policy
   * engine exclusive rights to the Tag, and it may be referenced by other
   * policy engines. A purpose cannot be changed once set.
   *
   * @var string
   */
  public $purpose;
  /**
   * Optional. Purpose data corresponds to the policy system that the tag is
   * intended for. See documentation for `Purpose` for formatting of this field.
   * Purpose data cannot be changed once set.
   *
   * @var string[]
   */
  public $purposeData;
  /**
   * Required. Immutable. The user friendly name for a TagKey. The short name
   * should be unique for TagKeys within the same tag namespace. The short name
   * must be 1-256 characters, beginning and ending with an alphanumeric
   * character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @var string
   */
  public $shortName;
  /**
   * Output only. Update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Regular expression constraint for freeform tag values. If
   * present, it implicitly allows freeform values (constrained by the regex).
   *
   * @param string $allowedValuesRegex
   */
  public function setAllowedValuesRegex($allowedValuesRegex)
  {
    $this->allowedValuesRegex = $allowedValuesRegex;
  }
  /**
   * @return string
   */
  public function getAllowedValuesRegex()
  {
    return $this->allowedValuesRegex;
  }
  /**
   * Output only. Creation time.
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
   * Optional. User-assigned description of the TagKey. Must not exceed 256
   * characters. Read-write.
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
   * Optional. Entity tag which users can pass to prevent race conditions. This
   * field is always set in server responses. See UpdateTagKeyRequest for
   * details.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Immutable. The resource name for a TagKey. Must be in the format
   * `tagKeys/{tag_key_id}`, where `tag_key_id` is the generated numeric id for
   * the TagKey.
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
   * Output only. Immutable. Namespaced name of the TagKey.
   *
   * @param string $namespacedName
   */
  public function setNamespacedName($namespacedName)
  {
    $this->namespacedName = $namespacedName;
  }
  /**
   * @return string
   */
  public function getNamespacedName()
  {
    return $this->namespacedName;
  }
  /**
   * Immutable. The resource name of the TagKey's parent. A TagKey can be
   * parented by an Organization or a Project. For a TagKey parented by an
   * Organization, its parent must be in the form `organizations/{org_id}`. For
   * a TagKey parented by a Project, its parent can be in the form
   * `projects/{project_id}` or `projects/{project_number}`.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Optional. A purpose denotes that this Tag is intended for use in policies
   * of a specific policy engine, and will involve that policy engine in
   * management operations involving this Tag. A purpose does not grant a policy
   * engine exclusive rights to the Tag, and it may be referenced by other
   * policy engines. A purpose cannot be changed once set.
   *
   * Accepted values: PURPOSE_UNSPECIFIED, GCE_FIREWALL, DATA_GOVERNANCE
   *
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * Optional. Purpose data corresponds to the policy system that the tag is
   * intended for. See documentation for `Purpose` for formatting of this field.
   * Purpose data cannot be changed once set.
   *
   * @param string[] $purposeData
   */
  public function setPurposeData($purposeData)
  {
    $this->purposeData = $purposeData;
  }
  /**
   * @return string[]
   */
  public function getPurposeData()
  {
    return $this->purposeData;
  }
  /**
   * Required. Immutable. The user friendly name for a TagKey. The short name
   * should be unique for TagKeys within the same tag namespace. The short name
   * must be 1-256 characters, beginning and ending with an alphanumeric
   * character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }
  /**
   * Output only. Update time.
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
class_alias(TagKey::class, 'Google_Service_CloudResourceManager_TagKey');
