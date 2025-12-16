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

namespace Google\Service\ToolResults;

class History extends \Google\Model
{
  public const TEST_PLATFORM_unknownPlatform = 'unknownPlatform';
  public const TEST_PLATFORM_android = 'android';
  public const TEST_PLATFORM_ios = 'ios';
  /**
   * A short human-readable (plain text) name to display in the UI. Maximum of
   * 100 characters. - In response: present if set during create. - In create
   * request: optional
   *
   * @var string
   */
  public $displayName;
  /**
   * A unique identifier within a project for this History. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response always set - In create request: never set
   *
   * @var string
   */
  public $historyId;
  /**
   * A name to uniquely identify a history within a project. Maximum of 200
   * characters. - In response always set - In create request: always set
   *
   * @var string
   */
  public $name;
  /**
   * The platform of the test history. - In response: always set. Returns the
   * platform of the last execution if unknown.
   *
   * @var string
   */
  public $testPlatform;

  /**
   * A short human-readable (plain text) name to display in the UI. Maximum of
   * 100 characters. - In response: present if set during create. - In create
   * request: optional
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * A unique identifier within a project for this History. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response always set - In create request: never set
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * A name to uniquely identify a history within a project. Maximum of 200
   * characters. - In response always set - In create request: always set
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
   * The platform of the test history. - In response: always set. Returns the
   * platform of the last execution if unknown.
   *
   * Accepted values: unknownPlatform, android, ios
   *
   * @param self::TEST_PLATFORM_* $testPlatform
   */
  public function setTestPlatform($testPlatform)
  {
    $this->testPlatform = $testPlatform;
  }
  /**
   * @return self::TEST_PLATFORM_*
   */
  public function getTestPlatform()
  {
    return $this->testPlatform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(History::class, 'Google_Service_ToolResults_History');
