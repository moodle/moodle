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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchLinkPromotion extends \Google\Model
{
  /**
   * Optional. The Promotion description. Maximum length: 200 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The Document the user wants to promote. For site search, leave
   * unset and only populate uri. Can be set along with uri.
   *
   * @var string
   */
  public $document;
  /**
   * Optional. The enabled promotion will be returned for any serving configs
   * associated with the parent of the control this promotion is attached to.
   * This flag is used for basic site search only.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. The promotion thumbnail image url.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Required. The title of the promotion. Maximum length: 160 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Optional. The URL for the page the user wants to promote. Must be set for
   * site search. For other verticals, this is optional.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. The Promotion description. Maximum length: 200 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. The Document the user wants to promote. For site search, leave
   * unset and only populate uri. Can be set along with uri.
   *
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Optional. The enabled promotion will be returned for any serving configs
   * associated with the parent of the control this promotion is attached to.
   * This flag is used for basic site search only.
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
  /**
   * Optional. The promotion thumbnail image url.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Required. The title of the promotion. Maximum length: 160 characters.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Optional. The URL for the page the user wants to promote. Must be set for
   * site search. For other verticals, this is optional.
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
class_alias(GoogleCloudDiscoveryengineV1SearchLinkPromotion::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchLinkPromotion');
