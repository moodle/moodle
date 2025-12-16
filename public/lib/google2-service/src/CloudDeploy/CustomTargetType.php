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

class CustomTargetType extends \Google\Model
{
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Time at which the `CustomTargetType` was created.
   *
   * @var string
   */
  public $createTime;
  protected $customActionsType = CustomTargetSkaffoldActions::class;
  protected $customActionsDataType = '';
  /**
   * Output only. Resource id of the `CustomTargetType`.
   *
   * @var string
   */
  public $customTargetTypeId;
  /**
   * Optional. Description of the `CustomTargetType`. Max length is 255
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels are attributes that can be set and used by both the user
   * and by Cloud Deploy. Labels must meet the following constraints: * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the `CustomTargetType`. Format is `projects/{project}/l
   * ocations/{location}/customTargetTypes/{customTargetType}`. The
   * `customTargetType` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique identifier of the `CustomTargetType`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Most recent time at which the `CustomTargetType` was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
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
   * Output only. Time at which the `CustomTargetType` was created.
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
   * Optional. Configures render and deploy for the `CustomTargetType` using
   * Skaffold custom actions.
   *
   * @param CustomTargetSkaffoldActions $customActions
   */
  public function setCustomActions(CustomTargetSkaffoldActions $customActions)
  {
    $this->customActions = $customActions;
  }
  /**
   * @return CustomTargetSkaffoldActions
   */
  public function getCustomActions()
  {
    return $this->customActions;
  }
  /**
   * Output only. Resource id of the `CustomTargetType`.
   *
   * @param string $customTargetTypeId
   */
  public function setCustomTargetTypeId($customTargetTypeId)
  {
    $this->customTargetTypeId = $customTargetTypeId;
  }
  /**
   * @return string
   */
  public function getCustomTargetTypeId()
  {
    return $this->customTargetTypeId;
  }
  /**
   * Optional. Description of the `CustomTargetType`. Max length is 255
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
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
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
   * Optional. Labels are attributes that can be set and used by both the user
   * and by Cloud Deploy. Labels must meet the following constraints: * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. * All characters must use UTF-8 encoding, and international
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
   * Identifier. Name of the `CustomTargetType`. Format is `projects/{project}/l
   * ocations/{location}/customTargetTypes/{customTargetType}`. The
   * `customTargetType` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Output only. Unique identifier of the `CustomTargetType`.
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
   * Output only. Most recent time at which the `CustomTargetType` was updated.
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
class_alias(CustomTargetType::class, 'Google_Service_CloudDeploy_CustomTargetType');
