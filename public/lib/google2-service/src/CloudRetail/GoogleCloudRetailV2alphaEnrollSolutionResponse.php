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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2alphaEnrollSolutionResponse extends \Google\Model
{
  /**
   * Default value.
   */
  public const ENROLLED_SOLUTION_SOLUTION_TYPE_UNSPECIFIED = 'SOLUTION_TYPE_UNSPECIFIED';
  /**
   * Used for Recommendations AI.
   */
  public const ENROLLED_SOLUTION_SOLUTION_TYPE_RECOMMENDATION = 'SOLUTION_TYPE_RECOMMENDATION';
  /**
   * Used for Retail Search.
   */
  public const ENROLLED_SOLUTION_SOLUTION_TYPE_SEARCH = 'SOLUTION_TYPE_SEARCH';
  /**
   * Retail API solution that the project has enrolled.
   *
   * @var string
   */
  public $enrolledSolution;

  /**
   * Retail API solution that the project has enrolled.
   *
   * Accepted values: SOLUTION_TYPE_UNSPECIFIED, SOLUTION_TYPE_RECOMMENDATION,
   * SOLUTION_TYPE_SEARCH
   *
   * @param self::ENROLLED_SOLUTION_* $enrolledSolution
   */
  public function setEnrolledSolution($enrolledSolution)
  {
    $this->enrolledSolution = $enrolledSolution;
  }
  /**
   * @return self::ENROLLED_SOLUTION_*
   */
  public function getEnrolledSolution()
  {
    return $this->enrolledSolution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2alphaEnrollSolutionResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2alphaEnrollSolutionResponse');
