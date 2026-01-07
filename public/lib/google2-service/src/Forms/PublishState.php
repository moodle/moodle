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

namespace Google\Service\Forms;

class PublishState extends \Google\Model
{
  /**
   * Required. Whether the form accepts responses. If `is_published` is set to
   * `false`, this field is forced to `false`.
   *
   * @var bool
   */
  public $isAcceptingResponses;
  /**
   * Required. Whether the form is published and visible to others.
   *
   * @var bool
   */
  public $isPublished;

  /**
   * Required. Whether the form accepts responses. If `is_published` is set to
   * `false`, this field is forced to `false`.
   *
   * @param bool $isAcceptingResponses
   */
  public function setIsAcceptingResponses($isAcceptingResponses)
  {
    $this->isAcceptingResponses = $isAcceptingResponses;
  }
  /**
   * @return bool
   */
  public function getIsAcceptingResponses()
  {
    return $this->isAcceptingResponses;
  }
  /**
   * Required. Whether the form is published and visible to others.
   *
   * @param bool $isPublished
   */
  public function setIsPublished($isPublished)
  {
    $this->isPublished = $isPublished;
  }
  /**
   * @return bool
   */
  public function getIsPublished()
  {
    return $this->isPublished;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishState::class, 'Google_Service_Forms_PublishState');
