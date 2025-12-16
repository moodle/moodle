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

class GoogleCloudDialogflowV2OriginalDetectIntentRequest extends \Google\Model
{
  /**
   * Optional. This field is set to the value of the `QueryParameters.payload`
   * field passed in the request. Some integrations that query a Dialogflow
   * agent may provide additional information in the payload. In particular, for
   * the Dialogflow Phone Gateway integration, this field has the form: {
   * "telephony": { "caller_id": "+18558363987" } } Note: The caller ID field
   * (`caller_id`) will be redacted for Trial Edition agents and populated with
   * the caller ID in [E.164 format](https://en.wikipedia.org/wiki/E.164) for
   * Essentials Edition agents.
   *
   * @var array[]
   */
  public $payload;
  /**
   * The source of this request, e.g., `google`, `facebook`, `slack`. It is set
   * by Dialogflow-owned servers.
   *
   * @var string
   */
  public $source;
  /**
   * Optional. The version of the protocol used for this request. This field is
   * AoG-specific.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. This field is set to the value of the `QueryParameters.payload`
   * field passed in the request. Some integrations that query a Dialogflow
   * agent may provide additional information in the payload. In particular, for
   * the Dialogflow Phone Gateway integration, this field has the form: {
   * "telephony": { "caller_id": "+18558363987" } } Note: The caller ID field
   * (`caller_id`) will be redacted for Trial Edition agents and populated with
   * the caller ID in [E.164 format](https://en.wikipedia.org/wiki/E.164) for
   * Essentials Edition agents.
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * The source of this request, e.g., `google`, `facebook`, `slack`. It is set
   * by Dialogflow-owned servers.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Optional. The version of the protocol used for this request. This field is
   * AoG-specific.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2OriginalDetectIntentRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2OriginalDetectIntentRequest');
