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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1ViolationRemediationInstructionsConsole extends \Google\Collection
{
  protected $collection_key = 'steps';
  /**
   * Additional urls for more information about steps
   *
   * @var string[]
   */
  public $additionalLinks;
  /**
   * Link to console page where violations can be resolved
   *
   * @var string[]
   */
  public $consoleUris;
  /**
   * Steps to resolve violation via cloud console
   *
   * @var string[]
   */
  public $steps;

  /**
   * Additional urls for more information about steps
   *
   * @param string[] $additionalLinks
   */
  public function setAdditionalLinks($additionalLinks)
  {
    $this->additionalLinks = $additionalLinks;
  }
  /**
   * @return string[]
   */
  public function getAdditionalLinks()
  {
    return $this->additionalLinks;
  }
  /**
   * Link to console page where violations can be resolved
   *
   * @param string[] $consoleUris
   */
  public function setConsoleUris($consoleUris)
  {
    $this->consoleUris = $consoleUris;
  }
  /**
   * @return string[]
   */
  public function getConsoleUris()
  {
    return $this->consoleUris;
  }
  /**
   * Steps to resolve violation via cloud console
   *
   * @param string[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return string[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1ViolationRemediationInstructionsConsole::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1ViolationRemediationInstructionsConsole');
