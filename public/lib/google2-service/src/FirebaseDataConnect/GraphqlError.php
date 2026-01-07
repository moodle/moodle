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

namespace Google\Service\FirebaseDataConnect;

class GraphqlError extends \Google\Collection
{
  protected $collection_key = 'path';
  protected $extensionsType = GraphqlErrorExtensions::class;
  protected $extensionsDataType = '';
  protected $locationsType = SourceLocation::class;
  protected $locationsDataType = 'array';
  /**
   * The detailed error message. The message should help developer understand
   * the underlying problem without leaking internal data.
   *
   * @var string
   */
  public $message;
  /**
   * The result field which could not be populated due to error. Clients can use
   * path to identify whether a null result is intentional or caused by a
   * runtime error. It should be a list of string or index from the root of
   * GraphQL query document.
   *
   * @var array[]
   */
  public $path;

  /**
   * Additional error information.
   *
   * @param GraphqlErrorExtensions $extensions
   */
  public function setExtensions(GraphqlErrorExtensions $extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return GraphqlErrorExtensions
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * The source locations where the error occurred. Locations should help
   * developers and toolings identify the source of error quickly. Included in
   * admin endpoints (`ExecuteGraphql`, `ExecuteGraphqlRead`,
   * `IntrospectGraphql`, `ImpersonateQuery`, `ImpersonateMutation`,
   * `UpdateSchema` and `UpdateConnector`) to reference the provided GraphQL GQL
   * document. Omitted in `ExecuteMutation` and `ExecuteQuery` since the caller
   * shouldn't have access access the underlying GQL source.
   *
   * @param SourceLocation[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return SourceLocation[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The detailed error message. The message should help developer understand
   * the underlying problem without leaking internal data.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The result field which could not be populated due to error. Clients can use
   * path to identify whether a null result is intentional or caused by a
   * runtime error. It should be a list of string or index from the root of
   * GraphQL query document.
   *
   * @param array[] $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return array[]
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GraphqlError::class, 'Google_Service_FirebaseDataConnect_GraphqlError');
