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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1EventingRuntimeDataWebhookData extends \Google\Collection
{
  protected $collection_key = 'additionalVariables';
  protected $additionalVariablesType = GoogleCloudConnectorsV1ConfigVariable::class;
  protected $additionalVariablesDataType = 'array';
  /**
   * Output only. Timestamp when the webhook was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. ID to uniquely identify webhook.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Name of the Webhook
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Next webhook refresh time. Will be null if refresh is not
   * supported.
   *
   * @var string
   */
  public $nextRefreshTime;
  /**
   * Output only. Timestamp when the webhook was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Additional webhook related field values.
   *
   * @param GoogleCloudConnectorsV1ConfigVariable[] $additionalVariables
   */
  public function setAdditionalVariables($additionalVariables)
  {
    $this->additionalVariables = $additionalVariables;
  }
  /**
   * @return GoogleCloudConnectorsV1ConfigVariable[]
   */
  public function getAdditionalVariables()
  {
    return $this->additionalVariables;
  }
  /**
   * Output only. Timestamp when the webhook was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. ID to uniquely identify webhook.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Name of the Webhook
   *
   * @param string $name
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
   * Output only. Next webhook refresh time. Will be null if refresh is not
   * supported.
   *
   * @param string $nextRefreshTime
   */
  public function setNextRefreshTime($nextRefreshTime)
  {
    $this->nextRefreshTime = $nextRefreshTime;
  }
  /**
   * @return string
   */
  public function getNextRefreshTime()
  {
    return $this->nextRefreshTime;
  }
  /**
   * Output only. Timestamp when the webhook was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1EventingRuntimeDataWebhookData::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1EventingRuntimeDataWebhookData');
