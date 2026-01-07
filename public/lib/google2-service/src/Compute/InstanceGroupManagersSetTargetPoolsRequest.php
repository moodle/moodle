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

namespace Google\Service\Compute;

class InstanceGroupManagersSetTargetPoolsRequest extends \Google\Collection
{
  protected $collection_key = 'targetPools';
  /**
   * The fingerprint of the target pools information. Use this optional property
   * to prevent conflicts when multiple users change the target pools settings
   * concurrently. Obtain the fingerprint with theinstanceGroupManagers.get
   * method. Then, include the fingerprint in your request to ensure that you do
   * not overwrite changes that were applied from another concurrent request.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * The list of target pool URLs that instances in this managed instance group
   * belong to. The managed instance group applies these target pools to all of
   * the instances in the group. Existing instances and new instances in the
   * group all receive these target pool settings.
   *
   * @var string[]
   */
  public $targetPools;

  /**
   * The fingerprint of the target pools information. Use this optional property
   * to prevent conflicts when multiple users change the target pools settings
   * concurrently. Obtain the fingerprint with theinstanceGroupManagers.get
   * method. Then, include the fingerprint in your request to ensure that you do
   * not overwrite changes that were applied from another concurrent request.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * The list of target pool URLs that instances in this managed instance group
   * belong to. The managed instance group applies these target pools to all of
   * the instances in the group. Existing instances and new instances in the
   * group all receive these target pool settings.
   *
   * @param string[] $targetPools
   */
  public function setTargetPools($targetPools)
  {
    $this->targetPools = $targetPools;
  }
  /**
   * @return string[]
   */
  public function getTargetPools()
  {
    return $this->targetPools;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagersSetTargetPoolsRequest::class, 'Google_Service_Compute_InstanceGroupManagersSetTargetPoolsRequest');
