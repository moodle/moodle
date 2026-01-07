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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1Identity extends \Google\Model
{
  protected $analysisStateType = IamPolicyAnalysisState::class;
  protected $analysisStateDataType = '';
  /**
   * The identity of members, formatted as appear in an [IAM policy
   * binding](https://cloud.google.com/iam/reference/rest/v1/Binding). For
   * example, they might be formatted like the following: - user:foo@google.com
   * - group:group1@google.com - serviceAccount:s1@prj1.iam.gserviceaccount.com
   * - projectOwner:some_project_id - domain:google.com - allUsers
   *
   * @var string
   */
  public $name;

  /**
   * The analysis state of this identity.
   *
   * @param IamPolicyAnalysisState $analysisState
   */
  public function setAnalysisState(IamPolicyAnalysisState $analysisState)
  {
    $this->analysisState = $analysisState;
  }
  /**
   * @return IamPolicyAnalysisState
   */
  public function getAnalysisState()
  {
    return $this->analysisState;
  }
  /**
   * The identity of members, formatted as appear in an [IAM policy
   * binding](https://cloud.google.com/iam/reference/rest/v1/Binding). For
   * example, they might be formatted like the following: - user:foo@google.com
   * - group:group1@google.com - serviceAccount:s1@prj1.iam.gserviceaccount.com
   * - projectOwner:some_project_id - domain:google.com - allUsers
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1Identity::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1Identity');
