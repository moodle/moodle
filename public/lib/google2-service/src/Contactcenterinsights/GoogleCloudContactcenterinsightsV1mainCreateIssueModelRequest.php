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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainCreateIssueModelRequest extends \Google\Model
{
  protected $issueModelType = GoogleCloudContactcenterinsightsV1mainIssueModel::class;
  protected $issueModelDataType = '';
  /**
   * Required. The parent resource of the issue model.
   *
   * @var string
   */
  public $parent;

  /**
   * Required. The issue model to create.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueModel $issueModel
   */
  public function setIssueModel(GoogleCloudContactcenterinsightsV1mainIssueModel $issueModel)
  {
    $this->issueModel = $issueModel;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueModel
   */
  public function getIssueModel()
  {
    return $this->issueModel;
  }
  /**
   * Required. The parent resource of the issue model.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainCreateIssueModelRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainCreateIssueModelRequest');
