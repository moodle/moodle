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

namespace Google\Service\Adsense;

class UrlChannel extends \Google\Model
{
  /**
   * Output only. Resource name of the URL channel. Format:
   * accounts/{account}/adclients/{adclient}/urlchannels/{urlchannel}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique ID of the custom channel as used in the
   * `URL_CHANNEL_ID` reporting dimension.
   *
   * @var string
   */
  public $reportingDimensionId;
  /**
   * URI pattern of the channel. Does not include "http://" or "https://".
   * Example: www.example.com/home
   *
   * @var string
   */
  public $uriPattern;

  /**
   * Output only. Resource name of the URL channel. Format:
   * accounts/{account}/adclients/{adclient}/urlchannels/{urlchannel}
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
   * Output only. Unique ID of the custom channel as used in the
   * `URL_CHANNEL_ID` reporting dimension.
   *
   * @param string $reportingDimensionId
   */
  public function setReportingDimensionId($reportingDimensionId)
  {
    $this->reportingDimensionId = $reportingDimensionId;
  }
  /**
   * @return string
   */
  public function getReportingDimensionId()
  {
    return $this->reportingDimensionId;
  }
  /**
   * URI pattern of the channel. Does not include "http://" or "https://".
   * Example: www.example.com/home
   *
   * @param string $uriPattern
   */
  public function setUriPattern($uriPattern)
  {
    $this->uriPattern = $uriPattern;
  }
  /**
   * @return string
   */
  public function getUriPattern()
  {
    return $this->uriPattern;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlChannel::class, 'Google_Service_Adsense_UrlChannel');
