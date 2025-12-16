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

class Document extends \Google\Model
{
  /**
   * Output only. The time at which the document was created. This value
   * increases monotonically when a document is deleted then recreated. It can
   * also be compared to values from other documents and the `read_time` of a
   * query.
   *
   * @var string
   */
  public $createTime;
  protected $fieldsType = Value::class;
  protected $fieldsDataType = 'map';
  /**
   * The resource name of the document, for example
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time at which the document was last changed. This value is
   * initially set to the `create_time` then increases monotonically with each
   * change to the document. It can also be compared to values from other
   * documents and the `read_time` of a query.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the document was created. This value
   * increases monotonically when a document is deleted then recreated. It can
   * also be compared to values from other documents and the `read_time` of a
   * query.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The document's fields. The map keys represent field names. Field names
   * matching the regular expression `__.*__` are reserved. Reserved field names
   * are forbidden except in certain documented contexts. The field names,
   * represented as UTF-8, must not exceed 1,500 bytes and cannot be empty.
   * Field paths may be used in other contexts to refer to structured fields
   * defined here. For `map_value`, the field path is represented by a dot-
   * delimited (`.`) string of segments. Each segment is either a simple field
   * name (defined below) or a quoted field name. For example, the structured
   * field `"foo" : { map_value: { "x&y" : { string_value: "hello" }}}` would be
   * represented by the field path `` foo.`x&y` ``. A simple field name contains
   * only characters `a` to `z`, `A` to `Z`, `0` to `9`, or `_`, and must not
   * start with `0` to `9`. For example, `foo_bar_17`. A quoted field name
   * starts and ends with `` ` `` and may contain any character. Some
   * characters, including `` ` ``, must be escaped using a `\`. For example, ``
   * `x&y` `` represents `x&y` and `` `bak\`tik` `` represents `` bak`tik ``.
   *
   * @param Value[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return Value[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The resource name of the document, for example
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
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
   * Output only. The time at which the document was last changed. This value is
   * initially set to the `create_time` then increases monotonically with each
   * change to the document. It can also be compared to values from other
   * documents and the `read_time` of a query.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Document::class, 'Google_Service_Firestore_Document');
