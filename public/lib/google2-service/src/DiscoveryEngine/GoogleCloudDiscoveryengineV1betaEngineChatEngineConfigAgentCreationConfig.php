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

class GoogleCloudDiscoveryengineV1betaEngineChatEngineConfigAgentCreationConfig extends \Google\Model
{
  /**
   * Name of the company, organization or other entity that the agent
   * represents. Used for knowledge connector LLM prompt and for knowledge
   * search.
   *
   * @var string
   */
  public $business;
  /**
   * Required. The default language of the agent as a language tag. See
   * [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes.
   *
   * @var string
   */
  public $defaultLanguageCode;
  /**
   * Agent location for Agent creation, supported values: global/us/eu. If not
   * provided, us Engine will create Agent using us-central-1 by default; eu
   * Engine will create Agent using eu-west-1 by default.
   *
   * @var string
   */
  public $location;
  /**
   * Required. The time zone of the agent from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Name of the company, organization or other entity that the agent
   * represents. Used for knowledge connector LLM prompt and for knowledge
   * search.
   *
   * @param string $business
   */
  public function setBusiness($business)
  {
    $this->business = $business;
  }
  /**
   * @return string
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Required. The default language of the agent as a language tag. See
   * [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes.
   *
   * @param string $defaultLanguageCode
   */
  public function setDefaultLanguageCode($defaultLanguageCode)
  {
    $this->defaultLanguageCode = $defaultLanguageCode;
  }
  /**
   * @return string
   */
  public function getDefaultLanguageCode()
  {
    return $this->defaultLanguageCode;
  }
  /**
   * Agent location for Agent creation, supported values: global/us/eu. If not
   * provided, us Engine will create Agent using us-central-1 by default; eu
   * Engine will create Agent using eu-west-1 by default.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. The time zone of the agent from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris.
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
class_alias(GoogleCloudDiscoveryengineV1betaEngineChatEngineConfigAgentCreationConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaEngineChatEngineConfigAgentCreationConfig');
