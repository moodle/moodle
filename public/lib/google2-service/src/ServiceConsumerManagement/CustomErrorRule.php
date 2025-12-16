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

namespace Google\Service\ServiceConsumerManagement;

class CustomErrorRule extends \Google\Model
{
  /**
   * Mark this message as possible payload in error response. Otherwise, objects
   * of this type will be filtered when they appear in error payload.
   *
   * @var bool
   */
  public $isErrorType;
  /**
   * Selects messages to which this rule applies. Refer to selector for syntax
   * details.
   *
   * @var string
   */
  public $selector;

  /**
   * Mark this message as possible payload in error response. Otherwise, objects
   * of this type will be filtered when they appear in error payload.
   *
   * @param bool $isErrorType
   */
  public function setIsErrorType($isErrorType)
  {
    $this->isErrorType = $isErrorType;
  }
  /**
   * @return bool
   */
  public function getIsErrorType()
  {
    return $this->isErrorType;
  }
  /**
   * Selects messages to which this rule applies. Refer to selector for syntax
   * details.
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomErrorRule::class, 'Google_Service_ServiceConsumerManagement_CustomErrorRule');
