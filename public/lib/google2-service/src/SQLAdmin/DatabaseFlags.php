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

namespace Google\Service\SQLAdmin;

class DatabaseFlags extends \Google\Model
{
  /**
   * The name of the flag. These flags are passed at instance startup, so
   * include both server options and system variables. Flags are specified with
   * underscores, not hyphens. For more information, see [Configuring Database
   * Flags](https://cloud.google.com/sql/docs/mysql/flags) in the Cloud SQL
   * documentation.
   *
   * @var string
   */
  public $name;
  /**
   * The value of the flag. Boolean flags are set to `on` for true and `off` for
   * false. This field must be omitted if the flag doesn't take a value.
   *
   * @var string
   */
  public $value;

  /**
   * The name of the flag. These flags are passed at instance startup, so
   * include both server options and system variables. Flags are specified with
   * underscores, not hyphens. For more information, see [Configuring Database
   * Flags](https://cloud.google.com/sql/docs/mysql/flags) in the Cloud SQL
   * documentation.
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
   * The value of the flag. Boolean flags are set to `on` for true and `off` for
   * false. This field must be omitted if the flag doesn't take a value.
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
class_alias(DatabaseFlags::class, 'Google_Service_SQLAdmin_DatabaseFlags');
