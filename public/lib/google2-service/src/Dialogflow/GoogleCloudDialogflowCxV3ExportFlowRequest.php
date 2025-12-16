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

class GoogleCloudDialogflowCxV3ExportFlowRequest extends \Google\Model
{
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the flow to.
   * The format of this URI must be `gs:`. If left unspecified, the serialized
   * flow is returned inline. Dialogflow performs a write operation for the
   * Cloud Storage object on the caller's behalf, so your request authentication
   * must have write permissions for the object. For more information, see
   * [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $flowUri;
  /**
   * Optional. Whether to export flows referenced by the specified flow.
   *
   * @var bool
   */
  public $includeReferencedFlows;

  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the flow to.
   * The format of this URI must be `gs:`. If left unspecified, the serialized
   * flow is returned inline. Dialogflow performs a write operation for the
   * Cloud Storage object on the caller's behalf, so your request authentication
   * must have write permissions for the object. For more information, see
   * [Dialogflow access
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
   * Optional. Whether to export flows referenced by the specified flow.
   *
   * @param bool $includeReferencedFlows
   */
  public function setIncludeReferencedFlows($includeReferencedFlows)
  {
    $this->includeReferencedFlows = $includeReferencedFlows;
  }
  /**
   * @return bool
   */
  public function getIncludeReferencedFlows()
  {
    return $this->includeReferencedFlows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportFlowRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportFlowRequest');
