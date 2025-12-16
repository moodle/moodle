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

namespace Google\Service\YouTube;

class ChannelSnippet extends \Google\Model
{
  /**
   * The country of the channel.
   *
   * @var string
   */
  public $country;
  /**
   * The custom url of the channel.
   *
   * @var string
   */
  public $customUrl;
  /**
   * The language of the channel's default title and description.
   *
   * @var string
   */
  public $defaultLanguage;
  /**
   * The description of the channel.
   *
   * @var string
   */
  public $description;
  protected $localizedType = ChannelLocalization::class;
  protected $localizedDataType = '';
  /**
   * The date and time that the channel was created.
   *
   * @var string
   */
  public $publishedAt;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The channel's title.
   *
   * @var string
   */
  public $title;

  /**
   * The country of the channel.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The custom url of the channel.
   *
   * @param string $customUrl
   */
  public function setCustomUrl($customUrl)
  {
    $this->customUrl = $customUrl;
  }
  /**
   * @return string
   */
  public function getCustomUrl()
  {
    return $this->customUrl;
  }
  /**
   * The language of the channel's default title and description.
   *
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * The description of the channel.
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
   * Localized title and description, read-only.
   *
   * @param ChannelLocalization $localized
   */
  public function setLocalized(ChannelLocalization $localized)
  {
    $this->localized = $localized;
  }
  /**
   * @return ChannelLocalization
   */
  public function getLocalized()
  {
    return $this->localized;
  }
  /**
   * The date and time that the channel was created.
   *
   * @param string $publishedAt
   */
  public function setPublishedAt($publishedAt)
  {
    $this->publishedAt = $publishedAt;
  }
  /**
   * @return string
   */
  public function getPublishedAt()
  {
    return $this->publishedAt;
  }
  /**
   * A map of thumbnail images associated with the channel. For each object in
   * the map, the key is the name of the thumbnail image, and the value is an
   * object that contains other information about the thumbnail. When displaying
   * thumbnails in your application, make sure that your code uses the image
   * URLs exactly as they are returned in API responses. For example, your
   * application should not use the http domain instead of the https domain in a
   * URL returned in an API response. Beginning in July 2018, channel thumbnail
   * URLs will only be available in the https domain, which is how the URLs
   * appear in API responses. After that time, you might see broken images in
   * your application if it tries to load YouTube images from the http domain.
   * Thumbnail images might be empty for newly created channels and might take
   * up to one day to populate.
   *
   * @param ThumbnailDetails $thumbnails
   */
  public function setThumbnails(ThumbnailDetails $thumbnails)
  {
    $this->thumbnails = $thumbnails;
  }
  /**
   * @return ThumbnailDetails
   */
  public function getThumbnails()
  {
    return $this->thumbnails;
  }
  /**
   * The channel's title.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelSnippet::class, 'Google_Service_YouTube_ChannelSnippet');
