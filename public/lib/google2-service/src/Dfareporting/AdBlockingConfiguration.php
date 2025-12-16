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

namespace Google\Service\Dfareporting;

class AdBlockingConfiguration extends \Google\Model
{
  /**
   * Whether this campaign has enabled ad blocking. When true, ad blocking is
   * enabled for placements in the campaign, but this may be overridden by site
   * and placement settings. When false, ad blocking is disabled for all
   * placements under the campaign, regardless of site and placement settings.
   *
   * @var bool
   */
  public $enabled;

  /**
   * Whether this campaign has enabled ad blocking. When true, ad blocking is
   * enabled for placements in the campaign, but this may be overridden by site
   * and placement settings. When false, ad blocking is disabled for all
   * placements under the campaign, regardless of site and placement settings.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdBlockingConfiguration::class, 'Google_Service_Dfareporting_AdBlockingConfiguration');
