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

class GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio extends \Google\Model
{
  /**
   * Required. URI to a Google Cloud Storage object containing the audio to
   * play, e.g., "gs://bucket/object". The object must contain a single channel
   * (mono) of linear PCM audio (2 bytes / sample) at 8kHz. This object must be
   * readable by the `service-@gcp-sa-dialogflow.iam.gserviceaccount.com`
   * service account where is the number of the Telephony Gateway project
   * (usually the same as the Dialogflow agent project). If the Google Cloud
   * Storage bucket is in the Telephony Gateway project, this permission is
   * added by default when enabling the Dialogflow V2 API. For audio from other
   * sources, consider using the `TelephonySynthesizeSpeech` message with SSML.
   *
   * @var string
   */
  public $audioUri;

  /**
   * Required. URI to a Google Cloud Storage object containing the audio to
   * play, e.g., "gs://bucket/object". The object must contain a single channel
   * (mono) of linear PCM audio (2 bytes / sample) at 8kHz. This object must be
   * readable by the `service-@gcp-sa-dialogflow.iam.gserviceaccount.com`
   * service account where is the number of the Telephony Gateway project
   * (usually the same as the Dialogflow agent project). If the Google Cloud
   * Storage bucket is in the Telephony Gateway project, this permission is
   * added by default when enabling the Dialogflow V2 API. For audio from other
   * sources, consider using the `TelephonySynthesizeSpeech` message with SSML.
   *
   * @param string $audioUri
   */
  public function setAudioUri($audioUri)
  {
    $this->audioUri = $audioUri;
  }
  /**
   * @return string
   */
  public function getAudioUri()
  {
    return $this->audioUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio');
