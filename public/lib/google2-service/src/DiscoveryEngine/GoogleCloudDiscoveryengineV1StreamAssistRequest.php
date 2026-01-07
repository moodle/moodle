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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1StreamAssistRequest extends \Google\Model
{
  protected $generationSpecType = GoogleCloudDiscoveryengineV1StreamAssistRequestGenerationSpec::class;
  protected $generationSpecDataType = '';
  protected $queryType = GoogleCloudDiscoveryengineV1Query::class;
  protected $queryDataType = '';
  /**
   * Optional. The session to use for the request. If specified, the assistant
   * has access to the session history, and the query and the answer are stored
   * there. If `-` is specified as the session ID, or it is left empty, then a
   * new session is created with an automatically generated ID. Format: `project
   * s/{project}/locations/{location}/collections/{collection}/engines/{engine}/
   * sessions/{session}`
   *
   * @var string
   */
  public $session;
  protected $toolsSpecType = GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpec::class;
  protected $toolsSpecDataType = '';
  protected $userMetadataType = GoogleCloudDiscoveryengineV1AssistUserMetadata::class;
  protected $userMetadataDataType = '';

  /**
   * Optional. Specification of the generation configuration for the request.
   *
   * @param GoogleCloudDiscoveryengineV1StreamAssistRequestGenerationSpec $generationSpec
   */
  public function setGenerationSpec(GoogleCloudDiscoveryengineV1StreamAssistRequestGenerationSpec $generationSpec)
  {
    $this->generationSpec = $generationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1StreamAssistRequestGenerationSpec
   */
  public function getGenerationSpec()
  {
    return $this->generationSpec;
  }
  /**
   * Optional. Current user query. Empty query is only supported if `file_ids`
   * are provided. In this case, the answer will be generated based on those
   * context files.
   *
   * @param GoogleCloudDiscoveryengineV1Query $query
   */
  public function setQuery(GoogleCloudDiscoveryengineV1Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Optional. The session to use for the request. If specified, the assistant
   * has access to the session history, and the query and the answer are stored
   * there. If `-` is specified as the session ID, or it is left empty, then a
   * new session is created with an automatically generated ID. Format: `project
   * s/{project}/locations/{location}/collections/{collection}/engines/{engine}/
   * sessions/{session}`
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * Optional. Specification of tools that are used to serve the request.
   *
   * @param GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpec $toolsSpec
   */
  public function setToolsSpec(GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpec $toolsSpec)
  {
    $this->toolsSpec = $toolsSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1StreamAssistRequestToolsSpec
   */
  public function getToolsSpec()
  {
    return $this->toolsSpec;
  }
  /**
   * Optional. Information about the user initiating the query.
   *
   * @param GoogleCloudDiscoveryengineV1AssistUserMetadata $userMetadata
   */
  public function setUserMetadata(GoogleCloudDiscoveryengineV1AssistUserMetadata $userMetadata)
  {
    $this->userMetadata = $userMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistUserMetadata
   */
  public function getUserMetadata()
  {
    return $this->userMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1StreamAssistRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1StreamAssistRequest');
