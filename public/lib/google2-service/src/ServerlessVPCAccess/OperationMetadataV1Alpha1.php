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

namespace Google\Service\ServerlessVPCAccess;

class OperationMetadataV1Alpha1 extends \Google\Model
{
  /**
   * Output only. Time when the operation completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Time when the operation was created.
   *
   * @var string
   */
  public $insertTime;
  /**
   * Output only. Method that initiated the operation e.g.
   * google.cloud.vpcaccess.v1alpha1.Connectors.CreateConnector.
   *
   * @var string
   */
  public $method;
  /**
   * Output only. Name of the resource that this operation is acting on e.g.
   * projects/my-project/locations/us-central1/connectors/v1.
   *
   * @var string
   */
  public $target;

  /**
   * Output only. Time when the operation completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Time when the operation was created.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * Output only. Method that initiated the operation e.g.
   * google.cloud.vpcaccess.v1alpha1.Connectors.CreateConnector.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Output only. Name of the resource that this operation is acting on e.g.
   * projects/my-project/locations/us-central1/connectors/v1.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadataV1Alpha1::class, 'Google_Service_ServerlessVPCAccess_OperationMetadataV1Alpha1');
