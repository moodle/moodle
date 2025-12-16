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

namespace Google\Service\AndroidEnterprise;

class ManagedConfigurationsSettings extends \Google\Model
{
  /**
   * The last updated time of the managed configuration settings in milliseconds
   * since 1970-01-01T00:00:00Z.
   *
   * @var string
   */
  public $lastUpdatedTimestampMillis;
  /**
   * The ID of the managed configurations settings.
   *
   * @var string
   */
  public $mcmId;
  /**
   * The name of the managed configurations settings.
   *
   * @var string
   */
  public $name;

  /**
   * The last updated time of the managed configuration settings in milliseconds
   * since 1970-01-01T00:00:00Z.
   *
   * @param string $lastUpdatedTimestampMillis
   */
  public function setLastUpdatedTimestampMillis($lastUpdatedTimestampMillis)
  {
    $this->lastUpdatedTimestampMillis = $lastUpdatedTimestampMillis;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimestampMillis()
  {
    return $this->lastUpdatedTimestampMillis;
  }
  /**
   * The ID of the managed configurations settings.
   *
   * @param string $mcmId
   */
  public function setMcmId($mcmId)
  {
    $this->mcmId = $mcmId;
  }
  /**
   * @return string
   */
  public function getMcmId()
  {
    return $this->mcmId;
  }
  /**
   * The name of the managed configurations settings.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedConfigurationsSettings::class, 'Google_Service_AndroidEnterprise_ManagedConfigurationsSettings');
