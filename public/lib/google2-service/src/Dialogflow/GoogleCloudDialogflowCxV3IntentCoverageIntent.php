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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3IntentCoverageIntent extends \Google\Model
{
  /**
   * Whether the intent is covered by at least one of the agent's test cases.
   *
   * @var bool
   */
  public $covered;
  /**
   * The intent full resource name
   *
   * @var string
   */
  public $intent;

  /**
   * Whether the intent is covered by at least one of the agent's test cases.
   *
   * @param bool $covered
   */
  public function setCovered($covered)
  {
    $this->covered = $covered;
  }
  /**
   * @return bool
   */
  public function getCovered()
  {
    return $this->covered;
  }
  /**
   * The intent full resource name
   *
   * @param string $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return string
   */
  public function getIntent()
  {
    return $this->intent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3IntentCoverageIntent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3IntentCoverageIntent');
