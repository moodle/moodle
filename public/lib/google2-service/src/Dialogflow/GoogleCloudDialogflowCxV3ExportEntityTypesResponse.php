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

class GoogleCloudDialogflowCxV3ExportEntityTypesResponse extends \Google\Model
{
  protected $entityTypesContentType = GoogleCloudDialogflowCxV3InlineDestination::class;
  protected $entityTypesContentDataType = '';
  /**
   * The URI to a file containing the exported entity types. This field is
   * populated only if `entity_types_uri` is specified in
   * ExportEntityTypesRequest.
   *
   * @var string
   */
  public $entityTypesUri;

  /**
   * Uncompressed byte content for entity types. This field is populated only if
   * `entity_types_content_inline` is set to true in ExportEntityTypesRequest.
   *
   * @param GoogleCloudDialogflowCxV3InlineDestination $entityTypesContent
   */
  public function setEntityTypesContent(GoogleCloudDialogflowCxV3InlineDestination $entityTypesContent)
  {
    $this->entityTypesContent = $entityTypesContent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3InlineDestination
   */
  public function getEntityTypesContent()
  {
    return $this->entityTypesContent;
  }
  /**
   * The URI to a file containing the exported entity types. This field is
   * populated only if `entity_types_uri` is specified in
   * ExportEntityTypesRequest.
   *
   * @param string $entityTypesUri
   */
  public function setEntityTypesUri($entityTypesUri)
  {
    $this->entityTypesUri = $entityTypesUri;
  }
  /**
   * @return string
   */
  public function getEntityTypesUri()
  {
    return $this->entityTypesUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportEntityTypesResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportEntityTypesResponse');
