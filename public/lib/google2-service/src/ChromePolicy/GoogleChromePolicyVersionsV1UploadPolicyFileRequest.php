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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1UploadPolicyFileRequest extends \Google\Model
{
  /**
   * Required. The fully qualified policy schema and field name this file is
   * uploaded for. This information will be used to validate the content type of
   * the file.
   *
   * @var string
   */
  public $policyField;

  /**
   * Required. The fully qualified policy schema and field name this file is
   * uploaded for. This information will be used to validate the content type of
   * the file.
   *
   * @param string $policyField
   */
  public function setPolicyField($policyField)
  {
    $this->policyField = $policyField;
  }
  /**
   * @return string
   */
  public function getPolicyField()
  {
    return $this->policyField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1UploadPolicyFileRequest::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1UploadPolicyFileRequest');
