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

namespace Google\Service\ChecksService;

class PrivacyPolicy extends \Google\Model
{
  /**
   * @var string
   */
  public $htmlContent;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $privacyPolicyUri;
  /**
   * @var float
   */
  public $score;
  /**
   * @var string
   */
  public $websiteUri;

  /**
   * @param string
   */
  public function setHtmlContent($htmlContent)
  {
    $this->htmlContent = $htmlContent;
  }
  /**
   * @return string
   */
  public function getHtmlContent()
  {
    return $this->htmlContent;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setPrivacyPolicyUri($privacyPolicyUri)
  {
    $this->privacyPolicyUri = $privacyPolicyUri;
  }
  /**
   * @return string
   */
  public function getPrivacyPolicyUri()
  {
    return $this->privacyPolicyUri;
  }
  /**
   * @param float
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * @param string
   */
  public function setWebsiteUri($websiteUri)
  {
    $this->websiteUri = $websiteUri;
  }
  /**
   * @return string
   */
  public function getWebsiteUri()
  {
    return $this->websiteUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivacyPolicy::class, 'Google_Service_ChecksService_PrivacyPolicy');
