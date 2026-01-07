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

namespace Google\Service\Texttospeech;

class CustomVoiceParams extends \Google\Model
{
  /**
   * Request with reported usage unspecified will be rejected.
   */
  public const REPORTED_USAGE_REPORTED_USAGE_UNSPECIFIED = 'REPORTED_USAGE_UNSPECIFIED';
  /**
   * For scenarios where the synthesized audio is not downloadable and can only
   * be used once. For example, real-time request in IVR system.
   */
  public const REPORTED_USAGE_REALTIME = 'REALTIME';
  /**
   * For scenarios where the synthesized audio is downloadable and can be
   * reused. For example, the synthesized audio is downloaded, stored in
   * customer service system and played repeatedly.
   */
  public const REPORTED_USAGE_OFFLINE = 'OFFLINE';
  /**
   * Required. The name of the AutoML model that synthesizes the custom voice.
   *
   * @var string
   */
  public $model;
  /**
   * Optional. Deprecated. The usage of the synthesized audio to be reported.
   *
   * @deprecated
   * @var string
   */
  public $reportedUsage;

  /**
   * Required. The name of the AutoML model that synthesizes the custom voice.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Optional. Deprecated. The usage of the synthesized audio to be reported.
   *
   * Accepted values: REPORTED_USAGE_UNSPECIFIED, REALTIME, OFFLINE
   *
   * @deprecated
   * @param self::REPORTED_USAGE_* $reportedUsage
   */
  public function setReportedUsage($reportedUsage)
  {
    $this->reportedUsage = $reportedUsage;
  }
  /**
   * @deprecated
   * @return self::REPORTED_USAGE_*
   */
  public function getReportedUsage()
  {
    return $this->reportedUsage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomVoiceParams::class, 'Google_Service_Texttospeech_CustomVoiceParams');
