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

class TagValue extends \Google\Model
{
  /**
   * Output only. Creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-assigned description of the TagValue. Must not exceed 256
   * characters. Read-write.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Entity tag which users can pass to prevent race conditions. This
   * field is always set in server responses. See UpdateTagValueRequest for
   * details.
   *
   * @var string
   */
  public $etag;
  /**
   * Immutable. Resource name for TagValue in the format `tagValues/456`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The namespaced name of the TagValue. Can be in the form
   * `{organization_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_number}/{tag_key_short_name}/{tag_value_short_name}`.
   *
   * @var string
   */
  public $namespacedName;
  /**
   * Immutable. The resource name of the new TagValue's parent TagKey. Must be
   * of the form `tagKeys/{tag_key_id}`.
   *
   * @var string
   */
  public $parent;
  /**
   * Required. Immutable. User-assigned short name for TagValue. The short name
   * should be unique for TagValues within the same parent TagKey. The short
   * name must be 256 characters or less, beginning and ending with an
   * alphanumeric character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots
   * (.), and alphanumerics between.
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
   * Optional. User-assigned description of the TagValue. Must not exceed 256
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
   * field is always set in server responses. See UpdateTagValueRequest for
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
   * Immutable. Resource name for TagValue in the format `tagValues/456`.
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
   * Output only. The namespaced name of the TagValue. Can be in the form
   * `{organization_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_number}/{tag_key_short_name}/{tag_value_short_name}`.
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
   * Immutable. The resource name of the new TagValue's parent TagKey. Must be
   * of the form `tagKeys/{tag_key_id}`.
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
   * Required. Immutable. User-assigned short name for TagValue. The short name
   * should be unique for TagValues within the same parent TagKey. The short
   * name must be 256 characters or less, beginning and ending with an
   * alphanumeric character ([a-z0-9A-Z]) with dashes (-), underscores (_), dots
   * (.), and alphanumerics between.
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
class_alias(TagValue::class, 'Google_Service_CloudResourceManager_TagValue');
