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

namespace Google\Service\CloudDeploy;

class CustomTarget extends \Google\Model
{
  /**
   * Required. The name of the CustomTargetType. Format must be `projects/{proje
   * ct}/locations/{location}/customTargetTypes/{custom_target_type}`.
   *
   * @var string
   */
  public $customTargetType;

  /**
   * Required. The name of the CustomTargetType. Format must be `projects/{proje
   * ct}/locations/{location}/customTargetTypes/{custom_target_type}`.
   *
   * @param string $customTargetType
   */
  public function setCustomTargetType($customTargetType)
  {
    $this->customTargetType = $customTargetType;
  }
  /**
   * @return string
   */
  public function getCustomTargetType()
  {
    return $this->customTargetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomTarget::class, 'Google_Service_CloudDeploy_CustomTarget');
