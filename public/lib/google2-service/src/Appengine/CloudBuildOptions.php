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

namespace Google\Service\Appengine;

class CloudBuildOptions extends \Google\Model
{
  /**
   * Path to the yaml file used in deployment, used to determine runtime
   * configuration details.Required for flexible environment builds.See
   * https://cloud.google.com/appengine/docs/standard/python/config/appref for
   * more details.
   *
   * @var string
   */
  public $appYamlPath;
  /**
   * The Cloud Build timeout used as part of any dependent builds performed by
   * version creation. Defaults to 10 minutes.
   *
   * @var string
   */
  public $cloudBuildTimeout;

  /**
   * Path to the yaml file used in deployment, used to determine runtime
   * configuration details.Required for flexible environment builds.See
   * https://cloud.google.com/appengine/docs/standard/python/config/appref for
   * more details.
   *
   * @param string $appYamlPath
   */
  public function setAppYamlPath($appYamlPath)
  {
    $this->appYamlPath = $appYamlPath;
  }
  /**
   * @return string
   */
  public function getAppYamlPath()
  {
    return $this->appYamlPath;
  }
  /**
   * The Cloud Build timeout used as part of any dependent builds performed by
   * version creation. Defaults to 10 minutes.
   *
   * @param string $cloudBuildTimeout
   */
  public function setCloudBuildTimeout($cloudBuildTimeout)
  {
    $this->cloudBuildTimeout = $cloudBuildTimeout;
  }
  /**
   * @return string
   */
  public function getCloudBuildTimeout()
  {
    return $this->cloudBuildTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudBuildOptions::class, 'Google_Service_Appengine_CloudBuildOptions');
