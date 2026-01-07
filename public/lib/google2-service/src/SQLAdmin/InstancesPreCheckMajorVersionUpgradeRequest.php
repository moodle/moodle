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

namespace Google\Service\SQLAdmin;

class InstancesPreCheckMajorVersionUpgradeRequest extends \Google\Model
{
  protected $preCheckMajorVersionUpgradeContextType = PreCheckMajorVersionUpgradeContext::class;
  protected $preCheckMajorVersionUpgradeContextDataType = '';

  /**
   * Required. Contains details about the pre-check major version upgrade
   * operation.
   *
   * @param PreCheckMajorVersionUpgradeContext $preCheckMajorVersionUpgradeContext
   */
  public function setPreCheckMajorVersionUpgradeContext(PreCheckMajorVersionUpgradeContext $preCheckMajorVersionUpgradeContext)
  {
    $this->preCheckMajorVersionUpgradeContext = $preCheckMajorVersionUpgradeContext;
  }
  /**
   * @return PreCheckMajorVersionUpgradeContext
   */
  public function getPreCheckMajorVersionUpgradeContext()
  {
    return $this->preCheckMajorVersionUpgradeContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesPreCheckMajorVersionUpgradeRequest::class, 'Google_Service_SQLAdmin_InstancesPreCheckMajorVersionUpgradeRequest');
