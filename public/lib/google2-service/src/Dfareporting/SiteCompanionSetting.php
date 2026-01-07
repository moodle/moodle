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

class SiteCompanionSetting extends \Google\Collection
{
  protected $collection_key = 'enabledSizes';
  /**
   * Whether companions are disabled for this site template.
   *
   * @var bool
   */
  public $companionsDisabled;
  protected $enabledSizesType = Size::class;
  protected $enabledSizesDataType = 'array';
  /**
   * Whether to serve only static images as companions.
   *
   * @var bool
   */
  public $imageOnly;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#siteCompanionSetting".
   *
   * @var string
   */
  public $kind;

  /**
   * Whether companions are disabled for this site template.
   *
   * @param bool $companionsDisabled
   */
  public function setCompanionsDisabled($companionsDisabled)
  {
    $this->companionsDisabled = $companionsDisabled;
  }
  /**
   * @return bool
   */
  public function getCompanionsDisabled()
  {
    return $this->companionsDisabled;
  }
  /**
   * Allowlist of companion sizes to be served via this site template. Set this
   * list to null or empty to serve all companion sizes.
   *
   * @param Size[] $enabledSizes
   */
  public function setEnabledSizes($enabledSizes)
  {
    $this->enabledSizes = $enabledSizes;
  }
  /**
   * @return Size[]
   */
  public function getEnabledSizes()
  {
    return $this->enabledSizes;
  }
  /**
   * Whether to serve only static images as companions.
   *
   * @param bool $imageOnly
   */
  public function setImageOnly($imageOnly)
  {
    $this->imageOnly = $imageOnly;
  }
  /**
   * @return bool
   */
  public function getImageOnly()
  {
    return $this->imageOnly;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#siteCompanionSetting".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SiteCompanionSetting::class, 'Google_Service_Dfareporting_SiteCompanionSetting');
