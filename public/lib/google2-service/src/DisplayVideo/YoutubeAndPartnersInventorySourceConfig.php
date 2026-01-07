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

namespace Google\Service\DisplayVideo;

class YoutubeAndPartnersInventorySourceConfig extends \Google\Model
{
  /**
   * Optional. Whether to target inventory in video apps available with Google
   * TV.
   *
   * @var bool
   */
  public $includeGoogleTv;
  /**
   * Optional. Whether to target inventory on YouTube. This includes both
   * search, channels and videos.
   *
   * @var bool
   */
  public $includeYoutube;
  /**
   * Whether to target inventory on a collection of partner sites and apps that
   * follow the same brand safety standards as YouTube.
   *
   * @var bool
   */
  public $includeYoutubeVideoPartners;

  /**
   * Optional. Whether to target inventory in video apps available with Google
   * TV.
   *
   * @param bool $includeGoogleTv
   */
  public function setIncludeGoogleTv($includeGoogleTv)
  {
    $this->includeGoogleTv = $includeGoogleTv;
  }
  /**
   * @return bool
   */
  public function getIncludeGoogleTv()
  {
    return $this->includeGoogleTv;
  }
  /**
   * Optional. Whether to target inventory on YouTube. This includes both
   * search, channels and videos.
   *
   * @param bool $includeYoutube
   */
  public function setIncludeYoutube($includeYoutube)
  {
    $this->includeYoutube = $includeYoutube;
  }
  /**
   * @return bool
   */
  public function getIncludeYoutube()
  {
    return $this->includeYoutube;
  }
  /**
   * Whether to target inventory on a collection of partner sites and apps that
   * follow the same brand safety standards as YouTube.
   *
   * @param bool $includeYoutubeVideoPartners
   */
  public function setIncludeYoutubeVideoPartners($includeYoutubeVideoPartners)
  {
    $this->includeYoutubeVideoPartners = $includeYoutubeVideoPartners;
  }
  /**
   * @return bool
   */
  public function getIncludeYoutubeVideoPartners()
  {
    return $this->includeYoutubeVideoPartners;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAndPartnersInventorySourceConfig::class, 'Google_Service_DisplayVideo_YoutubeAndPartnersInventorySourceConfig');
