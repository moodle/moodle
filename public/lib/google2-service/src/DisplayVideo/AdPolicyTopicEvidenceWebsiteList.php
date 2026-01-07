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

class AdPolicyTopicEvidenceWebsiteList extends \Google\Collection
{
  protected $collection_key = 'websites';
  /**
   * Websites that caused the policy finding.
   *
   * @var string[]
   */
  public $websites;

  /**
   * Websites that caused the policy finding.
   *
   * @param string[] $websites
   */
  public function setWebsites($websites)
  {
    $this->websites = $websites;
  }
  /**
   * @return string[]
   */
  public function getWebsites()
  {
    return $this->websites;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEvidenceWebsiteList::class, 'Google_Service_DisplayVideo_AdPolicyTopicEvidenceWebsiteList');
