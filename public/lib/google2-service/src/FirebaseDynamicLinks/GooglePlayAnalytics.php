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

namespace Google\Service\FirebaseDynamicLinks;

class GooglePlayAnalytics extends \Google\Model
{
  /**
   * Deprecated; FDL SDK does not process nor log it.
   *
   * @deprecated
   * @var string
   */
  public $gclid;
  /**
   * Campaign name; used for keyword analysis to identify a specific product
   * promotion or strategic campaign.
   *
   * @var string
   */
  public $utmCampaign;
  /**
   * Campaign content; used for A/B testing and content-targeted ads to
   * differentiate ads or links that point to the same URL.
   *
   * @var string
   */
  public $utmContent;
  /**
   * Campaign medium; used to identify a medium such as email or cost-per-click.
   *
   * @var string
   */
  public $utmMedium;
  /**
   * Campaign source; used to identify a search engine, newsletter, or other
   * source.
   *
   * @var string
   */
  public $utmSource;
  /**
   * Campaign term; used with paid search to supply the keywords for ads.
   *
   * @var string
   */
  public $utmTerm;

  /**
   * Deprecated; FDL SDK does not process nor log it.
   *
   * @deprecated
   * @param string $gclid
   */
  public function setGclid($gclid)
  {
    $this->gclid = $gclid;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGclid()
  {
    return $this->gclid;
  }
  /**
   * Campaign name; used for keyword analysis to identify a specific product
   * promotion or strategic campaign.
   *
   * @param string $utmCampaign
   */
  public function setUtmCampaign($utmCampaign)
  {
    $this->utmCampaign = $utmCampaign;
  }
  /**
   * @return string
   */
  public function getUtmCampaign()
  {
    return $this->utmCampaign;
  }
  /**
   * Campaign content; used for A/B testing and content-targeted ads to
   * differentiate ads or links that point to the same URL.
   *
   * @param string $utmContent
   */
  public function setUtmContent($utmContent)
  {
    $this->utmContent = $utmContent;
  }
  /**
   * @return string
   */
  public function getUtmContent()
  {
    return $this->utmContent;
  }
  /**
   * Campaign medium; used to identify a medium such as email or cost-per-click.
   *
   * @param string $utmMedium
   */
  public function setUtmMedium($utmMedium)
  {
    $this->utmMedium = $utmMedium;
  }
  /**
   * @return string
   */
  public function getUtmMedium()
  {
    return $this->utmMedium;
  }
  /**
   * Campaign source; used to identify a search engine, newsletter, or other
   * source.
   *
   * @param string $utmSource
   */
  public function setUtmSource($utmSource)
  {
    $this->utmSource = $utmSource;
  }
  /**
   * @return string
   */
  public function getUtmSource()
  {
    return $this->utmSource;
  }
  /**
   * Campaign term; used with paid search to supply the keywords for ads.
   *
   * @param string $utmTerm
   */
  public function setUtmTerm($utmTerm)
  {
    $this->utmTerm = $utmTerm;
  }
  /**
   * @return string
   */
  public function getUtmTerm()
  {
    return $this->utmTerm;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayAnalytics::class, 'Google_Service_FirebaseDynamicLinks_GooglePlayAnalytics');
