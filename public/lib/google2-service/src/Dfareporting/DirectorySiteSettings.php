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

class DirectorySiteSettings extends \Google\Model
{
  /**
   * Whether this directory site has disabled active view creatives.
   *
   * @var bool
   */
  public $activeViewOptOut;
  protected $dfpSettingsType = DfpSettings::class;
  protected $dfpSettingsDataType = '';
  /**
   * Whether this site accepts in-stream video ads.
   *
   * @var bool
   */
  public $instreamVideoPlacementAccepted;
  /**
   * Whether this site accepts interstitial ads.
   *
   * @var bool
   */
  public $interstitialPlacementAccepted;

  /**
   * Whether this directory site has disabled active view creatives.
   *
   * @param bool $activeViewOptOut
   */
  public function setActiveViewOptOut($activeViewOptOut)
  {
    $this->activeViewOptOut = $activeViewOptOut;
  }
  /**
   * @return bool
   */
  public function getActiveViewOptOut()
  {
    return $this->activeViewOptOut;
  }
  /**
   * Directory site Ad Manager settings.
   *
   * @param DfpSettings $dfpSettings
   */
  public function setDfpSettings(DfpSettings $dfpSettings)
  {
    $this->dfpSettings = $dfpSettings;
  }
  /**
   * @return DfpSettings
   */
  public function getDfpSettings()
  {
    return $this->dfpSettings;
  }
  /**
   * Whether this site accepts in-stream video ads.
   *
   * @param bool $instreamVideoPlacementAccepted
   */
  public function setInstreamVideoPlacementAccepted($instreamVideoPlacementAccepted)
  {
    $this->instreamVideoPlacementAccepted = $instreamVideoPlacementAccepted;
  }
  /**
   * @return bool
   */
  public function getInstreamVideoPlacementAccepted()
  {
    return $this->instreamVideoPlacementAccepted;
  }
  /**
   * Whether this site accepts interstitial ads.
   *
   * @param bool $interstitialPlacementAccepted
   */
  public function setInterstitialPlacementAccepted($interstitialPlacementAccepted)
  {
    $this->interstitialPlacementAccepted = $interstitialPlacementAccepted;
  }
  /**
   * @return bool
   */
  public function getInterstitialPlacementAccepted()
  {
    return $this->interstitialPlacementAccepted;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectorySiteSettings::class, 'Google_Service_Dfareporting_DirectorySiteSettings');
