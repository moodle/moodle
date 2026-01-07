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

namespace Google\Service\Compute;

class DiskParams extends \Google\Model
{
  /**
   * Resource manager tags to be bound to the disk. Tag keys and values have the
   * same definition as resource manager tags. Keys and values can be either in
   * numeric format, such as `tagKeys/{tag_key_id}` and `tagValues/456` or in
   * namespaced format such as `{org_id|project_id}/{tag_key_short_name}` and
   * `{tag_value_short_name}`. The field is ignored (both PUT & PATCH) when
   * empty.
   *
   * @var string[]
   */
  public $resourceManagerTags;

  /**
   * Resource manager tags to be bound to the disk. Tag keys and values have the
   * same definition as resource manager tags. Keys and values can be either in
   * numeric format, such as `tagKeys/{tag_key_id}` and `tagValues/456` or in
   * namespaced format such as `{org_id|project_id}/{tag_key_short_name}` and
   * `{tag_value_short_name}`. The field is ignored (both PUT & PATCH) when
   * empty.
   *
   * @param string[] $resourceManagerTags
   */
  public function setResourceManagerTags($resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return string[]
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskParams::class, 'Google_Service_Compute_DiskParams');
