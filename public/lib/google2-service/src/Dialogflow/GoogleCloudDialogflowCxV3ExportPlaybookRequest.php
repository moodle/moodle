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

class GoogleCloudDialogflowCxV3ExportPlaybookRequest extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Flow content will be exported as raw bytes.
   */
  public const DATA_FORMAT_BLOB = 'BLOB';
  /**
   * Flow content will be exported in JSON format.
   */
  public const DATA_FORMAT_JSON = 'JSON';
  /**
   * Optional. The data format of the exported agent. If not specified, `BLOB`
   * is assumed.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the playbook
   * to. The format of this URI must be `gs:`. If left unspecified, the
   * serialized playbook is returned inline. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $playbookUri;

  /**
   * Optional. The data format of the exported agent. If not specified, `BLOB`
   * is assumed.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, BLOB, JSON
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
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the playbook
   * to. The format of this URI must be `gs:`. If left unspecified, the
   * serialized playbook is returned inline. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $playbookUri
   */
  public function setPlaybookUri($playbookUri)
  {
    $this->playbookUri = $playbookUri;
  }
  /**
   * @return string
   */
  public function getPlaybookUri()
  {
    return $this->playbookUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportPlaybookRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportPlaybookRequest');
