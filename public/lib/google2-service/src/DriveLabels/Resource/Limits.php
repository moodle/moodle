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

namespace Google\Service\DriveLabels\Resource;

use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2LabelLimits;

/**
 * The "limits" collection of methods.
 * Typical usage is:
 *  <code>
 *   $drivelabelsService = new Google\Service\DriveLabels(...);
 *   $limits = $drivelabelsService->limits;
 *  </code>
 */
class Limits extends \Google\Service\Resource
{
  /**
   * Get the constraints on the structure of a label; such as, the maximum number
   * of fields allowed and maximum length of the label title. (limits.getLabel)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string name Required. Label revision resource name must be:
   * "limits/label".
   * @return GoogleAppsDriveLabelsV2LabelLimits
   * @throws \Google\Service\Exception
   */
  public function getLabel($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('getLabel', [$params], GoogleAppsDriveLabelsV2LabelLimits::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Limits::class, 'Google_Service_DriveLabels_Resource_Limits');
