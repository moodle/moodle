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

class GoogleCloudDialogflowCxV3ImportFlowRequest extends \Google\Model
{
  /**
   * Unspecified. Treated as `KEEP`.
   */
  public const IMPORT_OPTION_IMPORT_OPTION_UNSPECIFIED = 'IMPORT_OPTION_UNSPECIFIED';
  /**
   * Always respect settings in exported flow content. It may cause a import
   * failure if some settings (e.g. custom NLU) are not supported in the agent
   * to import into.
   */
  public const IMPORT_OPTION_KEEP = 'KEEP';
  /**
   * Fallback to default settings if some settings are not supported in the
   * agent to import into. E.g. Standard NLU will be used if custom NLU is not
   * available.
   */
  public const IMPORT_OPTION_FALLBACK = 'FALLBACK';
  /**
   * Uncompressed raw byte content for flow.
   *
   * @var string
   */
  public $flowContent;
  protected $flowImportStrategyType = GoogleCloudDialogflowCxV3FlowImportStrategy::class;
  protected $flowImportStrategyDataType = '';
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import flow from. The format of this URI must be `gs:`. Dialogflow performs
   * a read operation for the Cloud Storage object on the caller's behalf, so
   * your request authentication must have read permissions for the object. For
   * more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $flowUri;
  /**
   * Flow import mode. If not specified, `KEEP` is assumed.
   *
   * @var string
   */
  public $importOption;

  /**
   * Uncompressed raw byte content for flow.
   *
   * @param string $flowContent
   */
  public function setFlowContent($flowContent)
  {
    $this->flowContent = $flowContent;
  }
  /**
   * @return string
   */
  public function getFlowContent()
  {
    return $this->flowContent;
  }
  /**
   * Optional. Specifies the import strategy used when resolving resource
   * conflicts.
   *
   * @param GoogleCloudDialogflowCxV3FlowImportStrategy $flowImportStrategy
   */
  public function setFlowImportStrategy(GoogleCloudDialogflowCxV3FlowImportStrategy $flowImportStrategy)
  {
    $this->flowImportStrategy = $flowImportStrategy;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FlowImportStrategy
   */
  public function getFlowImportStrategy()
  {
    return $this->flowImportStrategy;
  }
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import flow from. The format of this URI must be `gs:`. Dialogflow performs
   * a read operation for the Cloud Storage object on the caller's behalf, so
   * your request authentication must have read permissions for the object. For
   * more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $flowUri
   */
  public function setFlowUri($flowUri)
  {
    $this->flowUri = $flowUri;
  }
  /**
   * @return string
   */
  public function getFlowUri()
  {
    return $this->flowUri;
  }
  /**
   * Flow import mode. If not specified, `KEEP` is assumed.
   *
   * Accepted values: IMPORT_OPTION_UNSPECIFIED, KEEP, FALLBACK
   *
   * @param self::IMPORT_OPTION_* $importOption
   */
  public function setImportOption($importOption)
  {
    $this->importOption = $importOption;
  }
  /**
   * @return self::IMPORT_OPTION_*
   */
  public function getImportOption()
  {
    return $this->importOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ImportFlowRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ImportFlowRequest');
