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

class GoogleCloudRunV2RevisionScalingStatus extends \Google\Model
{
  /**
   * The current number of min instances provisioned for this revision.
   *
   * @var int
   */
  public $desiredMinInstanceCount;

  /**
   * The current number of min instances provisioned for this revision.
   *
   * @param int $desiredMinInstanceCount
   */
  public function setDesiredMinInstanceCount($desiredMinInstanceCount)
  {
    $this->desiredMinInstanceCount = $desiredMinInstanceCount;
  }
  /**
   * @return int
   */
  public function getDesiredMinInstanceCount()
  {
    return $this->desiredMinInstanceCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2RevisionScalingStatus::class, 'Google_Service_CloudRun_GoogleCloudRunV2RevisionScalingStatus');
