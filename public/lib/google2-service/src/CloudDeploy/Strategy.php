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

class Strategy extends \Google\Model
{
  protected $canaryType = Canary::class;
  protected $canaryDataType = '';
  protected $standardType = Standard::class;
  protected $standardDataType = '';

  /**
   * Optional. Canary deployment strategy provides progressive percentage based
   * deployments to a Target.
   *
   * @param Canary $canary
   */
  public function setCanary(Canary $canary)
  {
    $this->canary = $canary;
  }
  /**
   * @return Canary
   */
  public function getCanary()
  {
    return $this->canary;
  }
  /**
   * Optional. Standard deployment strategy executes a single deploy and allows
   * verifying the deployment.
   *
   * @param Standard $standard
   */
  public function setStandard(Standard $standard)
  {
    $this->standard = $standard;
  }
  /**
   * @return Standard
   */
  public function getStandard()
  {
    return $this->standard;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Strategy::class, 'Google_Service_CloudDeploy_Strategy');
