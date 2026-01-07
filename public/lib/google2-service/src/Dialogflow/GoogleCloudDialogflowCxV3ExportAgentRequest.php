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

class GoogleCloudDialogflowCxV3ExportAgentRequest extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Agent content will be exported as raw bytes.
   */
  public const DATA_FORMAT_BLOB = 'BLOB';
  /**
   * Agent content will be exported in JSON Package format.
   */
  public const DATA_FORMAT_JSON_PACKAGE = 'JSON_PACKAGE';
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the agent
   * to. The format of this URI must be `gs:`. If left unspecified, the
   * serialized agent is returned inline. Dialogflow performs a write operation
   * for the Cloud Storage object on the caller's behalf, so your request
   * authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $agentUri;
  /**
   * Optional. The data format of the exported agent. If not specified, `BLOB`
   * is assumed.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * Optional. Environment name. If not set, draft environment is assumed.
   * Format: `projects//locations//agents//environments/`.
   *
   * @var string
   */
  public $environment;
  protected $gitDestinationType = GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination::class;
  protected $gitDestinationDataType = '';
  /**
   * Optional. Whether to include BigQuery Export setting.
   *
   * @var bool
   */
  public $includeBigqueryExportSettings;

  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the agent
   * to. The format of this URI must be `gs:`. If left unspecified, the
   * serialized agent is returned inline. Dialogflow performs a write operation
   * for the Cloud Storage object on the caller's behalf, so your request
   * authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
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
   * Optional. The data format of the exported agent. If not specified, `BLOB`
   * is assumed.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, BLOB, JSON_PACKAGE
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * Optional. Environment name. If not set, draft environment is assumed.
   * Format: `projects//locations//agents//environments/`.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Optional. The Git branch to export the agent to.
   *
   * @param GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination $gitDestination
   */
  public function setGitDestination(GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination $gitDestination)
  {
    $this->gitDestination = $gitDestination;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ExportAgentRequestGitDestination
   */
  public function getGitDestination()
  {
    return $this->gitDestination;
  }
  /**
   * Optional. Whether to include BigQuery Export setting.
   *
   * @param bool $includeBigqueryExportSettings
   */
  public function setIncludeBigqueryExportSettings($includeBigqueryExportSettings)
  {
    $this->includeBigqueryExportSettings = $includeBigqueryExportSettings;
  }
  /**
   * @return bool
   */
  public function getIncludeBigqueryExportSettings()
  {
    return $this->includeBigqueryExportSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportAgentRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportAgentRequest');
