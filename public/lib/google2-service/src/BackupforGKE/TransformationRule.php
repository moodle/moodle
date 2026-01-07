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

namespace Google\Service\BackupforGKE;

class TransformationRule extends \Google\Collection
{
  protected $collection_key = 'fieldActions';
  /**
   * Optional. The description is a user specified string description of the
   * transformation rule.
   *
   * @var string
   */
  public $description;
  protected $fieldActionsType = TransformationRuleAction::class;
  protected $fieldActionsDataType = 'array';
  protected $resourceFilterType = ResourceFilter::class;
  protected $resourceFilterDataType = '';

  /**
   * Optional. The description is a user specified string description of the
   * transformation rule.
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
   * Required. A list of transformation rule actions to take against candidate
   * resources. Actions are executed in order defined - this order matters, as
   * they could potentially interfere with each other and the first operation
   * could affect the outcome of the second operation.
   *
   * @param TransformationRuleAction[] $fieldActions
   */
  public function setFieldActions($fieldActions)
  {
    $this->fieldActions = $fieldActions;
  }
  /**
   * @return TransformationRuleAction[]
   */
  public function getFieldActions()
  {
    return $this->fieldActions;
  }
  /**
   * Optional. This field is used to specify a set of fields that should be used
   * to determine which resources in backup should be acted upon by the supplied
   * transformation rule actions, and this will ensure that only specific
   * resources are affected by transformation rule actions.
   *
   * @param ResourceFilter $resourceFilter
   */
  public function setResourceFilter(ResourceFilter $resourceFilter)
  {
    $this->resourceFilter = $resourceFilter;
  }
  /**
   * @return ResourceFilter
   */
  public function getResourceFilter()
  {
    return $this->resourceFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransformationRule::class, 'Google_Service_BackupforGKE_TransformationRule');
