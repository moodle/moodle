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

class GoogleMapsPlacesV1PlaceConsumerAlertDetails extends \Google\Model
{
  protected $aboutLinkType = GoogleMapsPlacesV1PlaceConsumerAlertDetailsLink::class;
  protected $aboutLinkDataType = '';
  /**
   * The description of the consumer alert message.
   *
   * @var string
   */
  public $description;
  /**
   * The title to show together with the description.
   *
   * @var string
   */
  public $title;

  /**
   * The link to show together with the description to provide more information.
   *
   * @param GoogleMapsPlacesV1PlaceConsumerAlertDetailsLink $aboutLink
   */
  public function setAboutLink(GoogleMapsPlacesV1PlaceConsumerAlertDetailsLink $aboutLink)
  {
    $this->aboutLink = $aboutLink;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceConsumerAlertDetailsLink
   */
  public function getAboutLink()
  {
    return $this->aboutLink;
  }
  /**
   * The description of the consumer alert message.
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
   * The title to show together with the description.
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
class_alias(GoogleMapsPlacesV1PlaceConsumerAlertDetails::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceConsumerAlertDetails');
