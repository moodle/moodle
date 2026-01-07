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

class GoogleCloudDialogflowCxV3ExportIntentsRequest extends \Google\Collection
{
  /**
   * Unspecified format. Treated as `BLOB`.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Intents will be exported as raw bytes.
   */
  public const DATA_FORMAT_BLOB = 'BLOB';
  /**
   * Intents will be exported in JSON format.
   */
  public const DATA_FORMAT_JSON = 'JSON';
  /**
   * Intents will be exported in CSV format.
   */
  public const DATA_FORMAT_CSV = 'CSV';
  protected $collection_key = 'intents';
  /**
   * Optional. The data format of the exported intents. If not specified, `BLOB`
   * is assumed.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * Required. The name of the intents to export. Format:
   * `projects//locations//agents//intents/`.
   *
   * @var string[]
   */
  public $intents;
  /**
   * Optional. The option to return the serialized intents inline.
   *
   * @var bool
   */
  public $intentsContentInline;
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the intents
   * to. The format of this URI must be `gs:`. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $intentsUri;

  /**
   * Optional. The data format of the exported intents. If not specified, `BLOB`
   * is assumed.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, BLOB, JSON, CSV
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
   * Required. The name of the intents to export. Format:
   * `projects//locations//agents//intents/`.
   *
   * @param string[] $intents
   */
  public function setIntents($intents)
  {
    $this->intents = $intents;
  }
  /**
   * @return string[]
   */
  public function getIntents()
  {
    return $this->intents;
  }
  /**
   * Optional. The option to return the serialized intents inline.
   *
   * @param bool $intentsContentInline
   */
  public function setIntentsContentInline($intentsContentInline)
  {
    $this->intentsContentInline = $intentsContentInline;
  }
  /**
   * @return bool
   */
  public function getIntentsContentInline()
  {
    return $this->intentsContentInline;
  }
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the intents
   * to. The format of this URI must be `gs:`. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportIntentsRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportIntentsRequest');
