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

namespace Google\Service\AndroidPublisher;

class Listing extends \Google\Model
{
  /**
   * Full description of the app.
   *
   * @var string
   */
  public $fullDescription;
  /**
   * Language localization code (a BCP-47 language tag; for example, "de-AT" for
   * Austrian German).
   *
   * @var string
   */
  public $language;
  /**
   * Short description of the app.
   *
   * @var string
   */
  public $shortDescription;
  /**
   * Localized title of the app.
   *
   * @var string
   */
  public $title;
  /**
   * URL of a promotional YouTube video for the app.
   *
   * @var string
   */
  public $video;

  /**
   * Full description of the app.
   *
   * @param string $fullDescription
   */
  public function setFullDescription($fullDescription)
  {
    $this->fullDescription = $fullDescription;
  }
  /**
   * @return string
   */
  public function getFullDescription()
  {
    return $this->fullDescription;
  }
  /**
   * Language localization code (a BCP-47 language tag; for example, "de-AT" for
   * Austrian German).
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Short description of the app.
   *
   * @param string $shortDescription
   */
  public function setShortDescription($shortDescription)
  {
    $this->shortDescription = $shortDescription;
  }
  /**
   * @return string
   */
  public function getShortDescription()
  {
    return $this->shortDescription;
  }
  /**
   * Localized title of the app.
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
   * URL of a promotional YouTube video for the app.
   *
   * @param string $video
   */
  public function setVideo($video)
  {
    $this->video = $video;
  }
  /**
   * @return string
   */
  public function getVideo()
  {
    return $this->video;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Listing::class, 'Google_Service_AndroidPublisher_Listing');
