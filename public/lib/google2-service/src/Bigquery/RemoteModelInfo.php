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

namespace Google\Service\Bigquery;

class RemoteModelInfo extends \Google\Model
{
  /**
   * Unspecified remote service type.
   */
  public const REMOTE_SERVICE_TYPE_REMOTE_SERVICE_TYPE_UNSPECIFIED = 'REMOTE_SERVICE_TYPE_UNSPECIFIED';
  /**
   * V3 Cloud AI Translation API. See more details at [Cloud Translation API]
   * (https://cloud.google.com/translate/docs/reference/rest).
   */
  public const REMOTE_SERVICE_TYPE_CLOUD_AI_TRANSLATE_V3 = 'CLOUD_AI_TRANSLATE_V3';
  /**
   * V1 Cloud AI Vision API See more details at [Cloud Vision API]
   * (https://cloud.google.com/vision/docs/reference/rest).
   */
  public const REMOTE_SERVICE_TYPE_CLOUD_AI_VISION_V1 = 'CLOUD_AI_VISION_V1';
  /**
   * V1 Cloud AI Natural Language API. See more details at [REST Resource:
   * documents](https://cloud.google.com/natural-
   * language/docs/reference/rest/v1/documents).
   */
  public const REMOTE_SERVICE_TYPE_CLOUD_AI_NATURAL_LANGUAGE_V1 = 'CLOUD_AI_NATURAL_LANGUAGE_V1';
  /**
   * V2 Speech-to-Text API. See more details at [Google Cloud Speech-to-Text V2
   * API](https://cloud.google.com/speech-to-text/v2/docs)
   */
  public const REMOTE_SERVICE_TYPE_CLOUD_AI_SPEECH_TO_TEXT_V2 = 'CLOUD_AI_SPEECH_TO_TEXT_V2';
  /**
   * Output only. Fully qualified name of the user-provided connection object of
   * the remote model. Format: ```"projects/{project_id}/locations/{location_id}
   * /connections/{connection_id}"```
   *
   * @var string
   */
  public $connection;
  /**
   * Output only. The endpoint for remote model.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Output only. Max number of rows in each batch sent to the remote service.
   * If unset, the number of rows in each batch is set dynamically.
   *
   * @var string
   */
  public $maxBatchingRows;
  /**
   * Output only. The model version for LLM.
   *
   * @var string
   */
  public $remoteModelVersion;
  /**
   * Output only. The remote service type for remote model.
   *
   * @var string
   */
  public $remoteServiceType;
  /**
   * Output only. The name of the speech recognizer to use for speech
   * recognition. The expected format is
   * `projects/{project}/locations/{location}/recognizers/{recognizer}`.
   * Customers can specify this field at model creation. If not specified, a
   * default recognizer `projects/{model
   * project}/locations/global/recognizers/_` will be used. See more details at
   * [recognizers](https://cloud.google.com/speech-to-
   * text/v2/docs/reference/rest/v2/projects.locations.recognizers)
   *
   * @var string
   */
  public $speechRecognizer;

  /**
   * Output only. Fully qualified name of the user-provided connection object of
   * the remote model. Format: ```"projects/{project_id}/locations/{location_id}
   * /connections/{connection_id}"```
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Output only. The endpoint for remote model.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Output only. Max number of rows in each batch sent to the remote service.
   * If unset, the number of rows in each batch is set dynamically.
   *
   * @param string $maxBatchingRows
   */
  public function setMaxBatchingRows($maxBatchingRows)
  {
    $this->maxBatchingRows = $maxBatchingRows;
  }
  /**
   * @return string
   */
  public function getMaxBatchingRows()
  {
    return $this->maxBatchingRows;
  }
  /**
   * Output only. The model version for LLM.
   *
   * @param string $remoteModelVersion
   */
  public function setRemoteModelVersion($remoteModelVersion)
  {
    $this->remoteModelVersion = $remoteModelVersion;
  }
  /**
   * @return string
   */
  public function getRemoteModelVersion()
  {
    return $this->remoteModelVersion;
  }
  /**
   * Output only. The remote service type for remote model.
   *
   * Accepted values: REMOTE_SERVICE_TYPE_UNSPECIFIED, CLOUD_AI_TRANSLATE_V3,
   * CLOUD_AI_VISION_V1, CLOUD_AI_NATURAL_LANGUAGE_V1,
   * CLOUD_AI_SPEECH_TO_TEXT_V2
   *
   * @param self::REMOTE_SERVICE_TYPE_* $remoteServiceType
   */
  public function setRemoteServiceType($remoteServiceType)
  {
    $this->remoteServiceType = $remoteServiceType;
  }
  /**
   * @return self::REMOTE_SERVICE_TYPE_*
   */
  public function getRemoteServiceType()
  {
    return $this->remoteServiceType;
  }
  /**
   * Output only. The name of the speech recognizer to use for speech
   * recognition. The expected format is
   * `projects/{project}/locations/{location}/recognizers/{recognizer}`.
   * Customers can specify this field at model creation. If not specified, a
   * default recognizer `projects/{model
   * project}/locations/global/recognizers/_` will be used. See more details at
   * [recognizers](https://cloud.google.com/speech-to-
   * text/v2/docs/reference/rest/v2/projects.locations.recognizers)
   *
   * @param string $speechRecognizer
   */
  public function setSpeechRecognizer($speechRecognizer)
  {
    $this->speechRecognizer = $speechRecognizer;
  }
  /**
   * @return string
   */
  public function getSpeechRecognizer()
  {
    return $this->speechRecognizer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoteModelInfo::class, 'Google_Service_Bigquery_RemoteModelInfo');
