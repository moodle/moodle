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

class GoogleCloudDialogflowCxV3ExportFlowResponse extends \Google\Model
{
  /**
   * Uncompressed raw byte content for flow.
   *
   * @var string
   */
  public $flowContent;
  /**
   * The URI to a file containing the exported flow. This field is populated
   * only if `flow_uri` is specified in ExportFlowRequest.
   *
   * @var string
   */
  public $flowUri;

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
   * The URI to a file containing the exported flow. This field is populated
   * only if `flow_uri` is specified in ExportFlowRequest.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportFlowResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportFlowResponse');
