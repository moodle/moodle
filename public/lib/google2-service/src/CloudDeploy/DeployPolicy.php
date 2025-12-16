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

class DeployPolicy extends \Google\Collection
{
  protected $collection_key = 'selectors';
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. Annotations must meet the following
   * constraints: * Annotations are key/value pairs. * Valid annotation keys
   * have two segments: an optional prefix and name, separated by a slash (`/`).
   * * The name segment is required and must be 63 characters or less, beginning
   * and ending with an alphanumeric character (`[a-z0-9A-Z]`) with dashes
   * (`-`), underscores (`_`), dots (`.`), and alphanumerics between. * The
   * prefix is optional. If specified, the prefix must be a DNS subdomain: a
   * series of DNS labels separated by dots(`.`), not longer than 253 characters
   * in total, followed by a slash (`/`). See
   * https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/annotations/#syntax-and-character-set for more details.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Time at which the deploy policy was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the `DeployPolicy`. Max length is 255 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The weak etag of the `DeployPolicy` resource. This checksum is computed by
   * the server based on the value of other fields, and may be sent on update
   * and delete requests to ensure the client has an up-to-date value before
   * proceeding.
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
   * Output only. Name of the `DeployPolicy`. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deployPolicy}`.
   * The `deployPolicy` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  protected $rulesType = PolicyRule::class;
  protected $rulesDataType = 'array';
  protected $selectorsType = DeployPolicyResourceSelector::class;
  protected $selectorsDataType = 'array';
  /**
   * Optional. When suspended, the policy will not prevent actions from
   * occurring, even if the action violates the policy.
   *
   * @var bool
   */
  public $suspended;
  /**
   * Output only. Unique identifier of the `DeployPolicy`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Most recent time at which the deploy policy was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. Annotations must meet the following
   * constraints: * Annotations are key/value pairs. * Valid annotation keys
   * have two segments: an optional prefix and name, separated by a slash (`/`).
   * * The name segment is required and must be 63 characters or less, beginning
   * and ending with an alphanumeric character (`[a-z0-9A-Z]`) with dashes
   * (`-`), underscores (`_`), dots (`.`), and alphanumerics between. * The
   * prefix is optional. If specified, the prefix must be a DNS subdomain: a
   * series of DNS labels separated by dots(`.`), not longer than 253 characters
   * in total, followed by a slash (`/`). See
   * https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/annotations/#syntax-and-character-set for more details.
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
   * Output only. Time at which the deploy policy was created.
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
   * Optional. Description of the `DeployPolicy`. Max length is 255 characters.
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
   * The weak etag of the `DeployPolicy` resource. This checksum is computed by
   * the server based on the value of other fields, and may be sent on update
   * and delete requests to ensure the client has an up-to-date value before
   * proceeding.
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
   * Output only. Name of the `DeployPolicy`. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deployPolicy}`.
   * The `deployPolicy` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Required. Rules to apply. At least one rule must be present.
   *
   * @param PolicyRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return PolicyRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Required. Selected resources to which the policy will be applied. At least
   * one selector is required. If one selector matches the resource the policy
   * applies. For example, if there are two selectors and the action being
   * attempted matches one of them, the policy will apply to that action.
   *
   * @param DeployPolicyResourceSelector[] $selectors
   */
  public function setSelectors($selectors)
  {
    $this->selectors = $selectors;
  }
  /**
   * @return DeployPolicyResourceSelector[]
   */
  public function getSelectors()
  {
    return $this->selectors;
  }
  /**
   * Optional. When suspended, the policy will not prevent actions from
   * occurring, even if the action violates the policy.
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
   * Output only. Unique identifier of the `DeployPolicy`.
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
   * Output only. Most recent time at which the deploy policy was updated.
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
class_alias(DeployPolicy::class, 'Google_Service_CloudDeploy_DeployPolicy');
