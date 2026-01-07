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

class GoogleCloudContactcenterinsightsV1ImportIssueModelRequest extends \Google\Model
{
  /**
   * Optional. If set to true, will create an issue model from the imported file
   * with randomly generated IDs for the issue model and corresponding issues.
   * Otherwise, replaces an existing model with the same ID as the file.
   *
   * @var bool
   */
  public $createNewModel;
  protected $gcsSourceType = GoogleCloudContactcenterinsightsV1ImportIssueModelRequestGcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Required. The parent resource of the issue model.
   *
   * @var string
   */
  public $parent;

  /**
   * Optional. If set to true, will create an issue model from the imported file
   * with randomly generated IDs for the issue model and corresponding issues.
   * Otherwise, replaces an existing model with the same ID as the file.
   *
   * @param bool $createNewModel
   */
  public function setCreateNewModel($createNewModel)
  {
    $this->createNewModel = $createNewModel;
  }
  /**
   * @return bool
   */
  public function getCreateNewModel()
  {
    return $this->createNewModel;
  }
  /**
   * Google Cloud Storage source message.
   *
   * @param GoogleCloudContactcenterinsightsV1ImportIssueModelRequestGcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudContactcenterinsightsV1ImportIssueModelRequestGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ImportIssueModelRequestGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
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
class_alias(GoogleCloudContactcenterinsightsV1ImportIssueModelRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ImportIssueModelRequest');
