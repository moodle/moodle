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

class EffectiveTag extends \Google\Model
{
  /**
   * Indicates the inheritance status of a tag value attached to the given
   * resource. If the tag value is inherited from one of the resource's
   * ancestors, inherited will be true. If false, then the tag value is directly
   * attached to the resource, inherited will be false.
   *
   * @var bool
   */
  public $inherited;
  /**
   * The namespaced name of the TagKey. Can be in the form
   * `{organization_id}/{tag_key_short_name}` or
   * `{project_id}/{tag_key_short_name}` or
   * `{project_number}/{tag_key_short_name}`.
   *
   * @var string
   */
  public $namespacedTagKey;
  /**
   * The namespaced name of the TagValue. Can be in the form
   * `{organization_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_number}/{tag_key_short_name}/{tag_value_short_name}`.
   *
   * @var string
   */
  public $namespacedTagValue;
  /**
   * The name of the TagKey, in the format `tagKeys/{id}`, such as
   * `tagKeys/123`.
   *
   * @var string
   */
  public $tagKey;
  /**
   * The parent name of the tag key. Must be in the format
   * `organizations/{organization_id}` or `projects/{project_number}`
   *
   * @var string
   */
  public $tagKeyParentName;
  /**
   * Resource name for TagValue in the format `tagValues/456`.
   *
   * @var string
   */
  public $tagValue;

  /**
   * Indicates the inheritance status of a tag value attached to the given
   * resource. If the tag value is inherited from one of the resource's
   * ancestors, inherited will be true. If false, then the tag value is directly
   * attached to the resource, inherited will be false.
   *
   * @param bool $inherited
   */
  public function setInherited($inherited)
  {
    $this->inherited = $inherited;
  }
  /**
   * @return bool
   */
  public function getInherited()
  {
    return $this->inherited;
  }
  /**
   * The namespaced name of the TagKey. Can be in the form
   * `{organization_id}/{tag_key_short_name}` or
   * `{project_id}/{tag_key_short_name}` or
   * `{project_number}/{tag_key_short_name}`.
   *
   * @param string $namespacedTagKey
   */
  public function setNamespacedTagKey($namespacedTagKey)
  {
    $this->namespacedTagKey = $namespacedTagKey;
  }
  /**
   * @return string
   */
  public function getNamespacedTagKey()
  {
    return $this->namespacedTagKey;
  }
  /**
   * The namespaced name of the TagValue. Can be in the form
   * `{organization_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_id}/{tag_key_short_name}/{tag_value_short_name}` or
   * `{project_number}/{tag_key_short_name}/{tag_value_short_name}`.
   *
   * @param string $namespacedTagValue
   */
  public function setNamespacedTagValue($namespacedTagValue)
  {
    $this->namespacedTagValue = $namespacedTagValue;
  }
  /**
   * @return string
   */
  public function getNamespacedTagValue()
  {
    return $this->namespacedTagValue;
  }
  /**
   * The name of the TagKey, in the format `tagKeys/{id}`, such as
   * `tagKeys/123`.
   *
   * @param string $tagKey
   */
  public function setTagKey($tagKey)
  {
    $this->tagKey = $tagKey;
  }
  /**
   * @return string
   */
  public function getTagKey()
  {
    return $this->tagKey;
  }
  /**
   * The parent name of the tag key. Must be in the format
   * `organizations/{organization_id}` or `projects/{project_number}`
   *
   * @param string $tagKeyParentName
   */
  public function setTagKeyParentName($tagKeyParentName)
  {
    $this->tagKeyParentName = $tagKeyParentName;
  }
  /**
   * @return string
   */
  public function getTagKeyParentName()
  {
    return $this->tagKeyParentName;
  }
  /**
   * Resource name for TagValue in the format `tagValues/456`.
   *
   * @param string $tagValue
   */
  public function setTagValue($tagValue)
  {
    $this->tagValue = $tagValue;
  }
  /**
   * @return string
   */
  public function getTagValue()
  {
    return $this->tagValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EffectiveTag::class, 'Google_Service_CloudResourceManager_EffectiveTag');
