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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesDynamicSearchAdsSearchTermView extends \Google\Model
{
  /**
   * Output only. The dynamically selected landing page URL of the impression.
   * This field is read-only.
   *
   * @var string
   */
  public $landingPage;
  /**
   * Output only. The resource name of the dynamic search ads search term view.
   * Dynamic search ads search term view resource names have the form: `customer
   * s/{customer_id}/dynamicSearchAdsSearchTermViews/{ad_group_id}~{search_term_
   * fingerprint}~{headline_fingerprint}~{landing_page_fingerprint}~{page_url_fi
   * ngerprint}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * Output only. The dynamically selected landing page URL of the impression.
   * This field is read-only.
   *
   * @param string $landingPage
   */
  public function setLandingPage($landingPage)
  {
    $this->landingPage = $landingPage;
  }
  /**
   * @return string
   */
  public function getLandingPage()
  {
    return $this->landingPage;
  }
  /**
   * Output only. The resource name of the dynamic search ads search term view.
   * Dynamic search ads search term view resource names have the form: `customer
   * s/{customer_id}/dynamicSearchAdsSearchTermViews/{ad_group_id}~{search_term_
   * fingerprint}~{headline_fingerprint}~{landing_page_fingerprint}~{page_url_fi
   * ngerprint}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesDynamicSearchAdsSearchTermView::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesDynamicSearchAdsSearchTermView');
