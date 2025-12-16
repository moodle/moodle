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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1Review extends \Google\Model
{
  protected $authorAttributionType = GoogleMapsPlacesV1AuthorAttribution::class;
  protected $authorAttributionDataType = '';
  /**
   * A link where users can flag a problem with the review.
   *
   * @var string
   */
  public $flagContentUri;
  /**
   * A link to show the review on Google Maps.
   *
   * @var string
   */
  public $googleMapsUri;
  /**
   * A reference representing this place review which may be used to look up
   * this place review again (also called the API "resource" name:
   * `places/{place_id}/reviews/{review}`).
   *
   * @var string
   */
  public $name;
  protected $originalTextType = GoogleTypeLocalizedText::class;
  protected $originalTextDataType = '';
  /**
   * Timestamp for the review.
   *
   * @var string
   */
  public $publishTime;
  /**
   * A number between 1.0 and 5.0, also called the number of stars.
   *
   * @var 
   */
  public $rating;
  /**
   * A string of formatted recent time, expressing the review time relative to
   * the current time in a form appropriate for the language and country.
   *
   * @var string
   */
  public $relativePublishTimeDescription;
  protected $textType = GoogleTypeLocalizedText::class;
  protected $textDataType = '';
  protected $visitDateType = GoogleTypeDate::class;
  protected $visitDateDataType = '';

  /**
   * This review's author.
   *
   * @param GoogleMapsPlacesV1AuthorAttribution $authorAttribution
   */
  public function setAuthorAttribution(GoogleMapsPlacesV1AuthorAttribution $authorAttribution)
  {
    $this->authorAttribution = $authorAttribution;
  }
  /**
   * @return GoogleMapsPlacesV1AuthorAttribution
   */
  public function getAuthorAttribution()
  {
    return $this->authorAttribution;
  }
  /**
   * A link where users can flag a problem with the review.
   *
   * @param string $flagContentUri
   */
  public function setFlagContentUri($flagContentUri)
  {
    $this->flagContentUri = $flagContentUri;
  }
  /**
   * @return string
   */
  public function getFlagContentUri()
  {
    return $this->flagContentUri;
  }
  /**
   * A link to show the review on Google Maps.
   *
   * @param string $googleMapsUri
   */
  public function setGoogleMapsUri($googleMapsUri)
  {
    $this->googleMapsUri = $googleMapsUri;
  }
  /**
   * @return string
   */
  public function getGoogleMapsUri()
  {
    return $this->googleMapsUri;
  }
  /**
   * A reference representing this place review which may be used to look up
   * this place review again (also called the API "resource" name:
   * `places/{place_id}/reviews/{review}`).
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
   * The review text in its original language.
   *
   * @param GoogleTypeLocalizedText $originalText
   */
  public function setOriginalText(GoogleTypeLocalizedText $originalText)
  {
    $this->originalText = $originalText;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getOriginalText()
  {
    return $this->originalText;
  }
  /**
   * Timestamp for the review.
   *
   * @param string $publishTime
   */
  public function setPublishTime($publishTime)
  {
    $this->publishTime = $publishTime;
  }
  /**
   * @return string
   */
  public function getPublishTime()
  {
    return $this->publishTime;
  }
  public function setRating($rating)
  {
    $this->rating = $rating;
  }
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * A string of formatted recent time, expressing the review time relative to
   * the current time in a form appropriate for the language and country.
   *
   * @param string $relativePublishTimeDescription
   */
  public function setRelativePublishTimeDescription($relativePublishTimeDescription)
  {
    $this->relativePublishTimeDescription = $relativePublishTimeDescription;
  }
  /**
   * @return string
   */
  public function getRelativePublishTimeDescription()
  {
    return $this->relativePublishTimeDescription;
  }
  /**
   * The localized text of the review.
   *
   * @param GoogleTypeLocalizedText $text
   */
  public function setText(GoogleTypeLocalizedText $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The date when the author visited the place. This is truncated to the year
   * and month of the visit.
   *
   * @param GoogleTypeDate $visitDate
   */
  public function setVisitDate(GoogleTypeDate $visitDate)
  {
    $this->visitDate = $visitDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getVisitDate()
  {
    return $this->visitDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1Review::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1Review');
