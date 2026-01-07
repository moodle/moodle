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

namespace Google\Service\CloudIAP;

class TagsFullStateForChildResource extends \Google\Model
{
  /**
   * If TagsFullStateForChildResource is initialized, the values in this field
   * represent all the tags in the next state for the child resource. Only one
   * type of tags reference (numeric or namespace) is required to be passed.
   * IMPORTANT: This field should only be used when the target resource IAM
   * policy name is UNKNOWN and the resource's parent IAM policy name is being
   * passed in the request.
   *
   * @var string[]
   */
  public $tags;

  /**
   * If TagsFullStateForChildResource is initialized, the values in this field
   * represent all the tags in the next state for the child resource. Only one
   * type of tags reference (numeric or namespace) is required to be passed.
   * IMPORTANT: This field should only be used when the target resource IAM
   * policy name is UNKNOWN and the resource's parent IAM policy name is being
   * passed in the request.
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
class_alias(TagsFullStateForChildResource::class, 'Google_Service_CloudIAP_TagsFullStateForChildResource');
