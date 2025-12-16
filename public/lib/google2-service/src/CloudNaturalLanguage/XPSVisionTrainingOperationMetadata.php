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

namespace Google\Service\CloudNaturalLanguage;

class XPSVisionTrainingOperationMetadata extends \Google\Model
{
  protected $explanationUsageType = InfraUsage::class;
  protected $explanationUsageDataType = '';

  /**
   * Aggregated infra usage within certain time period, for billing report
   * purpose if XAI is enable in training request.
   *
   * @param InfraUsage $explanationUsage
   */
  public function setExplanationUsage(InfraUsage $explanationUsage)
  {
    $this->explanationUsage = $explanationUsage;
  }
  /**
   * @return InfraUsage
   */
  public function getExplanationUsage()
  {
    return $this->explanationUsage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVisionTrainingOperationMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSVisionTrainingOperationMetadata');
