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

namespace Google\Service\FirebaseRules;

class GetReleaseExecutableResponse extends \Google\Model
{
  /**
   * Executable format unspecified. Defaults to FIREBASE_RULES_EXECUTABLE_V1
   */
  public const EXECUTABLE_VERSION_RELEASE_EXECUTABLE_VERSION_UNSPECIFIED = 'RELEASE_EXECUTABLE_VERSION_UNSPECIFIED';
  /**
   * Firebase Rules syntax 'rules2' executable versions: Custom AST for use with
   * Java clients.
   */
  public const EXECUTABLE_VERSION_FIREBASE_RULES_EXECUTABLE_V1 = 'FIREBASE_RULES_EXECUTABLE_V1';
  /**
   * CEL-based executable for use with C++ clients.
   */
  public const EXECUTABLE_VERSION_FIREBASE_RULES_EXECUTABLE_V2 = 'FIREBASE_RULES_EXECUTABLE_V2';
  /**
   * Language unspecified. Defaults to FIREBASE_RULES.
   */
  public const LANGUAGE_LANGUAGE_UNSPECIFIED = 'LANGUAGE_UNSPECIFIED';
  /**
   * Firebase Rules language.
   */
  public const LANGUAGE_FIREBASE_RULES = 'FIREBASE_RULES';
  /**
   * Event Flow triggers.
   */
  public const LANGUAGE_EVENT_FLOW_TRIGGERS = 'EVENT_FLOW_TRIGGERS';
  /**
   * Executable view of the `Ruleset` referenced by the `Release`.
   *
   * @var string
   */
  public $executable;
  /**
   * The Rules runtime version of the executable.
   *
   * @var string
   */
  public $executableVersion;
  /**
   * `Language` used to generate the executable bytes.
   *
   * @var string
   */
  public $language;
  /**
   * `Ruleset` name associated with the `Release` executable.
   *
   * @var string
   */
  public $rulesetName;
  /**
   * Optional, indicates the freshness of the result. The response is guaranteed
   * to be the latest within an interval up to the sync_time (inclusive).
   *
   * @var string
   */
  public $syncTime;
  /**
   * Timestamp for the most recent `Release.update_time`.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Executable view of the `Ruleset` referenced by the `Release`.
   *
   * @param string $executable
   */
  public function setExecutable($executable)
  {
    $this->executable = $executable;
  }
  /**
   * @return string
   */
  public function getExecutable()
  {
    return $this->executable;
  }
  /**
   * The Rules runtime version of the executable.
   *
   * Accepted values: RELEASE_EXECUTABLE_VERSION_UNSPECIFIED,
   * FIREBASE_RULES_EXECUTABLE_V1, FIREBASE_RULES_EXECUTABLE_V2
   *
   * @param self::EXECUTABLE_VERSION_* $executableVersion
   */
  public function setExecutableVersion($executableVersion)
  {
    $this->executableVersion = $executableVersion;
  }
  /**
   * @return self::EXECUTABLE_VERSION_*
   */
  public function getExecutableVersion()
  {
    return $this->executableVersion;
  }
  /**
   * `Language` used to generate the executable bytes.
   *
   * Accepted values: LANGUAGE_UNSPECIFIED, FIREBASE_RULES, EVENT_FLOW_TRIGGERS
   *
   * @param self::LANGUAGE_* $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return self::LANGUAGE_*
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * `Ruleset` name associated with the `Release` executable.
   *
   * @param string $rulesetName
   */
  public function setRulesetName($rulesetName)
  {
    $this->rulesetName = $rulesetName;
  }
  /**
   * @return string
   */
  public function getRulesetName()
  {
    return $this->rulesetName;
  }
  /**
   * Optional, indicates the freshness of the result. The response is guaranteed
   * to be the latest within an interval up to the sync_time (inclusive).
   *
   * @param string $syncTime
   */
  public function setSyncTime($syncTime)
  {
    $this->syncTime = $syncTime;
  }
  /**
   * @return string
   */
  public function getSyncTime()
  {
    return $this->syncTime;
  }
  /**
   * Timestamp for the most recent `Release.update_time`.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetReleaseExecutableResponse::class, 'Google_Service_FirebaseRules_GetReleaseExecutableResponse');
