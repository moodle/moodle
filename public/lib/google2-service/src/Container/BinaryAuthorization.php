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

namespace Google\Service\Container;

class BinaryAuthorization extends \Google\Model
{
  /**
   * Default value
   */
  public const EVALUATION_MODE_EVALUATION_MODE_UNSPECIFIED = 'EVALUATION_MODE_UNSPECIFIED';
  /**
   * Disable BinaryAuthorization
   */
  public const EVALUATION_MODE_DISABLED = 'DISABLED';
  /**
   * Enforce Kubernetes admission requests with BinaryAuthorization using the
   * project's singleton policy. This is equivalent to setting the enabled
   * boolean to true.
   */
  public const EVALUATION_MODE_PROJECT_SINGLETON_POLICY_ENFORCE = 'PROJECT_SINGLETON_POLICY_ENFORCE';
  /**
   * This field is deprecated. Leave this unset and instead configure
   * BinaryAuthorization using evaluation_mode. If evaluation_mode is set to
   * anything other than EVALUATION_MODE_UNSPECIFIED, this field is ignored.
   *
   * @deprecated
   * @var bool
   */
  public $enabled;
  /**
   * Mode of operation for binauthz policy evaluation. If unspecified, defaults
   * to DISABLED.
   *
   * @var string
   */
  public $evaluationMode;

  /**
   * This field is deprecated. Leave this unset and instead configure
   * BinaryAuthorization using evaluation_mode. If evaluation_mode is set to
   * anything other than EVALUATION_MODE_UNSPECIFIED, this field is ignored.
   *
   * @deprecated
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Mode of operation for binauthz policy evaluation. If unspecified, defaults
   * to DISABLED.
   *
   * Accepted values: EVALUATION_MODE_UNSPECIFIED, DISABLED,
   * PROJECT_SINGLETON_POLICY_ENFORCE
   *
   * @param self::EVALUATION_MODE_* $evaluationMode
   */
  public function setEvaluationMode($evaluationMode)
  {
    $this->evaluationMode = $evaluationMode;
  }
  /**
   * @return self::EVALUATION_MODE_*
   */
  public function getEvaluationMode()
  {
    return $this->evaluationMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BinaryAuthorization::class, 'Google_Service_Container_BinaryAuthorization');
