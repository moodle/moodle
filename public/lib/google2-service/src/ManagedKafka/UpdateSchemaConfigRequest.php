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

namespace Google\Service\ManagedKafka;

class UpdateSchemaConfigRequest extends \Google\Model
{
  /**
   * No compatibility check.
   */
  public const COMPATIBILITY_NONE = 'NONE';
  /**
   * Backwards compatible with the most recent version.
   */
  public const COMPATIBILITY_BACKWARD = 'BACKWARD';
  /**
   * Backwards compatible with all previous versions.
   */
  public const COMPATIBILITY_BACKWARD_TRANSITIVE = 'BACKWARD_TRANSITIVE';
  /**
   * Forwards compatible with the most recent version.
   */
  public const COMPATIBILITY_FORWARD = 'FORWARD';
  /**
   * Forwards compatible with all previous versions.
   */
  public const COMPATIBILITY_FORWARD_TRANSITIVE = 'FORWARD_TRANSITIVE';
  /**
   * Backwards and forwards compatible with the most recent version.
   */
  public const COMPATIBILITY_FULL = 'FULL';
  /**
   * Backwards and forwards compatible with all previous versions.
   */
  public const COMPATIBILITY_FULL_TRANSITIVE = 'FULL_TRANSITIVE';
  /**
   * Required. The compatibility type of the schemas. Cannot be unset for a
   * SchemaRegistry-level SchemaConfig. If unset on a SchemaSubject-level
   * SchemaConfig, removes the compatibility field for the SchemaConfig.
   *
   * @var string
   */
  public $compatibility;
  /**
   * Optional. If true, the schema will be normalized before being stored or
   * looked up. The default is false. Cannot be unset for a SchemaRegistry-level
   * SchemaConfig. If unset on a SchemaSubject-level SchemaConfig, removes the
   * normalize field for the SchemaConfig.
   *
   * @var bool
   */
  public $normalize;

  /**
   * Required. The compatibility type of the schemas. Cannot be unset for a
   * SchemaRegistry-level SchemaConfig. If unset on a SchemaSubject-level
   * SchemaConfig, removes the compatibility field for the SchemaConfig.
   *
   * Accepted values: NONE, BACKWARD, BACKWARD_TRANSITIVE, FORWARD,
   * FORWARD_TRANSITIVE, FULL, FULL_TRANSITIVE
   *
   * @param self::COMPATIBILITY_* $compatibility
   */
  public function setCompatibility($compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return self::COMPATIBILITY_*
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * Optional. If true, the schema will be normalized before being stored or
   * looked up. The default is false. Cannot be unset for a SchemaRegistry-level
   * SchemaConfig. If unset on a SchemaSubject-level SchemaConfig, removes the
   * normalize field for the SchemaConfig.
   *
   * @param bool $normalize
   */
  public function setNormalize($normalize)
  {
    $this->normalize = $normalize;
  }
  /**
   * @return bool
   */
  public function getNormalize()
  {
    return $this->normalize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSchemaConfigRequest::class, 'Google_Service_ManagedKafka_UpdateSchemaConfigRequest');
