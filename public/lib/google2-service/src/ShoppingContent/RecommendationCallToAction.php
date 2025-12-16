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

namespace Google\Service\ShoppingContent;

class RecommendationCallToAction extends \Google\Model
{
  /**
   * Output only. Intent of the action. This value describes the intent (for
   * example, `OPEN_CREATE_EMAIL_CAMPAIGN_FLOW`) and can vary from
   * recommendation to recommendation. This value can change over time for the
   * same recommendation. Currently available intent values: -
   * OPEN_CREATE_EMAIL_CAMPAIGN_FLOW: Opens a user journey where they can create
   * a marketing email campaign. (No default URL) - OPEN_CREATE_COLLECTION_TAB:
   * Opens a user journey where they can [create a
   * collection](https://support.google.com/merchants/answer/9703228) for their
   * Merchant account. (No default URL)
   *
   * @var string
   */
  public $intent;
  /**
   * Output only. Localized text of the CTA. Optional.
   *
   * @var string
   */
  public $localizedText;
  /**
   * Optional. URL of the CTA. This field will only be set for some
   * recommendations where there is a suggested landing URL. Otherwise it will
   * be set to an empty string. We recommend developers to use their own custom
   * landing page according to the description of the intent field above when
   * this uri field is empty.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. Intent of the action. This value describes the intent (for
   * example, `OPEN_CREATE_EMAIL_CAMPAIGN_FLOW`) and can vary from
   * recommendation to recommendation. This value can change over time for the
   * same recommendation. Currently available intent values: -
   * OPEN_CREATE_EMAIL_CAMPAIGN_FLOW: Opens a user journey where they can create
   * a marketing email campaign. (No default URL) - OPEN_CREATE_COLLECTION_TAB:
   * Opens a user journey where they can [create a
   * collection](https://support.google.com/merchants/answer/9703228) for their
   * Merchant account. (No default URL)
   *
   * @param string $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return string
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Output only. Localized text of the CTA. Optional.
   *
   * @param string $localizedText
   */
  public function setLocalizedText($localizedText)
  {
    $this->localizedText = $localizedText;
  }
  /**
   * @return string
   */
  public function getLocalizedText()
  {
    return $this->localizedText;
  }
  /**
   * Optional. URL of the CTA. This field will only be set for some
   * recommendations where there is a suggested landing URL. Otherwise it will
   * be set to an empty string. We recommend developers to use their own custom
   * landing page according to the description of the intent field above when
   * this uri field is empty.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecommendationCallToAction::class, 'Google_Service_ShoppingContent_RecommendationCallToAction');
