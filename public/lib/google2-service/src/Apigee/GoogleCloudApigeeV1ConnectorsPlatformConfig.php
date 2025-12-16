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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ConnectorsPlatformConfig extends \Google\Model
{
  /**
   * Flag that specifies whether the Connectors Platform add-on is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Output only. Time at which the Connectors Platform add-on expires in
   * milliseconds since epoch. If unspecified, the add-on will never expire.
   *
   * @var string
   */
  public $expiresAt;

  /**
   * Flag that specifies whether the Connectors Platform add-on is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Output only. Time at which the Connectors Platform add-on expires in
   * milliseconds since epoch. If unspecified, the add-on will never expire.
   *
   * @param string $expiresAt
   */
  public function setExpiresAt($expiresAt)
  {
    $this->expiresAt = $expiresAt;
  }
  /**
   * @return string
   */
  public function getExpiresAt()
  {
    return $this->expiresAt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ConnectorsPlatformConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ConnectorsPlatformConfig');
