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

namespace Google\Service\Dataproc;

class ListWorkflowTemplatesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * Output only. This token is included in the response if there are more
   * results to fetch. To fetch additional results, provide this value as the
   * page_token in a subsequent ListWorkflowTemplatesRequest.
   *
   * @var string
   */
  public $nextPageToken;
  protected $templatesType = WorkflowTemplate::class;
  protected $templatesDataType = 'array';
  /**
   * Output only. List of workflow templates that could not be included in the
   * response. Attempting to get one of these resources may indicate why it was
   * not included in the list response.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Output only. This token is included in the response if there are more
   * results to fetch. To fetch additional results, provide this value as the
   * page_token in a subsequent ListWorkflowTemplatesRequest.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Output only. WorkflowTemplates list.
   *
   * @param WorkflowTemplate[] $templates
   */
  public function setTemplates($templates)
  {
    $this->templates = $templates;
  }
  /**
   * @return WorkflowTemplate[]
   */
  public function getTemplates()
  {
    return $this->templates;
  }
  /**
   * Output only. List of workflow templates that could not be included in the
   * response. Attempting to get one of these resources may indicate why it was
   * not included in the list response.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListWorkflowTemplatesResponse::class, 'Google_Service_Dataproc_ListWorkflowTemplatesResponse');
