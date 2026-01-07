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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1EnrichmentConfig extends \Google\Model
{
  /**
   * Optional. Append ACL to the event.
   *
   * @var bool
   */
  public $appendAcl;

  /**
   * Optional. Append ACL to the event.
   *
   * @param bool $appendAcl
   */
  public function setAppendAcl($appendAcl)
  {
    $this->appendAcl = $appendAcl;
  }
  /**
   * @return bool
   */
  public function getAppendAcl()
  {
    return $this->appendAcl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1EnrichmentConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1EnrichmentConfig');
