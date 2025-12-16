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

namespace Google\Service\CloudDeploy;

class DeliveryPipeline extends \Google\Model
{
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy.
   *
   * @var string[]
   */
  public $annotations;
  protected $conditionType = PipelineCondition::class;
  protected $conditionDataType = '';
  /**
   * Output only. Time at which the pipeline was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the `DeliveryPipeline`. Max length is 255
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the `DeliveryPipeline`. Format is `projects/{project}/l
   * ocations/{location}/deliveryPipelines/{deliveryPipeline}`. The
   * `deliveryPipeline` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  protected $serialPipelineType = SerialPipeline::class;
  protected $serialPipelineDataType = '';
  /**
   * Optional. When suspended, no new releases or rollouts can be created, but
   * in-progress ones will complete.
   *
   * @var bool
   */
  public $suspended;
  /**
   * Output only. Unique identifier of the `DeliveryPipeline`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Most recent time at which the pipeline was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Information around the state of the Delivery Pipeline.
   *
   * @param PipelineCondition $condition
   */
  public function setCondition(PipelineCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return PipelineCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Output only. Time at which the pipeline was created.
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
   * Optional. Description of the `DeliveryPipeline`. Max length is 255
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Labels are attributes that can be set and used by both the user and by
   * Cloud Deploy. Labels must meet the following constraints: * Keys and values
   * can contain only lowercase letters, numeric characters, underscores, and
   * dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. Name of the `DeliveryPipeline`. Format is `projects/{project}/l
   * ocations/{location}/deliveryPipelines/{deliveryPipeline}`. The
   * `deliveryPipeline` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Optional. SerialPipeline defines a sequential set of stages for a
   * `DeliveryPipeline`.
   *
   * @param SerialPipeline $serialPipeline
   */
  public function setSerialPipeline(SerialPipeline $serialPipeline)
  {
    $this->serialPipeline = $serialPipeline;
  }
  /**
   * @return SerialPipeline
   */
  public function getSerialPipeline()
  {
    return $this->serialPipeline;
  }
  /**
   * Optional. When suspended, no new releases or rollouts can be created, but
   * in-progress ones will complete.
   *
   * @param bool $suspended
   */
  public function setSuspended($suspended)
  {
    $this->suspended = $suspended;
  }
  /**
   * @return bool
   */
  public function getSuspended()
  {
    return $this->suspended;
  }
  /**
   * Output only. Unique identifier of the `DeliveryPipeline`.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Most recent time at which the pipeline was updated.
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
class_alias(DeliveryPipeline::class, 'Google_Service_CloudDeploy_DeliveryPipeline');
