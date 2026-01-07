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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1Warning extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  /**
   * e.g. deprecation warnings and alternative feature highlights.
   */
  public const PRIORITY_INFO = 'INFO';
  /**
   * e.g. automated detection of possible issues with the build.
   */
  public const PRIORITY_WARNING = 'WARNING';
  /**
   * e.g. alerts that a feature used in the build is pending removal
   */
  public const PRIORITY_ALERT = 'ALERT';
  /**
   * The priority for this warning.
   *
   * @var string
   */
  public $priority;
  /**
   * Explanation of the warning generated.
   *
   * @var string
   */
  public $text;

  /**
   * The priority for this warning.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, INFO, WARNING, ALERT
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Explanation of the warning generated.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1Warning::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Warning');
