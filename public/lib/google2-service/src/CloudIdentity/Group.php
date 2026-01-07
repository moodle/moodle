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

namespace Google\Service\CloudIdentity;

class Group extends \Google\Collection
{
  protected $collection_key = 'additionalGroupKeys';
  protected $additionalGroupKeysType = EntityKey::class;
  protected $additionalGroupKeysDataType = 'array';
  /**
   * Output only. The time when the `Group` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * An extended description to help users determine the purpose of a `Group`.
   * Must not be longer than 4,096 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The display name of the `Group`.
   *
   * @var string
   */
  public $displayName;
  protected $dynamicGroupMetadataType = DynamicGroupMetadata::class;
  protected $dynamicGroupMetadataDataType = '';
  protected $groupKeyType = EntityKey::class;
  protected $groupKeyDataType = '';
  /**
   * Required. One or more label entries that apply to the Group. Labels contain
   * a key with an empty value. Google Groups are the default type of group and
   * have a label with a key of
   * `cloudidentity.googleapis.com/groups.discussion_forum` and an empty value.
   * Existing Google Groups can have an additional label with a key of
   * `cloudidentity.googleapis.com/groups.security` and an empty value added to
   * them. **This is an immutable change and the security label cannot be
   * removed once added.** Dynamic groups have a label with a key of
   * `cloudidentity.googleapis.com/groups.dynamic`. Identity-mapped groups for
   * Cloud Search have a label with a key of `system/groups/external` and an
   * empty value. Google Groups can be
   * [locked](https://support.google.com/a?p=locked-groups). To lock a group,
   * add a label with a key of `cloudidentity.googleapis.com/groups.locked` and
   * an empty value. Doing so locks the group. To unlock the group, remove this
   * label.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the `Group`.
   * Shall be of the form `groups/{group}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The resource name of the entity under which this
   * `Group` resides in the Cloud Identity resource hierarchy. Must be of the
   * form `identitysources/{identity_source}` for external [identity-mapped
   * groups](https://support.google.com/a/answer/9039510) or
   * `customers/{customer_id}` for Google Groups. The `customer_id` must begin
   * with "C" (for example, 'C046psxkn'). [Find your customer ID.]
   * (https://support.google.com/cloudidentity/answer/10070793)
   *
   * @var string
   */
  public $parent;
  /**
   * Output only. The time when the `Group` was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Additional group keys associated with the Group.
   *
   * @param EntityKey[] $additionalGroupKeys
   */
  public function setAdditionalGroupKeys($additionalGroupKeys)
  {
    $this->additionalGroupKeys = $additionalGroupKeys;
  }
  /**
   * @return EntityKey[]
   */
  public function getAdditionalGroupKeys()
  {
    return $this->additionalGroupKeys;
  }
  /**
   * Output only. The time when the `Group` was created.
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
   * An extended description to help users determine the purpose of a `Group`.
   * Must not be longer than 4,096 characters.
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
   * The display name of the `Group`.
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
   * Optional. Dynamic group metadata like queries and status.
   *
   * @param DynamicGroupMetadata $dynamicGroupMetadata
   */
  public function setDynamicGroupMetadata(DynamicGroupMetadata $dynamicGroupMetadata)
  {
    $this->dynamicGroupMetadata = $dynamicGroupMetadata;
  }
  /**
   * @return DynamicGroupMetadata
   */
  public function getDynamicGroupMetadata()
  {
    return $this->dynamicGroupMetadata;
  }
  /**
   * Required. The `EntityKey` of the `Group`.
   *
   * @param EntityKey $groupKey
   */
  public function setGroupKey(EntityKey $groupKey)
  {
    $this->groupKey = $groupKey;
  }
  /**
   * @return EntityKey
   */
  public function getGroupKey()
  {
    return $this->groupKey;
  }
  /**
   * Required. One or more label entries that apply to the Group. Labels contain
   * a key with an empty value. Google Groups are the default type of group and
   * have a label with a key of
   * `cloudidentity.googleapis.com/groups.discussion_forum` and an empty value.
   * Existing Google Groups can have an additional label with a key of
   * `cloudidentity.googleapis.com/groups.security` and an empty value added to
   * them. **This is an immutable change and the security label cannot be
   * removed once added.** Dynamic groups have a label with a key of
   * `cloudidentity.googleapis.com/groups.dynamic`. Identity-mapped groups for
   * Cloud Search have a label with a key of `system/groups/external` and an
   * empty value. Google Groups can be
   * [locked](https://support.google.com/a?p=locked-groups). To lock a group,
   * add a label with a key of `cloudidentity.googleapis.com/groups.locked` and
   * an empty value. Doing so locks the group. To unlock the group, remove this
   * label.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the `Group`.
   * Shall be of the form `groups/{group}`.
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
   * Required. Immutable. The resource name of the entity under which this
   * `Group` resides in the Cloud Identity resource hierarchy. Must be of the
   * form `identitysources/{identity_source}` for external [identity-mapped
   * groups](https://support.google.com/a/answer/9039510) or
   * `customers/{customer_id}` for Google Groups. The `customer_id` must begin
   * with "C" (for example, 'C046psxkn'). [Find your customer ID.]
   * (https://support.google.com/cloudidentity/answer/10070793)
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
   * Output only. The time when the `Group` was last updated.
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
class_alias(Group::class, 'Google_Service_CloudIdentity_Group');
