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

class RichMediaExitOverride extends \Google\Model
{
  protected $clickThroughUrlType = ClickThroughUrl::class;
  protected $clickThroughUrlDataType = '';
  /**
   * Whether to use the clickThroughUrl. If false, the creative-level exit will
   * be used.
   *
   * @var bool
   */
  public $enabled;
  /**
   * ID for the override to refer to a specific exit in the creative.
   *
   * @var string
   */
  public $exitId;

  /**
   * Click-through URL of this rich media exit override. Applicable if the
   * enabled field is set to true.
   *
   * @param ClickThroughUrl $clickThroughUrl
   */
  public function setClickThroughUrl(ClickThroughUrl $clickThroughUrl)
  {
    $this->clickThroughUrl = $clickThroughUrl;
  }
  /**
   * @return ClickThroughUrl
   */
  public function getClickThroughUrl()
  {
    return $this->clickThroughUrl;
  }
  /**
   * Whether to use the clickThroughUrl. If false, the creative-level exit will
   * be used.
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
   * ID for the override to refer to a specific exit in the creative.
   *
   * @param string $exitId
   */
  public function setExitId($exitId)
  {
    $this->exitId = $exitId;
  }
  /**
   * @return string
   */
  public function getExitId()
  {
    return $this->exitId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RichMediaExitOverride::class, 'Google_Service_Dfareporting_RichMediaExitOverride');
