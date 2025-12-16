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

namespace Google\Service\DisplayVideo;

class ObaIcon extends \Google\Model
{
  /**
   * The OBA icon position is not specified.
   */
  public const POSITION_OBA_ICON_POSITION_UNSPECIFIED = 'OBA_ICON_POSITION_UNSPECIFIED';
  /**
   * At the upper right side of the creative.
   */
  public const POSITION_OBA_ICON_POSITION_UPPER_RIGHT = 'OBA_ICON_POSITION_UPPER_RIGHT';
  /**
   * At the upper left side of the creative.
   */
  public const POSITION_OBA_ICON_POSITION_UPPER_LEFT = 'OBA_ICON_POSITION_UPPER_LEFT';
  /**
   * At the lower right side of the creative.
   */
  public const POSITION_OBA_ICON_POSITION_LOWER_RIGHT = 'OBA_ICON_POSITION_LOWER_RIGHT';
  /**
   * At the lower left side of the creative.
   */
  public const POSITION_OBA_ICON_POSITION_LOWER_LEFT = 'OBA_ICON_POSITION_LOWER_LEFT';
  /**
   * Required. The click tracking URL of the OBA icon. Only URLs of the
   * following domains are allowed: * `https://info.evidon.com` *
   * `https://l.betrad.com`
   *
   * @var string
   */
  public $clickTrackingUrl;
  protected $dimensionsType = Dimensions::class;
  protected $dimensionsDataType = '';
  /**
   * Required. The landing page URL of the OBA icon. Only URLs of the following
   * domains are allowed: * `https://info.evidon.com` * `https://l.betrad.com`
   *
   * @var string
   */
  public $landingPageUrl;
  /**
   * Optional. The position of the OBA icon on the creative.
   *
   * @var string
   */
  public $position;
  /**
   * Optional. The program of the OBA icon. For example: “AdChoices”.
   *
   * @var string
   */
  public $program;
  /**
   * Optional. The MIME type of the OBA icon resource.
   *
   * @var string
   */
  public $resourceMimeType;
  /**
   * Optional. The URL of the OBA icon resource.
   *
   * @var string
   */
  public $resourceUrl;
  /**
   * Required. The view tracking URL of the OBA icon. Only URLs of the following
   * domains are allowed: * `https://info.evidon.com` * `https://l.betrad.com`
   *
   * @var string
   */
  public $viewTrackingUrl;

  /**
   * Required. The click tracking URL of the OBA icon. Only URLs of the
   * following domains are allowed: * `https://info.evidon.com` *
   * `https://l.betrad.com`
   *
   * @param string $clickTrackingUrl
   */
  public function setClickTrackingUrl($clickTrackingUrl)
  {
    $this->clickTrackingUrl = $clickTrackingUrl;
  }
  /**
   * @return string
   */
  public function getClickTrackingUrl()
  {
    return $this->clickTrackingUrl;
  }
  /**
   * Optional. The dimensions of the OBA icon.
   *
   * @param Dimensions $dimensions
   */
  public function setDimensions(Dimensions $dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return Dimensions
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Required. The landing page URL of the OBA icon. Only URLs of the following
   * domains are allowed: * `https://info.evidon.com` * `https://l.betrad.com`
   *
   * @param string $landingPageUrl
   */
  public function setLandingPageUrl($landingPageUrl)
  {
    $this->landingPageUrl = $landingPageUrl;
  }
  /**
   * @return string
   */
  public function getLandingPageUrl()
  {
    return $this->landingPageUrl;
  }
  /**
   * Optional. The position of the OBA icon on the creative.
   *
   * Accepted values: OBA_ICON_POSITION_UNSPECIFIED,
   * OBA_ICON_POSITION_UPPER_RIGHT, OBA_ICON_POSITION_UPPER_LEFT,
   * OBA_ICON_POSITION_LOWER_RIGHT, OBA_ICON_POSITION_LOWER_LEFT
   *
   * @param self::POSITION_* $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }
  /**
   * @return self::POSITION_*
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Optional. The program of the OBA icon. For example: “AdChoices”.
   *
   * @param string $program
   */
  public function setProgram($program)
  {
    $this->program = $program;
  }
  /**
   * @return string
   */
  public function getProgram()
  {
    return $this->program;
  }
  /**
   * Optional. The MIME type of the OBA icon resource.
   *
   * @param string $resourceMimeType
   */
  public function setResourceMimeType($resourceMimeType)
  {
    $this->resourceMimeType = $resourceMimeType;
  }
  /**
   * @return string
   */
  public function getResourceMimeType()
  {
    return $this->resourceMimeType;
  }
  /**
   * Optional. The URL of the OBA icon resource.
   *
   * @param string $resourceUrl
   */
  public function setResourceUrl($resourceUrl)
  {
    $this->resourceUrl = $resourceUrl;
  }
  /**
   * @return string
   */
  public function getResourceUrl()
  {
    return $this->resourceUrl;
  }
  /**
   * Required. The view tracking URL of the OBA icon. Only URLs of the following
   * domains are allowed: * `https://info.evidon.com` * `https://l.betrad.com`
   *
   * @param string $viewTrackingUrl
   */
  public function setViewTrackingUrl($viewTrackingUrl)
  {
    $this->viewTrackingUrl = $viewTrackingUrl;
  }
  /**
   * @return string
   */
  public function getViewTrackingUrl()
  {
    return $this->viewTrackingUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObaIcon::class, 'Google_Service_DisplayVideo_ObaIcon');
