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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1DimensionConversationalAgentsPlaybookDimensionMetadata extends \Google\Model
{
  /**
   * Optional. The dialogflow playbook display name.
   *
   * @var string
   */
  public $playbookDisplayName;
  /**
   * Optional. The dialogflow playbook ID.
   *
   * @var string
   */
  public $playbookId;

  /**
   * Optional. The dialogflow playbook display name.
   *
   * @param string $playbookDisplayName
   */
  public function setPlaybookDisplayName($playbookDisplayName)
  {
    $this->playbookDisplayName = $playbookDisplayName;
  }
  /**
   * @return string
   */
  public function getPlaybookDisplayName()
  {
    return $this->playbookDisplayName;
  }
  /**
   * Optional. The dialogflow playbook ID.
   *
   * @param string $playbookId
   */
  public function setPlaybookId($playbookId)
  {
    $this->playbookId = $playbookId;
  }
  /**
   * @return string
   */
  public function getPlaybookId()
  {
    return $this->playbookId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1DimensionConversationalAgentsPlaybookDimensionMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1DimensionConversationalAgentsPlaybookDimensionMetadata');
