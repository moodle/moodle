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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ApiView extends \Google\Model
{
  protected $mcpServerViewType = GoogleCloudApihubV1FlattenedApiVersionDeploymentView::class;
  protected $mcpServerViewDataType = '';
  protected $mcpToolViewType = GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView::class;
  protected $mcpToolViewDataType = '';

  /**
   * Output only. MCP server view.
   *
   * @param GoogleCloudApihubV1FlattenedApiVersionDeploymentView $mcpServerView
   */
  public function setMcpServerView(GoogleCloudApihubV1FlattenedApiVersionDeploymentView $mcpServerView)
  {
    $this->mcpServerView = $mcpServerView;
  }
  /**
   * @return GoogleCloudApihubV1FlattenedApiVersionDeploymentView
   */
  public function getMcpServerView()
  {
    return $this->mcpServerView;
  }
  /**
   * Output only. MCP tools view.
   *
   * @param GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView $mcpToolView
   */
  public function setMcpToolView(GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView $mcpToolView)
  {
    $this->mcpToolView = $mcpToolView;
  }
  /**
   * @return GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView
   */
  public function getMcpToolView()
  {
    return $this->mcpToolView;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ApiView::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApiView');
