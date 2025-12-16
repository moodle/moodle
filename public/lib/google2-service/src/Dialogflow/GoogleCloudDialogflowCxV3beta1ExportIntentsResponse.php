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

class GoogleCloudDialogflowCxV3beta1ExportIntentsResponse extends \Google\Model
{
  protected $intentsContentType = GoogleCloudDialogflowCxV3beta1InlineDestination::class;
  protected $intentsContentDataType = '';
  /**
   * The URI to a file containing the exported intents. This field is populated
   * only if `intents_uri` is specified in ExportIntentsRequest.
   *
   * @var string
   */
  public $intentsUri;

  /**
   * Uncompressed byte content for intents. This field is populated only if
   * `intents_content_inline` is set to true in ExportIntentsRequest.
   *
   * @param GoogleCloudDialogflowCxV3beta1InlineDestination $intentsContent
   */
  public function setIntentsContent(GoogleCloudDialogflowCxV3beta1InlineDestination $intentsContent)
  {
    $this->intentsContent = $intentsContent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1InlineDestination
   */
  public function getIntentsContent()
  {
    return $this->intentsContent;
  }
  /**
   * The URI to a file containing the exported intents. This field is populated
   * only if `intents_uri` is specified in ExportIntentsRequest.
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
class_alias(GoogleCloudDialogflowCxV3beta1ExportIntentsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ExportIntentsResponse');
