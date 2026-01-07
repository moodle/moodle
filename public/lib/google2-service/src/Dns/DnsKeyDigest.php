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

namespace Google\Service\Dns;

class DnsKeyDigest extends \Google\Model
{
  public const TYPE_sha1 = 'sha1';
  public const TYPE_sha256 = 'sha256';
  public const TYPE_sha384 = 'sha384';
  /**
   * The base-16 encoded bytes of this digest. Suitable for use in a DS resource
   * record.
   *
   * @var string
   */
  public $digest;
  /**
   * Specifies the algorithm used to calculate this digest.
   *
   * @var string
   */
  public $type;

  /**
   * The base-16 encoded bytes of this digest. Suitable for use in a DS resource
   * record.
   *
   * @param string $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return string
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * Specifies the algorithm used to calculate this digest.
   *
   * Accepted values: sha1, sha256, sha384
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsKeyDigest::class, 'Google_Service_Dns_DnsKeyDigest');
