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

namespace Google\Service\CloudComposer;

class UserWorkloadsSecret extends \Google\Model
{
  /**
   * Optional. The "data" field of Kubernetes Secret, organized in key-value
   * pairs, which can contain sensitive values such as a password, a token, or a
   * key. The values for all keys have to be base64-encoded strings. For details
   * see: https://kubernetes.io/docs/concepts/configuration/secret/ Example: {
   * "example": "ZXhhbXBsZV92YWx1ZQ==", "another-example":
   * "YW5vdGhlcl9leGFtcGxlX3ZhbHVl" }
   *
   * @var string[]
   */
  public $data;
  /**
   * Identifier. The resource name of the Secret, in the form: "projects/{projec
   * tId}/locations/{locationId}/environments/{environmentId}/userWorkloadsSecre
   * ts/{userWorkloadsSecretId}"
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The "data" field of Kubernetes Secret, organized in key-value
   * pairs, which can contain sensitive values such as a password, a token, or a
   * key. The values for all keys have to be base64-encoded strings. For details
   * see: https://kubernetes.io/docs/concepts/configuration/secret/ Example: {
   * "example": "ZXhhbXBsZV92YWx1ZQ==", "another-example":
   * "YW5vdGhlcl9leGFtcGxlX3ZhbHVl" }
   *
   * @param string[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Identifier. The resource name of the Secret, in the form: "projects/{projec
   * tId}/locations/{locationId}/environments/{environmentId}/userWorkloadsSecre
   * ts/{userWorkloadsSecretId}"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserWorkloadsSecret::class, 'Google_Service_CloudComposer_UserWorkloadsSecret');
