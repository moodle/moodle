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

namespace Google\Service\SASPortalTesting;

class SasPortalPolicy extends \Google\Collection
{
  protected $collection_key = 'assignments';
  protected $assignmentsType = SasPortalAssignment::class;
  protected $assignmentsDataType = 'array';
  /**
   * The etag is used for optimistic concurrency control as a way to help
   * prevent simultaneous updates of a policy from overwriting each other. It is
   * strongly suggested that systems make use of the etag in the read-modify-
   * write cycle to perform policy updates in order to avoid race conditions: An
   * etag is returned in the response to GetPolicy, and systems are expected to
   * put that etag in the request to SetPolicy to ensure that their change will
   * be applied to the same version of the policy. If no etag is provided in the
   * call to GetPolicy, then the existing policy is overwritten blindly.
   *
   * @var string
   */
  public $etag;

  /**
   * List of assignments
   *
   * @param SasPortalAssignment[] $assignments
   */
  public function setAssignments($assignments)
  {
    $this->assignments = $assignments;
  }
  /**
   * @return SasPortalAssignment[]
   */
  public function getAssignments()
  {
    return $this->assignments;
  }
  /**
   * The etag is used for optimistic concurrency control as a way to help
   * prevent simultaneous updates of a policy from overwriting each other. It is
   * strongly suggested that systems make use of the etag in the read-modify-
   * write cycle to perform policy updates in order to avoid race conditions: An
   * etag is returned in the response to GetPolicy, and systems are expected to
   * put that etag in the request to SetPolicy to ensure that their change will
   * be applied to the same version of the policy. If no etag is provided in the
   * call to GetPolicy, then the existing policy is overwritten blindly.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalPolicy::class, 'Google_Service_SASPortalTesting_SasPortalPolicy');
