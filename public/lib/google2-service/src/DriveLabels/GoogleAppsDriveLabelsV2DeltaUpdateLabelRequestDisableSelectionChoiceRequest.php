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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestDisableSelectionChoiceRequest extends \Google\Model
{
  protected $disabledPolicyType = GoogleAppsDriveLabelsV2LifecycleDisabledPolicy::class;
  protected $disabledPolicyDataType = '';
  /**
   * Required. The selection field in which a choice will be disabled.
   *
   * @var string
   */
  public $fieldId;
  /**
   * Required. Choice to disable.
   *
   * @var string
   */
  public $id;
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `disabled_policy` is implied and should not be specified. A single
   * `*` can be used as a short-hand for updating every field.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The disabled policy to update.
   *
   * @param GoogleAppsDriveLabelsV2LifecycleDisabledPolicy $disabledPolicy
   */
  public function setDisabledPolicy(GoogleAppsDriveLabelsV2LifecycleDisabledPolicy $disabledPolicy)
  {
    $this->disabledPolicy = $disabledPolicy;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LifecycleDisabledPolicy
   */
  public function getDisabledPolicy()
  {
    return $this->disabledPolicy;
  }
  /**
   * Required. The selection field in which a choice will be disabled.
   *
   * @param string $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return string
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Required. Choice to disable.
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
   * The fields that should be updated. At least one field must be specified.
   * The root `disabled_policy` is implied and should not be specified. A single
   * `*` can be used as a short-hand for updating every field.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestDisableSelectionChoiceRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestDisableSelectionChoiceRequest');
