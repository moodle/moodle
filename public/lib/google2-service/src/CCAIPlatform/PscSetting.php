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

namespace Google\Service\CCAIPlatform;

class PscSetting extends \Google\Collection
{
  protected $collection_key = 'producerProjectIds';
  /**
   * The list of project ids that are allowed to send traffic to the service
   * attachment. This field should be filled only for the ingress components.
   *
   * @var string[]
   */
  public $allowedConsumerProjectIds;
  /**
   * Output only. The CCAIP tenant project ids.
   *
   * @var string[]
   */
  public $producerProjectIds;

  /**
   * The list of project ids that are allowed to send traffic to the service
   * attachment. This field should be filled only for the ingress components.
   *
   * @param string[] $allowedConsumerProjectIds
   */
  public function setAllowedConsumerProjectIds($allowedConsumerProjectIds)
  {
    $this->allowedConsumerProjectIds = $allowedConsumerProjectIds;
  }
  /**
   * @return string[]
   */
  public function getAllowedConsumerProjectIds()
  {
    return $this->allowedConsumerProjectIds;
  }
  /**
   * Output only. The CCAIP tenant project ids.
   *
   * @param string[] $producerProjectIds
   */
  public function setProducerProjectIds($producerProjectIds)
  {
    $this->producerProjectIds = $producerProjectIds;
  }
  /**
   * @return string[]
   */
  public function getProducerProjectIds()
  {
    return $this->producerProjectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscSetting::class, 'Google_Service_CCAIPlatform_PscSetting');
