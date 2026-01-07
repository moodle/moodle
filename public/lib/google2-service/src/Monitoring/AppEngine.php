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

namespace Google\Service\Monitoring;

class AppEngine extends \Google\Model
{
  /**
   * The ID of the App Engine module underlying this service. Corresponds to the
   * module_id resource label in the gae_app monitored resource
   * (https://cloud.google.com/monitoring/api/resources#tag_gae_app).
   *
   * @var string
   */
  public $moduleId;

  /**
   * The ID of the App Engine module underlying this service. Corresponds to the
   * module_id resource label in the gae_app monitored resource
   * (https://cloud.google.com/monitoring/api/resources#tag_gae_app).
   *
   * @param string $moduleId
   */
  public function setModuleId($moduleId)
  {
    $this->moduleId = $moduleId;
  }
  /**
   * @return string
   */
  public function getModuleId()
  {
    return $this->moduleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppEngine::class, 'Google_Service_Monitoring_AppEngine');
