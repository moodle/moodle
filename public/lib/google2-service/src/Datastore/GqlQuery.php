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

namespace Google\Service\Datastore;

class GqlQuery extends \Google\Collection
{
  protected $collection_key = 'positionalBindings';
  /**
   * When false, the query string must not contain any literals and instead must
   * bind all values. For example, `SELECT * FROM Kind WHERE a = 'string
   * literal'` is not allowed, while `SELECT * FROM Kind WHERE a = @value` is.
   *
   * @var bool
   */
  public $allowLiterals;
  protected $namedBindingsType = GqlQueryParameter::class;
  protected $namedBindingsDataType = 'map';
  protected $positionalBindingsType = GqlQueryParameter::class;
  protected $positionalBindingsDataType = 'array';
  /**
   * A string of the format described
   * [here](https://cloud.google.com/datastore/docs/apis/gql/gql_reference).
   *
   * @var string
   */
  public $queryString;

  /**
   * When false, the query string must not contain any literals and instead must
   * bind all values. For example, `SELECT * FROM Kind WHERE a = 'string
   * literal'` is not allowed, while `SELECT * FROM Kind WHERE a = @value` is.
   *
   * @param bool $allowLiterals
   */
  public function setAllowLiterals($allowLiterals)
  {
    $this->allowLiterals = $allowLiterals;
  }
  /**
   * @return bool
   */
  public function getAllowLiterals()
  {
    return $this->allowLiterals;
  }
  /**
   * For each non-reserved named binding site in the query string, there must be
   * a named parameter with that name, but not necessarily the inverse. Key must
   * match regex `A-Za-z_$*`, must not match regex `__.*__`, and must not be
   * `""`.
   *
   * @param GqlQueryParameter[] $namedBindings
   */
  public function setNamedBindings($namedBindings)
  {
    $this->namedBindings = $namedBindings;
  }
  /**
   * @return GqlQueryParameter[]
   */
  public function getNamedBindings()
  {
    return $this->namedBindings;
  }
  /**
   * Numbered binding site @1 references the first numbered parameter,
   * effectively using 1-based indexing, rather than the usual 0. For each
   * binding site numbered i in `query_string`, there must be an i-th numbered
   * parameter. The inverse must also be true.
   *
   * @param GqlQueryParameter[] $positionalBindings
   */
  public function setPositionalBindings($positionalBindings)
  {
    $this->positionalBindings = $positionalBindings;
  }
  /**
   * @return GqlQueryParameter[]
   */
  public function getPositionalBindings()
  {
    return $this->positionalBindings;
  }
  /**
   * A string of the format described
   * [here](https://cloud.google.com/datastore/docs/apis/gql/gql_reference).
   *
   * @param string $queryString
   */
  public function setQueryString($queryString)
  {
    $this->queryString = $queryString;
  }
  /**
   * @return string
   */
  public function getQueryString()
  {
    return $this->queryString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GqlQuery::class, 'Google_Service_Datastore_GqlQuery');
