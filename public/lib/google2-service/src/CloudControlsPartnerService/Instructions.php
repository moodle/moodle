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

namespace Google\Service\CloudControlsPartnerService;

class Instructions extends \Google\Model
{
  protected $consoleInstructionsType = Console::class;
  protected $consoleInstructionsDataType = '';
  protected $gcloudInstructionsType = Gcloud::class;
  protected $gcloudInstructionsDataType = '';

  /**
   * Remediation instructions to resolve violation via cloud console
   *
   * @param Console $consoleInstructions
   */
  public function setConsoleInstructions(Console $consoleInstructions)
  {
    $this->consoleInstructions = $consoleInstructions;
  }
  /**
   * @return Console
   */
  public function getConsoleInstructions()
  {
    return $this->consoleInstructions;
  }
  /**
   * Remediation instructions to resolve violation via gcloud cli
   *
   * @param Gcloud $gcloudInstructions
   */
  public function setGcloudInstructions(Gcloud $gcloudInstructions)
  {
    $this->gcloudInstructions = $gcloudInstructions;
  }
  /**
   * @return Gcloud
   */
  public function getGcloudInstructions()
  {
    return $this->gcloudInstructions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instructions::class, 'Google_Service_CloudControlsPartnerService_Instructions');
