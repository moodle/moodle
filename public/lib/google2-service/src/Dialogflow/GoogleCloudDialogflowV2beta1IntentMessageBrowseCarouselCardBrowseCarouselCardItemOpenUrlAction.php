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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCardBrowseCarouselCardItemOpenUrlAction extends \Google\Model
{
  /**
   * Unspecified
   */
  public const URL_TYPE_HINT_URL_TYPE_HINT_UNSPECIFIED = 'URL_TYPE_HINT_UNSPECIFIED';
  /**
   * Url would be an amp action
   */
  public const URL_TYPE_HINT_AMP_ACTION = 'AMP_ACTION';
  /**
   * URL that points directly to AMP content, or to a canonical URL which refers
   * to AMP content via .
   */
  public const URL_TYPE_HINT_AMP_CONTENT = 'AMP_CONTENT';
  /**
   * Required. URL
   *
   * @var string
   */
  public $url;
  /**
   * Optional. Specifies the type of viewer that is used when opening the URL.
   * Defaults to opening via web browser.
   *
   * @var string
   */
  public $urlTypeHint;

  /**
   * Required. URL
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * Optional. Specifies the type of viewer that is used when opening the URL.
   * Defaults to opening via web browser.
   *
   * Accepted values: URL_TYPE_HINT_UNSPECIFIED, AMP_ACTION, AMP_CONTENT
   *
   * @param self::URL_TYPE_HINT_* $urlTypeHint
   */
  public function setUrlTypeHint($urlTypeHint)
  {
    $this->urlTypeHint = $urlTypeHint;
  }
  /**
   * @return self::URL_TYPE_HINT_*
   */
  public function getUrlTypeHint()
  {
    return $this->urlTypeHint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCardBrowseCarouselCardItemOpenUrlAction::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCardBrowseCarouselCardItemOpenUrlAction');
