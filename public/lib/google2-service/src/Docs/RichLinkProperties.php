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

namespace Google\Service\Docs;

class RichLinkProperties extends \Google\Model
{
  /**
   * The [MIME type](https://developers.google.com/drive/api/v3/mime-types) of
   * the RichLink, if there's one (for example, when it's a file in Drive).
   *
   * @var string
   */
  public $mimeType;
  /**
   * The title of the RichLink as displayed in the link. This title matches the
   * title of the linked resource at the time of the insertion or last update of
   * the link. This field is always present.
   *
   * @var string
   */
  public $title;
  /**
   * The URI to the RichLink. This is always present.
   *
   * @var string
   */
  public $uri;

  /**
   * The [MIME type](https://developers.google.com/drive/api/v3/mime-types) of
   * the RichLink, if there's one (for example, when it's a file in Drive).
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The title of the RichLink as displayed in the link. This title matches the
   * title of the linked resource at the time of the insertion or last update of
   * the link. This field is always present.
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
   * The URI to the RichLink. This is always present.
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
class_alias(RichLinkProperties::class, 'Google_Service_Docs_RichLinkProperties');
