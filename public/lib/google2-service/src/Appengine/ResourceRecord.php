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

namespace Google\Service\Appengine;

class ResourceRecord extends \Google\Model
{
  /**
   * An unknown resource record.
   */
  public const TYPE_RECORD_TYPE_UNSPECIFIED = 'RECORD_TYPE_UNSPECIFIED';
  /**
   * An A resource record. Data is an IPv4 address.
   */
  public const TYPE_A = 'A';
  /**
   * An AAAA resource record. Data is an IPv6 address.
   */
  public const TYPE_AAAA = 'AAAA';
  /**
   * A CNAME resource record. Data is a domain name to be aliased.
   */
  public const TYPE_CNAME = 'CNAME';
  /**
   * Relative name of the object affected by this record. Only applicable for
   * CNAME records. Example: 'www'.
   *
   * @var string
   */
  public $name;
  /**
   * Data for this record. Values vary by record type, as defined in RFC 1035
   * (section 5) and RFC 1034 (section 3.6.1).
   *
   * @var string
   */
  public $rrdata;
  /**
   * Resource record type. Example: AAAA.
   *
   * @var string
   */
  public $type;

  /**
   * Relative name of the object affected by this record. Only applicable for
   * CNAME records. Example: 'www'.
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
   * Data for this record. Values vary by record type, as defined in RFC 1035
   * (section 5) and RFC 1034 (section 3.6.1).
   *
   * @param string $rrdata
   */
  public function setRrdata($rrdata)
  {
    $this->rrdata = $rrdata;
  }
  /**
   * @return string
   */
  public function getRrdata()
  {
    return $this->rrdata;
  }
  /**
   * Resource record type. Example: AAAA.
   *
   * Accepted values: RECORD_TYPE_UNSPECIFIED, A, AAAA, CNAME
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
class_alias(ResourceRecord::class, 'Google_Service_Appengine_ResourceRecord');
