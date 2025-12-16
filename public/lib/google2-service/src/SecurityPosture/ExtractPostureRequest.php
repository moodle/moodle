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

namespace Google\Service\SecurityPosture;

class ExtractPostureRequest extends \Google\Model
{
  /**
   * Required. An identifier for the posture.
   *
   * @var string
   */
  public $postureId;
  /**
   * Required. The organization, folder, or project from which policies are
   * extracted. Must be within the organization defined in parent. Use one of
   * the following formats: * `organization/{organization_number}` *
   * `folder/{folder_number}` * `project/{project_number}`
   *
   * @var string
   */
  public $workload;

  /**
   * Required. An identifier for the posture.
   *
   * @param string $postureId
   */
  public function setPostureId($postureId)
  {
    $this->postureId = $postureId;
  }
  /**
   * @return string
   */
  public function getPostureId()
  {
    return $this->postureId;
  }
  /**
   * Required. The organization, folder, or project from which policies are
   * extracted. Must be within the organization defined in parent. Use one of
   * the following formats: * `organization/{organization_number}` *
   * `folder/{folder_number}` * `project/{project_number}`
   *
   * @param string $workload
   */
  public function setWorkload($workload)
  {
    $this->workload = $workload;
  }
  /**
   * @return string
   */
  public function getWorkload()
  {
    return $this->workload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtractPostureRequest::class, 'Google_Service_SecurityPosture_ExtractPostureRequest');
