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

class GraphqlResponse extends \Google\Collection
{
  protected $collection_key = 'errors';
  /**
   * The result of the execution of the requested operation. If an error was
   * raised before execution begins, the data entry should not be present in the
   * result. (a request error: https://spec.graphql.org/draft/#sec-
   * Errors.Request-Errors) If an error was raised during the execution that
   * prevented a valid response, the data entry in the response should be null.
   * (a field error: https://spec.graphql.org/draft/#sec-Errors.Error-Result-
   * Format)
   *
   * @var array[]
   */
  public $data;
  protected $errorsType = GraphqlError::class;
  protected $errorsDataType = 'array';

  /**
   * The result of the execution of the requested operation. If an error was
   * raised before execution begins, the data entry should not be present in the
   * result. (a request error: https://spec.graphql.org/draft/#sec-
   * Errors.Request-Errors) If an error was raised during the execution that
   * prevented a valid response, the data entry in the response should be null.
   * (a field error: https://spec.graphql.org/draft/#sec-Errors.Error-Result-
   * Format)
   *
   * @param array[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return array[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Errors of this response. If the data entry in the response is not present,
   * the errors entry must be present. It conforms to
   * https://spec.graphql.org/draft/#sec-Errors.
   *
   * @param GraphqlError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GraphqlError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GraphqlResponse::class, 'Google_Service_FirebaseDataConnect_GraphqlResponse');
