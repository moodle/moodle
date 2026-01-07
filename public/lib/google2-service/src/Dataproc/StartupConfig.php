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

namespace Google\Service\Dataproc;

class StartupConfig extends \Google\Model
{
  /**
   * Optional. The config setting to enable cluster creation/ updation to be
   * successful only after required_registration_fraction of instances are up
   * and running. This configuration is applicable to only secondary workers for
   * now. The cluster will fail if required_registration_fraction of instances
   * are not available. This will include instance creation, agent registration,
   * and service registration (if enabled).
   *
   * @var 
   */
  public $requiredRegistrationFraction;

  public function setRequiredRegistrationFraction($requiredRegistrationFraction)
  {
    $this->requiredRegistrationFraction = $requiredRegistrationFraction;
  }
  public function getRequiredRegistrationFraction()
  {
    return $this->requiredRegistrationFraction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartupConfig::class, 'Google_Service_Dataproc_StartupConfig');
