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

class AdClientAdCode extends \Google\Model
{
  /**
   * Output only. The AdSense code snippet to add to the head of an HTML page.
   *
   * @var string
   */
  public $adCode;
  /**
   * Output only. The AdSense code snippet to add to the body of an AMP page.
   *
   * @var string
   */
  public $ampBody;
  /**
   * Output only. The AdSense code snippet to add to the head of an AMP page.
   *
   * @var string
   */
  public $ampHead;

  /**
   * Output only. The AdSense code snippet to add to the head of an HTML page.
   *
   * @param string $adCode
   */
  public function setAdCode($adCode)
  {
    $this->adCode = $adCode;
  }
  /**
   * @return string
   */
  public function getAdCode()
  {
    return $this->adCode;
  }
  /**
   * Output only. The AdSense code snippet to add to the body of an AMP page.
   *
   * @param string $ampBody
   */
  public function setAmpBody($ampBody)
  {
    $this->ampBody = $ampBody;
  }
  /**
   * @return string
   */
  public function getAmpBody()
  {
    return $this->ampBody;
  }
  /**
   * Output only. The AdSense code snippet to add to the head of an AMP page.
   *
   * @param string $ampHead
   */
  public function setAmpHead($ampHead)
  {
    $this->ampHead = $ampHead;
  }
  /**
   * @return string
   */
  public function getAmpHead()
  {
    return $this->ampHead;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdClientAdCode::class, 'Google_Service_Adsense_AdClientAdCode');
