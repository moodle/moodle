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

class TagBinding extends \Google\Model
{
  /**
   * Output only. The name of the TagBinding. This is a String of the form:
   * `tagBindings/{full-resource-name}/{tag-value-name}` (e.g. `tagBindings/%2F%
   * 2Fcloudresourcemanager.googleapis.com%2Fprojects%2F123/tagValues/456`).
   *
   * @var string
   */
  public $name;
  /**
   * The full resource name of the resource the TagValue is bound to. E.g.
   * `//cloudresourcemanager.googleapis.com/projects/123`
   *
   * @var string
   */
  public $parent;
  /**
   * The TagValue of the TagBinding. Must be of the form `tagValues/456`.
   *
   * @var string
   */
  public $tagValue;
  /**
   * The namespaced name for the TagValue of the TagBinding. Must be in the
   * format `{parent_id}/{tag_key_short_name}/{short_name}`. For methods that
   * support TagValue namespaced name, only one of tag_value_namespaced_name or
   * tag_value may be filled. Requests with both fields will be rejected.
   *
   * @var string
   */
  public $tagValueNamespacedName;

  /**
   * Output only. The name of the TagBinding. This is a String of the form:
   * `tagBindings/{full-resource-name}/{tag-value-name}` (e.g. `tagBindings/%2F%
   * 2Fcloudresourcemanager.googleapis.com%2Fprojects%2F123/tagValues/456`).
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
   * The full resource name of the resource the TagValue is bound to. E.g.
   * `//cloudresourcemanager.googleapis.com/projects/123`
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
   * The TagValue of the TagBinding. Must be of the form `tagValues/456`.
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
  /**
   * The namespaced name for the TagValue of the TagBinding. Must be in the
   * format `{parent_id}/{tag_key_short_name}/{short_name}`. For methods that
   * support TagValue namespaced name, only one of tag_value_namespaced_name or
   * tag_value may be filled. Requests with both fields will be rejected.
   *
   * @param string $tagValueNamespacedName
   */
  public function setTagValueNamespacedName($tagValueNamespacedName)
  {
    $this->tagValueNamespacedName = $tagValueNamespacedName;
  }
  /**
   * @return string
   */
  public function getTagValueNamespacedName()
  {
    return $this->tagValueNamespacedName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagBinding::class, 'Google_Service_CloudResourceManager_TagBinding');
