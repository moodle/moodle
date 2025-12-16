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

namespace Google\Service\Container;

class ResourceManagerTags extends \Google\Model
{
  /**
   * TagKeyValue must be in one of the following formats ([KEY]=[VALUE]) 1.
   * `tagKeys/{tag_key_id}=tagValues/{tag_value_id}` 2.
   * `{org_id}/{tag_key_name}={tag_value_name}` 3.
   * `{project_id}/{tag_key_name}={tag_value_name}`
   *
   * @var string[]
   */
  public $tags;

  /**
   * TagKeyValue must be in one of the following formats ([KEY]=[VALUE]) 1.
   * `tagKeys/{tag_key_id}=tagValues/{tag_value_id}` 2.
   * `{org_id}/{tag_key_name}={tag_value_name}` 3.
   * `{project_id}/{tag_key_name}={tag_value_name}`
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceManagerTags::class, 'Google_Service_Container_ResourceManagerTags');
