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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1ListFieldsResponse extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $fieldsType = GoogleFirestoreAdminV1Field::class;
  protected $fieldsDataType = 'array';
  /**
   * A page token that may be used to request another page of results. If blank,
   * this is the last page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The requested fields.
   *
   * @param GoogleFirestoreAdminV1Field[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleFirestoreAdminV1Field[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * A page token that may be used to request another page of results. If blank,
   * this is the last page.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1ListFieldsResponse::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1ListFieldsResponse');
