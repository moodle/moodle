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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic extends \Google\Model
{
  protected $customMemoryTopicType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicCustomMemoryTopic::class;
  protected $customMemoryTopicDataType = '';
  protected $managedMemoryTopicType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicManagedMemoryTopic::class;
  protected $managedMemoryTopicDataType = '';

  /**
   * A custom memory topic defined by the developer.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicCustomMemoryTopic $customMemoryTopic
   */
  public function setCustomMemoryTopic(GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicCustomMemoryTopic $customMemoryTopic)
  {
    $this->customMemoryTopic = $customMemoryTopic;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicCustomMemoryTopic
   */
  public function getCustomMemoryTopic()
  {
    return $this->customMemoryTopic;
  }
  /**
   * A managed memory topic defined by Memory Bank.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicManagedMemoryTopic $managedMemoryTopic
   */
  public function setManagedMemoryTopic(GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicManagedMemoryTopic $managedMemoryTopic)
  {
    $this->managedMemoryTopic = $managedMemoryTopic;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopicManagedMemoryTopic
   */
  public function getManagedMemoryTopic()
  {
    return $this->managedMemoryTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MemoryBankCustomizationConfigMemoryTopic');
