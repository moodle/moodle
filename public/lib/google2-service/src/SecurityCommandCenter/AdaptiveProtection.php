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

namespace Google\Service\SecurityCommandCenter;

class AdaptiveProtection extends \Google\Model
{
  /**
   * A score of 0 means that there is low confidence that the detected event is
   * an actual attack. A score of 1 means that there is high confidence that the
   * detected event is an attack. See the [Adaptive Protection
   * documentation](https://cloud.google.com/armor/docs/adaptive-protection-
   * overview#configure-alert-tuning) for further explanation.
   *
   * @var 
   */
  public $confidence;

  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  public function getConfidence()
  {
    return $this->confidence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdaptiveProtection::class, 'Google_Service_SecurityCommandCenter_AdaptiveProtection');
