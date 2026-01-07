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

class GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse extends \Google\Model
{
  protected $playbookType = GoogleCloudDialogflowCxV3Playbook::class;
  protected $playbookDataType = '';

  /**
   * The updated playbook.
   *
   * @param GoogleCloudDialogflowCxV3Playbook $playbook
   */
  public function setPlaybook(GoogleCloudDialogflowCxV3Playbook $playbook)
  {
    $this->playbook = $playbook;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Playbook
   */
  public function getPlaybook()
  {
    return $this->playbook;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3RestorePlaybookVersionResponse');
