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

namespace Google\Service\Bigquery;

class ForeignViewDefinition extends \Google\Model
{
  /**
   * Optional. Represents the dialect of the query.
   *
   * @var string
   */
  public $dialect;
  /**
   * Required. The query that defines the view.
   *
   * @var string
   */
  public $query;

  /**
   * Optional. Represents the dialect of the query.
   *
   * @param string $dialect
   */
  public function setDialect($dialect)
  {
    $this->dialect = $dialect;
  }
  /**
   * @return string
   */
  public function getDialect()
  {
    return $this->dialect;
  }
  /**
   * Required. The query that defines the view.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForeignViewDefinition::class, 'Google_Service_Bigquery_ForeignViewDefinition');
