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

class AdPolicyTopicEvidenceTextList extends \Google\Collection
{
  protected $collection_key = 'texts';
  /**
   * The fragments of text from the resource that caused the policy finding.
   *
   * @var string[]
   */
  public $texts;

  /**
   * The fragments of text from the resource that caused the policy finding.
   *
   * @param string[] $texts
   */
  public function setTexts($texts)
  {
    $this->texts = $texts;
  }
  /**
   * @return string[]
   */
  public function getTexts()
  {
    return $this->texts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEvidenceTextList::class, 'Google_Service_DisplayVideo_AdPolicyTopicEvidenceTextList');
