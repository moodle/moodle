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

class GoogleCloudDialogflowCxV3RestoreAgentRequest extends \Google\Model
{
  /**
   * Unspecified. Treated as KEEP.
   */
  public const RESTORE_OPTION_RESTORE_OPTION_UNSPECIFIED = 'RESTORE_OPTION_UNSPECIFIED';
  /**
   * Always respect the settings from the exported agent file. It may cause a
   * restoration failure if some settings (e.g. model type) are not supported in
   * the target agent.
   */
  public const RESTORE_OPTION_KEEP = 'KEEP';
  /**
   * Fallback to default settings if some settings are not supported in the
   * target agent.
   */
  public const RESTORE_OPTION_FALLBACK = 'FALLBACK';
  /**
   * Uncompressed raw byte content for agent.
   *
   * @var string
   */
  public $agentContent;
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * restore agent from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $agentUri;
  protected $gitSourceType = GoogleCloudDialogflowCxV3RestoreAgentRequestGitSource::class;
  protected $gitSourceDataType = '';
  /**
   * Agent restore mode. If not specified, `KEEP` is assumed.
   *
   * @var string
   */
  public $restoreOption;

  /**
   * Uncompressed raw byte content for agent.
   *
   * @param string $agentContent
   */
  public function setAgentContent($agentContent)
  {
    $this->agentContent = $agentContent;
  }
  /**
   * @return string
   */
  public function getAgentContent()
  {
    return $this->agentContent;
  }
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * restore agent from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $agentUri
   */
  public function setAgentUri($agentUri)
  {
    $this->agentUri = $agentUri;
  }
  /**
   * @return string
   */
  public function getAgentUri()
  {
    return $this->agentUri;
  }
  /**
   * Setting for restoring from a git branch
   *
   * @param GoogleCloudDialogflowCxV3RestoreAgentRequestGitSource $gitSource
   */
  public function setGitSource(GoogleCloudDialogflowCxV3RestoreAgentRequestGitSource $gitSource)
  {
    $this->gitSource = $gitSource;
  }
  /**
   * @return GoogleCloudDialogflowCxV3RestoreAgentRequestGitSource
   */
  public function getGitSource()
  {
    return $this->gitSource;
  }
  /**
   * Agent restore mode. If not specified, `KEEP` is assumed.
   *
   * Accepted values: RESTORE_OPTION_UNSPECIFIED, KEEP, FALLBACK
   *
   * @param self::RESTORE_OPTION_* $restoreOption
   */
  public function setRestoreOption($restoreOption)
  {
    $this->restoreOption = $restoreOption;
  }
  /**
   * @return self::RESTORE_OPTION_*
   */
  public function getRestoreOption()
  {
    return $this->restoreOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3RestoreAgentRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3RestoreAgentRequest');
