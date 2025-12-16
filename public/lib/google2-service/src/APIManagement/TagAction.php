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

namespace Google\Service\APIManagement;

class TagAction extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Addition of a Tag.
   */
  public const ACTION_ADD = 'ADD';
  /**
   * Removal of a Tag.
   */
  public const ACTION_REMOVE = 'REMOVE';
  /**
   * Required. Action to be applied
   *
   * @var string
   */
  public $action;
  /**
   * Required. Tag to be added or removed
   *
   * @var string
   */
  public $tag;

  /**
   * Required. Action to be applied
   *
   * Accepted values: ACTION_UNSPECIFIED, ADD, REMOVE
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Required. Tag to be added or removed
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagAction::class, 'Google_Service_APIManagement_TagAction');
