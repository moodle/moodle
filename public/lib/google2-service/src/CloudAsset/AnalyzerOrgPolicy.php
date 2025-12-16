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

namespace Google\Service\CloudAsset;

class AnalyzerOrgPolicy extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource where this organization policy applies to. For any user defined
   * org policies, this field has the same value as the [attached_resource]
   * field. Only for default policy, this field has the different value.
   *
   * @var string
   */
  public $appliedResource;
  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource where this organization policy is set. Notice that some type of
   * constraints are defined with default policy. This field will be empty for
   * them.
   *
   * @var string
   */
  public $attachedResource;
  /**
   * If `inherit_from_parent` is true, Rules set higher up in the hierarchy (up
   * to the closest root) are inherited and present in the effective policy. If
   * it is false, then no rules are inherited, and this policy becomes the
   * effective root for evaluation.
   *
   * @var bool
   */
  public $inheritFromParent;
  /**
   * Ignores policies set above this resource and restores the default behavior
   * of the constraint at this resource. This field can be set in policies for
   * either list or boolean constraints. If set, `rules` must be empty and
   * `inherit_from_parent` must be set to false.
   *
   * @var bool
   */
  public $reset;
  protected $rulesType = GoogleCloudAssetV1Rule::class;
  protected $rulesDataType = 'array';

  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource where this organization policy applies to. For any user defined
   * org policies, this field has the same value as the [attached_resource]
   * field. Only for default policy, this field has the different value.
   *
   * @param string $appliedResource
   */
  public function setAppliedResource($appliedResource)
  {
    $this->appliedResource = $appliedResource;
  }
  /**
   * @return string
   */
  public function getAppliedResource()
  {
    return $this->appliedResource;
  }
  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource where this organization policy is set. Notice that some type of
   * constraints are defined with default policy. This field will be empty for
   * them.
   *
   * @param string $attachedResource
   */
  public function setAttachedResource($attachedResource)
  {
    $this->attachedResource = $attachedResource;
  }
  /**
   * @return string
   */
  public function getAttachedResource()
  {
    return $this->attachedResource;
  }
  /**
   * If `inherit_from_parent` is true, Rules set higher up in the hierarchy (up
   * to the closest root) are inherited and present in the effective policy. If
   * it is false, then no rules are inherited, and this policy becomes the
   * effective root for evaluation.
   *
   * @param bool $inheritFromParent
   */
  public function setInheritFromParent($inheritFromParent)
  {
    $this->inheritFromParent = $inheritFromParent;
  }
  /**
   * @return bool
   */
  public function getInheritFromParent()
  {
    return $this->inheritFromParent;
  }
  /**
   * Ignores policies set above this resource and restores the default behavior
   * of the constraint at this resource. This field can be set in policies for
   * either list or boolean constraints. If set, `rules` must be empty and
   * `inherit_from_parent` must be set to false.
   *
   * @param bool $reset
   */
  public function setReset($reset)
  {
    $this->reset = $reset;
  }
  /**
   * @return bool
   */
  public function getReset()
  {
    return $this->reset;
  }
  /**
   * List of rules for this organization policy.
   *
   * @param GoogleCloudAssetV1Rule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GoogleCloudAssetV1Rule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzerOrgPolicy::class, 'Google_Service_CloudAsset_AnalyzerOrgPolicy');
