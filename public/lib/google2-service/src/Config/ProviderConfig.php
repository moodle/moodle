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

namespace Google\Service\Config;

class ProviderConfig extends \Google\Model
{
  /**
   * Unspecified source type, default to public sources.
   */
  public const SOURCE_TYPE_PROVIDER_SOURCE_UNSPECIFIED = 'PROVIDER_SOURCE_UNSPECIFIED';
  /**
   * Service maintained provider source type.
   */
  public const SOURCE_TYPE_SERVICE_MAINTAINED = 'SERVICE_MAINTAINED';
  /**
   * Optional. ProviderSource specifies the source type of the provider.
   *
   * @var string
   */
  public $sourceType;

  /**
   * Optional. ProviderSource specifies the source type of the provider.
   *
   * Accepted values: PROVIDER_SOURCE_UNSPECIFIED, SERVICE_MAINTAINED
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProviderConfig::class, 'Google_Service_Config_ProviderConfig');
