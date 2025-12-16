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

namespace Google\Service\Iam;

class GoogleIamV2Policy extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * A key-value map to store arbitrary metadata for the `Policy`. Keys can be
   * up to 63 characters. Values can be up to 255 characters.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The time when the `Policy` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the `Policy` was deleted. Empty if the policy is
   * not deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * A user-specified description of the `Policy`. This value can be up to 63
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * An opaque tag that identifies the current version of the `Policy`. IAM uses
   * this value to help manage concurrent updates, so they do not cause one
   * update to be overwritten by another. If this field is present in a
   * CreatePolicyRequest, the value is ignored.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The kind of the `Policy`. Always contains the value
   * `DenyPolicy`.
   *
   * @var string
   */
  public $kind;
  /**
   * Immutable. The resource name of the `Policy`, which must be unique. Format:
   * `policies/{attachment_point}/denypolicies/{policy_id}` The attachment point
   * is identified by its URL-encoded full resource name, which means that the
   * forward-slash character, `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies/my-deny-policy`. For organizations and folders, use
   * the numeric ID in the full resource name. For projects, requests can use
   * the alphanumeric or the numeric ID. Responses always contain the numeric
   * ID.
   *
   * @var string
   */
  public $name;
  protected $rulesType = GoogleIamV2PolicyRule::class;
  protected $rulesDataType = 'array';
  /**
   * Immutable. The globally unique ID of the `Policy`. Assigned automatically
   * when the `Policy` is created.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the `Policy` was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * A key-value map to store arbitrary metadata for the `Policy`. Keys can be
   * up to 63 characters. Values can be up to 255 characters.
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
   * Output only. The time when the `Policy` was created.
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
   * Output only. The time when the `Policy` was deleted. Empty if the policy is
   * not deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * A user-specified description of the `Policy`. This value can be up to 63
   * characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * An opaque tag that identifies the current version of the `Policy`. IAM uses
   * this value to help manage concurrent updates, so they do not cause one
   * update to be overwritten by another. If this field is present in a
   * CreatePolicyRequest, the value is ignored.
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
   * Output only. The kind of the `Policy`. Always contains the value
   * `DenyPolicy`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Immutable. The resource name of the `Policy`, which must be unique. Format:
   * `policies/{attachment_point}/denypolicies/{policy_id}` The attachment point
   * is identified by its URL-encoded full resource name, which means that the
   * forward-slash character, `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies/my-deny-policy`. For organizations and folders, use
   * the numeric ID in the full resource name. For projects, requests can use
   * the alphanumeric or the numeric ID. Responses always contain the numeric
   * ID.
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
   * A list of rules that specify the behavior of the `Policy`. All of the rules
   * should be of the `kind` specified in the `Policy`.
   *
   * @param GoogleIamV2PolicyRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GoogleIamV2PolicyRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Immutable. The globally unique ID of the `Policy`. Assigned automatically
   * when the `Policy` is created.
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
   * Output only. The time when the `Policy` was last updated.
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
class_alias(GoogleIamV2Policy::class, 'Google_Service_Iam_GoogleIamV2Policy');
