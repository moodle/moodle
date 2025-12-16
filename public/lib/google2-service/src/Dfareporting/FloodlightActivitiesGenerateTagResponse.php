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

class FloodlightActivitiesGenerateTagResponse extends \Google\Model
{
  /**
   * Generated tag for this Floodlight activity. For Google tags, this is the
   * event snippet.
   *
   * @var string
   */
  public $floodlightActivityTag;
  /**
   * The global snippet section of a Google tag. The Google tag sets new cookies
   * on your domain, which will store a unique identifier for a user or the ad
   * click that brought the user to your site. Learn more.
   *
   * @var string
   */
  public $globalSiteTagGlobalSnippet;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivitiesGenerateTagResponse".
   *
   * @var string
   */
  public $kind;

  /**
   * Generated tag for this Floodlight activity. For Google tags, this is the
   * event snippet.
   *
   * @param string $floodlightActivityTag
   */
  public function setFloodlightActivityTag($floodlightActivityTag)
  {
    $this->floodlightActivityTag = $floodlightActivityTag;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityTag()
  {
    return $this->floodlightActivityTag;
  }
  /**
   * The global snippet section of a Google tag. The Google tag sets new cookies
   * on your domain, which will store a unique identifier for a user or the ad
   * click that brought the user to your site. Learn more.
   *
   * @param string $globalSiteTagGlobalSnippet
   */
  public function setGlobalSiteTagGlobalSnippet($globalSiteTagGlobalSnippet)
  {
    $this->globalSiteTagGlobalSnippet = $globalSiteTagGlobalSnippet;
  }
  /**
   * @return string
   */
  public function getGlobalSiteTagGlobalSnippet()
  {
    return $this->globalSiteTagGlobalSnippet;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivitiesGenerateTagResponse".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightActivitiesGenerateTagResponse::class, 'Google_Service_Dfareporting_FloodlightActivitiesGenerateTagResponse');
