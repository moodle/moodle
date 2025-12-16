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

namespace Google\Service\ToolResults;

class Any extends \Google\Model
{
  /**
   * A URL/resource name that uniquely identifies the type of the serialized
   * protocol buffer message. This string must contain at least one "/"
   * character. The last segment of the URL's path must represent the fully
   * qualified name of the type (as in `path/google.protobuf.Duration`). The
   * name should be in a canonical form (e.g., leading "." is not accepted). In
   * practice, teams usually precompile into the binary all types that they
   * expect it to use in the context of Any. However, for URLs which use the
   * scheme `http`, `https`, or no scheme, one can optionally set up a type
   * server that maps type URLs to message definitions as follows: * If no
   * scheme is provided, `https` is assumed. * An HTTP GET on the URL must yield
   * a google.protobuf.Type value in binary format, or produce an error. *
   * Applications are allowed to cache lookup results based on the URL, or have
   * them precompiled into a binary to avoid any lookup. Therefore, binary
   * compatibility needs to be preserved on changes to types. (Use versioned
   * type names to manage breaking changes.) Note: this functionality is not
   * currently available in the official protobuf release, and it is not used
   * for type URLs beginning with type.googleapis.com. Schemes other than
   * `http`, `https` (or the empty scheme) might be used with implementation
   * specific semantics.
   *
   * @var string
   */
  public $typeUrl;
  /**
   * Must be a valid serialized protocol buffer of the above specified type.
   *
   * @var string
   */
  public $value;

  /**
   * A URL/resource name that uniquely identifies the type of the serialized
   * protocol buffer message. This string must contain at least one "/"
   * character. The last segment of the URL's path must represent the fully
   * qualified name of the type (as in `path/google.protobuf.Duration`). The
   * name should be in a canonical form (e.g., leading "." is not accepted). In
   * practice, teams usually precompile into the binary all types that they
   * expect it to use in the context of Any. However, for URLs which use the
   * scheme `http`, `https`, or no scheme, one can optionally set up a type
   * server that maps type URLs to message definitions as follows: * If no
   * scheme is provided, `https` is assumed. * An HTTP GET on the URL must yield
   * a google.protobuf.Type value in binary format, or produce an error. *
   * Applications are allowed to cache lookup results based on the URL, or have
   * them precompiled into a binary to avoid any lookup. Therefore, binary
   * compatibility needs to be preserved on changes to types. (Use versioned
   * type names to manage breaking changes.) Note: this functionality is not
   * currently available in the official protobuf release, and it is not used
   * for type URLs beginning with type.googleapis.com. Schemes other than
   * `http`, `https` (or the empty scheme) might be used with implementation
   * specific semantics.
   *
   * @param string $typeUrl
   */
  public function setTypeUrl($typeUrl)
  {
    $this->typeUrl = $typeUrl;
  }
  /**
   * @return string
   */
  public function getTypeUrl()
  {
    return $this->typeUrl;
  }
  /**
   * Must be a valid serialized protocol buffer of the above specified type.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Any::class, 'Google_Service_ToolResults_Any');
