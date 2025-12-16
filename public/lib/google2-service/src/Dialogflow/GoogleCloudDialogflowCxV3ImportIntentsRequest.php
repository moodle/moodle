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

class GoogleCloudDialogflowCxV3ImportIntentsRequest extends \Google\Model
{
  /**
   * Unspecified. Should not be used.
   */
  public const MERGE_OPTION_MERGE_OPTION_UNSPECIFIED = 'MERGE_OPTION_UNSPECIFIED';
  /**
   * DEPRECATED: Please use REPORT_CONFLICT instead. Fail the request if there
   * are intents whose display names conflict with the display names of intents
   * in the agent.
   *
   * @deprecated
   */
  public const MERGE_OPTION_REJECT = 'REJECT';
  /**
   * Replace the original intent in the agent with the new intent when display
   * name conflicts exist.
   */
  public const MERGE_OPTION_REPLACE = 'REPLACE';
  /**
   * Merge the original intent with the new intent when display name conflicts
   * exist.
   */
  public const MERGE_OPTION_MERGE = 'MERGE';
  /**
   * Create new intents with new display names to differentiate them from the
   * existing intents when display name conflicts exist.
   */
  public const MERGE_OPTION_RENAME = 'RENAME';
  /**
   * Report conflict information if display names conflict is detected.
   * Otherwise, import intents.
   */
  public const MERGE_OPTION_REPORT_CONFLICT = 'REPORT_CONFLICT';
  /**
   * Keep the original intent and discard the conflicting new intent when
   * display name conflicts exist.
   */
  public const MERGE_OPTION_KEEP = 'KEEP';
  protected $intentsContentType = GoogleCloudDialogflowCxV3InlineSource::class;
  protected $intentsContentDataType = '';
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import intents from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $intentsUri;
  /**
   * Merge option for importing intents. If not specified, `REJECT` is assumed.
   *
   * @var string
   */
  public $mergeOption;

  /**
   * Uncompressed byte content of intents.
   *
   * @param GoogleCloudDialogflowCxV3InlineSource $intentsContent
   */
  public function setIntentsContent(GoogleCloudDialogflowCxV3InlineSource $intentsContent)
  {
    $this->intentsContent = $intentsContent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3InlineSource
   */
  public function getIntentsContent()
  {
    return $this->intentsContent;
  }
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import intents from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $intentsUri
   */
  public function setIntentsUri($intentsUri)
  {
    $this->intentsUri = $intentsUri;
  }
  /**
   * @return string
   */
  public function getIntentsUri()
  {
    return $this->intentsUri;
  }
  /**
   * Merge option for importing intents. If not specified, `REJECT` is assumed.
   *
   * Accepted values: MERGE_OPTION_UNSPECIFIED, REJECT, REPLACE, MERGE, RENAME,
   * REPORT_CONFLICT, KEEP
   *
   * @param self::MERGE_OPTION_* $mergeOption
   */
  public function setMergeOption($mergeOption)
  {
    $this->mergeOption = $mergeOption;
  }
  /**
   * @return self::MERGE_OPTION_*
   */
  public function getMergeOption()
  {
    return $this->mergeOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ImportIntentsRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ImportIntentsRequest');
