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

class NextStateOfTags extends \Google\Model
{
  protected $tagsFullStateType = TagsFullState::class;
  protected $tagsFullStateDataType = '';
  protected $tagsFullStateForChildResourceType = TagsFullStateForChildResource::class;
  protected $tagsFullStateForChildResourceDataType = '';
  protected $tagsPartialStateType = TagsPartialState::class;
  protected $tagsPartialStateDataType = '';

  /**
   * @param TagsFullState $tagsFullState
   */
  public function setTagsFullState(TagsFullState $tagsFullState)
  {
    $this->tagsFullState = $tagsFullState;
  }
  /**
   * @return TagsFullState
   */
  public function getTagsFullState()
  {
    return $this->tagsFullState;
  }
  /**
   * @param TagsFullStateForChildResource $tagsFullStateForChildResource
   */
  public function setTagsFullStateForChildResource(TagsFullStateForChildResource $tagsFullStateForChildResource)
  {
    $this->tagsFullStateForChildResource = $tagsFullStateForChildResource;
  }
  /**
   * @return TagsFullStateForChildResource
   */
  public function getTagsFullStateForChildResource()
  {
    return $this->tagsFullStateForChildResource;
  }
  /**
   * @param TagsPartialState $tagsPartialState
   */
  public function setTagsPartialState(TagsPartialState $tagsPartialState)
  {
    $this->tagsPartialState = $tagsPartialState;
  }
  /**
   * @return TagsPartialState
   */
  public function getTagsPartialState()
  {
    return $this->tagsPartialState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NextStateOfTags::class, 'Google_Service_CloudIAP_NextStateOfTags');
