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

class GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination extends \Google\Model
{
  /**
   * Commit message for the git push.
   *
   * @var string
   */
  public $commitMessage;
  /**
   * Tracking branch for the git push.
   *
   * @var string
   */
  public $trackingBranch;

  /**
   * Commit message for the git push.
   *
   * @param string $commitMessage
   */
  public function setCommitMessage($commitMessage)
  {
    $this->commitMessage = $commitMessage;
  }
  /**
   * @return string
   */
  public function getCommitMessage()
  {
    return $this->commitMessage;
  }
  /**
   * Tracking branch for the git push.
   *
   * @param string $trackingBranch
   */
  public function setTrackingBranch($trackingBranch)
  {
    $this->trackingBranch = $trackingBranch;
  }
  /**
   * @return string
   */
  public function getTrackingBranch()
  {
    return $this->trackingBranch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination');
