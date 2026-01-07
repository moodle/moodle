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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaImportErrorConfig extends \Google\Model
{
  /**
   * Cloud Storage prefix for import errors. This must be an empty, existing
   * Cloud Storage directory. Import errors are written to sharded files in this
   * directory, one per line, as a JSON-encoded `google.rpc.Status` message.
   *
   * @var string
   */
  public $gcsPrefix;

  /**
   * Cloud Storage prefix for import errors. This must be an empty, existing
   * Cloud Storage directory. Import errors are written to sharded files in this
   * directory, one per line, as a JSON-encoded `google.rpc.Status` message.
   *
   * @param string $gcsPrefix
   */
  public function setGcsPrefix($gcsPrefix)
  {
    $this->gcsPrefix = $gcsPrefix;
  }
  /**
   * @return string
   */
  public function getGcsPrefix()
  {
    return $this->gcsPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaImportErrorConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaImportErrorConfig');
