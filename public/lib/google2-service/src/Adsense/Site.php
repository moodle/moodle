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

namespace Google\Service\Adsense;

class Site extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Either: * The site hasn't been checked yet. * The site is inactive and
   * needs another review before it can show ads again. Learn how to [request a
   * review for an inactive
   * site](https://support.google.com/adsense/answer/9393996).
   */
  public const STATE_REQUIRES_REVIEW = 'REQUIRES_REVIEW';
  /**
   * Google is running some checks on the site. This usually takes a few days,
   * but in some cases it can take two to four weeks.
   */
  public const STATE_GETTING_READY = 'GETTING_READY';
  /**
   * The site is ready to show ads. Learn how to [set up ads on the
   * site](https://support.google.com/adsense/answer/7037624).
   */
  public const STATE_READY = 'READY';
  /**
   * Publisher needs to fix some issues before the site is ready to show ads.
   * Learn what to do [if a new site isn't
   * ready](https://support.google.com/adsense/answer/9061852).
   */
  public const STATE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * Whether auto ads is turned on for the site.
   *
   * @var bool
   */
  public $autoAdsEnabled;
  /**
   * Domain (or subdomain) of the site, e.g. "example.com" or "www.example.com".
   * This is used in the `OWNED_SITE_DOMAIN_NAME` reporting dimension.
   *
   * @var string
   */
  public $domain;
  /**
   * Output only. Resource name of a site. Format:
   * accounts/{account}/sites/{site}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique ID of the site as used in the `OWNED_SITE_ID` reporting
   * dimension.
   *
   * @var string
   */
  public $reportingDimensionId;
  /**
   * Output only. State of a site.
   *
   * @var string
   */
  public $state;

  /**
   * Whether auto ads is turned on for the site.
   *
   * @param bool $autoAdsEnabled
   */
  public function setAutoAdsEnabled($autoAdsEnabled)
  {
    $this->autoAdsEnabled = $autoAdsEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoAdsEnabled()
  {
    return $this->autoAdsEnabled;
  }
  /**
   * Domain (or subdomain) of the site, e.g. "example.com" or "www.example.com".
   * This is used in the `OWNED_SITE_DOMAIN_NAME` reporting dimension.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Output only. Resource name of a site. Format:
   * accounts/{account}/sites/{site}
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
  /**
   * Output only. Unique ID of the site as used in the `OWNED_SITE_ID` reporting
   * dimension.
   *
   * @param string $reportingDimensionId
   */
  public function setReportingDimensionId($reportingDimensionId)
  {
    $this->reportingDimensionId = $reportingDimensionId;
  }
  /**
   * @return string
   */
  public function getReportingDimensionId()
  {
    return $this->reportingDimensionId;
  }
  /**
   * Output only. State of a site.
   *
   * Accepted values: STATE_UNSPECIFIED, REQUIRES_REVIEW, GETTING_READY, READY,
   * NEEDS_ATTENTION
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Site::class, 'Google_Service_Adsense_Site');
