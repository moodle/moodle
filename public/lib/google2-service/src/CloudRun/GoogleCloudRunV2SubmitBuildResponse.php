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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2SubmitBuildResponse extends \Google\Model
{
  /**
   * URI of the base builder image in Artifact Registry being used in the build.
   * Used to opt into automatic base image updates.
   *
   * @var string
   */
  public $baseImageUri;
  /**
   * Warning message for the base image.
   *
   * @var string
   */
  public $baseImageWarning;
  protected $buildOperationType = GoogleLongrunningOperation::class;
  protected $buildOperationDataType = '';

  /**
   * URI of the base builder image in Artifact Registry being used in the build.
   * Used to opt into automatic base image updates.
   *
   * @param string $baseImageUri
   */
  public function setBaseImageUri($baseImageUri)
  {
    $this->baseImageUri = $baseImageUri;
  }
  /**
   * @return string
   */
  public function getBaseImageUri()
  {
    return $this->baseImageUri;
  }
  /**
   * Warning message for the base image.
   *
   * @param string $baseImageWarning
   */
  public function setBaseImageWarning($baseImageWarning)
  {
    $this->baseImageWarning = $baseImageWarning;
  }
  /**
   * @return string
   */
  public function getBaseImageWarning()
  {
    return $this->baseImageWarning;
  }
  /**
   * Cloud Build operation to be polled via CloudBuild API.
   *
   * @param GoogleLongrunningOperation $buildOperation
   */
  public function setBuildOperation(GoogleLongrunningOperation $buildOperation)
  {
    $this->buildOperation = $buildOperation;
  }
  /**
   * @return GoogleLongrunningOperation
   */
  public function getBuildOperation()
  {
    return $this->buildOperation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2SubmitBuildResponse::class, 'Google_Service_CloudRun_GoogleCloudRunV2SubmitBuildResponse');
