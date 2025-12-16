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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AssistUserMetadata extends \Google\Model
{
  /**
   * Optional. Preferred language to be used for answering if language detection
   * fails. Also used as the language of error messages created by actions,
   * regardless of language detection results.
   *
   * @var string
   */
  public $preferredLanguageCode;
  /**
   * Optional. IANA time zone, e.g. Europe/Budapest.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Optional. Preferred language to be used for answering if language detection
   * fails. Also used as the language of error messages created by actions,
   * regardless of language detection results.
   *
   * @param string $preferredLanguageCode
   */
  public function setPreferredLanguageCode($preferredLanguageCode)
  {
    $this->preferredLanguageCode = $preferredLanguageCode;
  }
  /**
   * @return string
   */
  public function getPreferredLanguageCode()
  {
    return $this->preferredLanguageCode;
  }
  /**
   * Optional. IANA time zone, e.g. Europe/Budapest.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistUserMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistUserMetadata');
