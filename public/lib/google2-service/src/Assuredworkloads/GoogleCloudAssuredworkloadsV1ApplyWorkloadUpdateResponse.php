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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateResponse extends \Google\Model
{
  protected $appliedUpdateType = GoogleCloudAssuredworkloadsV1WorkloadUpdate::class;
  protected $appliedUpdateDataType = '';

  /**
   * The update that was applied.
   *
   * @param GoogleCloudAssuredworkloadsV1WorkloadUpdate $appliedUpdate
   */
  public function setAppliedUpdate(GoogleCloudAssuredworkloadsV1WorkloadUpdate $appliedUpdate)
  {
    $this->appliedUpdate = $appliedUpdate;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1WorkloadUpdate
   */
  public function getAppliedUpdate()
  {
    return $this->appliedUpdate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateResponse::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateResponse');
