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

class GoogleChromePolicyVersionsV1FieldConstraints extends \Google\Model
{
  protected $numericRangeConstraintType = GoogleChromePolicyVersionsV1NumericRangeConstraint::class;
  protected $numericRangeConstraintDataType = '';
  protected $uploadedFileConstraintsType = GoogleChromePolicyVersionsV1UploadedFileConstraints::class;
  protected $uploadedFileConstraintsDataType = '';

  /**
   * The allowed range for numeric fields.
   *
   * @param GoogleChromePolicyVersionsV1NumericRangeConstraint $numericRangeConstraint
   */
  public function setNumericRangeConstraint(GoogleChromePolicyVersionsV1NumericRangeConstraint $numericRangeConstraint)
  {
    $this->numericRangeConstraint = $numericRangeConstraint;
  }
  /**
   * @return GoogleChromePolicyVersionsV1NumericRangeConstraint
   */
  public function getNumericRangeConstraint()
  {
    return $this->numericRangeConstraint;
  }
  /**
   * Constraints on the uploaded file of a file policy. If present, this policy
   * requires a URL that can be fetched by uploading a file with the constraints
   * specified in this proto.
   *
   * @param GoogleChromePolicyVersionsV1UploadedFileConstraints $uploadedFileConstraints
   */
  public function setUploadedFileConstraints(GoogleChromePolicyVersionsV1UploadedFileConstraints $uploadedFileConstraints)
  {
    $this->uploadedFileConstraints = $uploadedFileConstraints;
  }
  /**
   * @return GoogleChromePolicyVersionsV1UploadedFileConstraints
   */
  public function getUploadedFileConstraints()
  {
    return $this->uploadedFileConstraints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1FieldConstraints::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1FieldConstraints');
