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

class GoogleCloudDialogflowCxV3PlaybookTransition extends \Google\Model
{
  /**
   * Output only. The display name of the playbook.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @var string
   */
  public $playbook;

  /**
   * Output only. The display name of the playbook.
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
   * Required. The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @param string $playbook
   */
  public function setPlaybook($playbook)
  {
    $this->playbook = $playbook;
  }
  /**
   * @return string
   */
  public function getPlaybook()
  {
    return $this->playbook;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3PlaybookTransition::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookTransition');
