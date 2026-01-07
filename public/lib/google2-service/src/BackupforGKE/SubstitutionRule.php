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

class SubstitutionRule extends \Google\Collection
{
  protected $collection_key = 'targetNamespaces';
  /**
   * Optional. This is the new value to set for any fields that pass the
   * filtering and selection criteria. To remove a value from a Kubernetes
   * resource, either leave this field unspecified, or set it to the empty
   * string ("").
   *
   * @var string
   */
  public $newValue;
  /**
   * Optional. (Filtering parameter) This is a [regular expression]
   * (https://en.wikipedia.org/wiki/Regular_expression) that is compared against
   * the fields matched by the target_json_path expression (and must also have
   * passed the previous filters). Substitution will not be performed against
   * fields whose value does not match this expression. If this field is NOT
   * specified, then ALL fields matched by the target_json_path expression will
   * undergo substitution. Note that an empty (e.g., "", rather than
   * unspecified) value for this field will only match empty fields.
   *
   * @var string
   */
  public $originalValuePattern;
  protected $targetGroupKindsType = GroupKind::class;
  protected $targetGroupKindsDataType = 'array';
  /**
   * Required. This is a [JSONPath]
   * (https://kubernetes.io/docs/reference/kubectl/jsonpath/) expression that
   * matches specific fields of candidate resources and it operates as both a
   * filtering parameter (resources that are not matched with this expression
   * will not be candidates for substitution) as well as a field identifier
   * (identifies exactly which fields out of the candidate resources will be
   * modified).
   *
   * @var string
   */
  public $targetJsonPath;
  /**
   * Optional. (Filtering parameter) Any resource subject to substitution must
   * be contained within one of the listed Kubernetes Namespace in the Backup.
   * If this field is not provided, no namespace filtering will be performed
   * (all resources in all Namespaces, including all cluster-scoped resources,
   * will be candidates for substitution). To mix cluster-scoped and namespaced
   * resources in the same rule, use an empty string ("") as one of the target
   * namespaces.
   *
   * @var string[]
   */
  public $targetNamespaces;

  /**
   * Optional. This is the new value to set for any fields that pass the
   * filtering and selection criteria. To remove a value from a Kubernetes
   * resource, either leave this field unspecified, or set it to the empty
   * string ("").
   *
   * @param string $newValue
   */
  public function setNewValue($newValue)
  {
    $this->newValue = $newValue;
  }
  /**
   * @return string
   */
  public function getNewValue()
  {
    return $this->newValue;
  }
  /**
   * Optional. (Filtering parameter) This is a [regular expression]
   * (https://en.wikipedia.org/wiki/Regular_expression) that is compared against
   * the fields matched by the target_json_path expression (and must also have
   * passed the previous filters). Substitution will not be performed against
   * fields whose value does not match this expression. If this field is NOT
   * specified, then ALL fields matched by the target_json_path expression will
   * undergo substitution. Note that an empty (e.g., "", rather than
   * unspecified) value for this field will only match empty fields.
   *
   * @param string $originalValuePattern
   */
  public function setOriginalValuePattern($originalValuePattern)
  {
    $this->originalValuePattern = $originalValuePattern;
  }
  /**
   * @return string
   */
  public function getOriginalValuePattern()
  {
    return $this->originalValuePattern;
  }
  /**
   * Optional. (Filtering parameter) Any resource subject to substitution must
   * belong to one of the listed "types". If this field is not provided, no type
   * filtering will be performed (all resources of all types matching previous
   * filtering parameters will be candidates for substitution).
   *
   * @param GroupKind[] $targetGroupKinds
   */
  public function setTargetGroupKinds($targetGroupKinds)
  {
    $this->targetGroupKinds = $targetGroupKinds;
  }
  /**
   * @return GroupKind[]
   */
  public function getTargetGroupKinds()
  {
    return $this->targetGroupKinds;
  }
  /**
   * Required. This is a [JSONPath]
   * (https://kubernetes.io/docs/reference/kubectl/jsonpath/) expression that
   * matches specific fields of candidate resources and it operates as both a
   * filtering parameter (resources that are not matched with this expression
   * will not be candidates for substitution) as well as a field identifier
   * (identifies exactly which fields out of the candidate resources will be
   * modified).
   *
   * @param string $targetJsonPath
   */
  public function setTargetJsonPath($targetJsonPath)
  {
    $this->targetJsonPath = $targetJsonPath;
  }
  /**
   * @return string
   */
  public function getTargetJsonPath()
  {
    return $this->targetJsonPath;
  }
  /**
   * Optional. (Filtering parameter) Any resource subject to substitution must
   * be contained within one of the listed Kubernetes Namespace in the Backup.
   * If this field is not provided, no namespace filtering will be performed
   * (all resources in all Namespaces, including all cluster-scoped resources,
   * will be candidates for substitution). To mix cluster-scoped and namespaced
   * resources in the same rule, use an empty string ("") as one of the target
   * namespaces.
   *
   * @param string[] $targetNamespaces
   */
  public function setTargetNamespaces($targetNamespaces)
  {
    $this->targetNamespaces = $targetNamespaces;
  }
  /**
   * @return string[]
   */
  public function getTargetNamespaces()
  {
    return $this->targetNamespaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubstitutionRule::class, 'Google_Service_BackupforGKE_SubstitutionRule');
