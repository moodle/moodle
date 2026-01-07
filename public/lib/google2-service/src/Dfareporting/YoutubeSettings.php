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

namespace Google\Service\Dfareporting;

class YoutubeSettings extends \Google\Collection
{
  protected $collection_key = 'longHeadlines';
  /**
   * Optional. The IDs of the creatives to use for the business logo. Currently
   * only one creative is supported.
   *
   * @var string[]
   */
  public $businessLogoCreativeIds;
  /**
   * Optional. The business name.
   *
   * @var string
   */
  public $businessName;
  /**
   * Optional. The call to actions. Currently only one call to action is
   * supported.
   *
   * @var string[]
   */
  public $callToActions;
  /**
   * Optional. The descriptions. Currently only one description is supported.
   *
   * @var string[]
   */
  public $descriptions;
  /**
   * Optional. The headlines associated with the call to actions. Currently only
   * one headline is supported.
   *
   * @var string[]
   */
  public $headlines;
  /**
   * Optional. The long headlines. Currently only one long headline is
   * supported.
   *
   * @var string[]
   */
  public $longHeadlines;

  /**
   * Optional. The IDs of the creatives to use for the business logo. Currently
   * only one creative is supported.
   *
   * @param string[] $businessLogoCreativeIds
   */
  public function setBusinessLogoCreativeIds($businessLogoCreativeIds)
  {
    $this->businessLogoCreativeIds = $businessLogoCreativeIds;
  }
  /**
   * @return string[]
   */
  public function getBusinessLogoCreativeIds()
  {
    return $this->businessLogoCreativeIds;
  }
  /**
   * Optional. The business name.
   *
   * @param string $businessName
   */
  public function setBusinessName($businessName)
  {
    $this->businessName = $businessName;
  }
  /**
   * @return string
   */
  public function getBusinessName()
  {
    return $this->businessName;
  }
  /**
   * Optional. The call to actions. Currently only one call to action is
   * supported.
   *
   * @param string[] $callToActions
   */
  public function setCallToActions($callToActions)
  {
    $this->callToActions = $callToActions;
  }
  /**
   * @return string[]
   */
  public function getCallToActions()
  {
    return $this->callToActions;
  }
  /**
   * Optional. The descriptions. Currently only one description is supported.
   *
   * @param string[] $descriptions
   */
  public function setDescriptions($descriptions)
  {
    $this->descriptions = $descriptions;
  }
  /**
   * @return string[]
   */
  public function getDescriptions()
  {
    return $this->descriptions;
  }
  /**
   * Optional. The headlines associated with the call to actions. Currently only
   * one headline is supported.
   *
   * @param string[] $headlines
   */
  public function setHeadlines($headlines)
  {
    $this->headlines = $headlines;
  }
  /**
   * @return string[]
   */
  public function getHeadlines()
  {
    return $this->headlines;
  }
  /**
   * Optional. The long headlines. Currently only one long headline is
   * supported.
   *
   * @param string[] $longHeadlines
   */
  public function setLongHeadlines($longHeadlines)
  {
    $this->longHeadlines = $longHeadlines;
  }
  /**
   * @return string[]
   */
  public function getLongHeadlines()
  {
    return $this->longHeadlines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeSettings::class, 'Google_Service_Dfareporting_YoutubeSettings');
