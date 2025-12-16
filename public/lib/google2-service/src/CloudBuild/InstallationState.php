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

namespace Google\Service\CloudBuild;

class InstallationState extends \Google\Model
{
  /**
   * No stage specified.
   */
  public const STAGE_STAGE_UNSPECIFIED = 'STAGE_UNSPECIFIED';
  /**
   * Only for GitHub Enterprise. An App creation has been requested. The user
   * needs to confirm the creation in their GitHub enterprise host.
   */
  public const STAGE_PENDING_CREATE_APP = 'PENDING_CREATE_APP';
  /**
   * User needs to authorize the GitHub (or Enterprise) App via OAuth.
   */
  public const STAGE_PENDING_USER_OAUTH = 'PENDING_USER_OAUTH';
  /**
   * User needs to follow the link to install the GitHub (or Enterprise) App.
   */
  public const STAGE_PENDING_INSTALL_APP = 'PENDING_INSTALL_APP';
  /**
   * Installation process has been completed.
   */
  public const STAGE_COMPLETE = 'COMPLETE';
  /**
   * Output only. Link to follow for next action. Empty string if the
   * installation is already complete.
   *
   * @var string
   */
  public $actionUri;
  /**
   * Output only. Message of what the user should do next to continue the
   * installation. Empty string if the installation is already complete.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. Current step of the installation process.
   *
   * @var string
   */
  public $stage;

  /**
   * Output only. Link to follow for next action. Empty string if the
   * installation is already complete.
   *
   * @param string $actionUri
   */
  public function setActionUri($actionUri)
  {
    $this->actionUri = $actionUri;
  }
  /**
   * @return string
   */
  public function getActionUri()
  {
    return $this->actionUri;
  }
  /**
   * Output only. Message of what the user should do next to continue the
   * installation. Empty string if the installation is already complete.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. Current step of the installation process.
   *
   * Accepted values: STAGE_UNSPECIFIED, PENDING_CREATE_APP, PENDING_USER_OAUTH,
   * PENDING_INSTALL_APP, COMPLETE
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstallationState::class, 'Google_Service_CloudBuild_InstallationState');
