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

namespace Google\Service\Directory;

class SchemaFieldSpec extends \Google\Model
{
  /**
   * Display Name of the field.
   *
   * @var string
   */
  public $displayName;
  /**
   * The ETag of the field.
   *
   * @var string
   */
  public $etag;
  /**
   * The unique identifier of the field (Read-only)
   *
   * @var string
   */
  public $fieldId;
  /**
   * The name of the field.
   *
   * @var string
   */
  public $fieldName;
  /**
   * The type of the field.
   *
   * @var string
   */
  public $fieldType;
  /**
   * Boolean specifying whether the field is indexed or not. Default: `true`.
   *
   * @var bool
   */
  public $indexed;
  /**
   * The kind of resource this is. For schema fields this is always
   * `admin#directory#schema#fieldspec`.
   *
   * @var string
   */
  public $kind;
  /**
   * A boolean specifying whether this is a multi-valued field or not. Default:
   * `false`.
   *
   * @var bool
   */
  public $multiValued;
  protected $numericIndexingSpecType = SchemaFieldSpecNumericIndexingSpec::class;
  protected $numericIndexingSpecDataType = '';
  /**
   * Specifies who can view values of this field. See [Retrieve users as a non-a
   * dministrator](https://developers.google.com/workspace/admin/directory/v1/gu
   * ides/manage-users#retrieve_users_non_admin) for more information. Note: It
   * may take up to 24 hours for changes to this field to be reflected.
   *
   * @var string
   */
  public $readAccessType;

  /**
   * Display Name of the field.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The ETag of the field.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The unique identifier of the field (Read-only)
   *
   * @param string $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return string
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * The name of the field.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * The type of the field.
   *
   * @param string $fieldType
   */
  public function setFieldType($fieldType)
  {
    $this->fieldType = $fieldType;
  }
  /**
   * @return string
   */
  public function getFieldType()
  {
    return $this->fieldType;
  }
  /**
   * Boolean specifying whether the field is indexed or not. Default: `true`.
   *
   * @param bool $indexed
   */
  public function setIndexed($indexed)
  {
    $this->indexed = $indexed;
  }
  /**
   * @return bool
   */
  public function getIndexed()
  {
    return $this->indexed;
  }
  /**
   * The kind of resource this is. For schema fields this is always
   * `admin#directory#schema#fieldspec`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A boolean specifying whether this is a multi-valued field or not. Default:
   * `false`.
   *
   * @param bool $multiValued
   */
  public function setMultiValued($multiValued)
  {
    $this->multiValued = $multiValued;
  }
  /**
   * @return bool
   */
  public function getMultiValued()
  {
    return $this->multiValued;
  }
  /**
   * Indexing spec for a numeric field. By default, only exact match queries
   * will be supported for numeric fields. Setting the `numericIndexingSpec`
   * allows range queries to be supported.
   *
   * @param SchemaFieldSpecNumericIndexingSpec $numericIndexingSpec
   */
  public function setNumericIndexingSpec(SchemaFieldSpecNumericIndexingSpec $numericIndexingSpec)
  {
    $this->numericIndexingSpec = $numericIndexingSpec;
  }
  /**
   * @return SchemaFieldSpecNumericIndexingSpec
   */
  public function getNumericIndexingSpec()
  {
    return $this->numericIndexingSpec;
  }
  /**
   * Specifies who can view values of this field. See [Retrieve users as a non-a
   * dministrator](https://developers.google.com/workspace/admin/directory/v1/gu
   * ides/manage-users#retrieve_users_non_admin) for more information. Note: It
   * may take up to 24 hours for changes to this field to be reflected.
   *
   * @param string $readAccessType
   */
  public function setReadAccessType($readAccessType)
  {
    $this->readAccessType = $readAccessType;
  }
  /**
   * @return string
   */
  public function getReadAccessType()
  {
    return $this->readAccessType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaFieldSpec::class, 'Google_Service_Directory_SchemaFieldSpec');
