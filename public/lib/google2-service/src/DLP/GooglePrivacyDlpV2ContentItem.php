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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2ContentItem extends \Google\Model
{
  protected $byteItemType = GooglePrivacyDlpV2ByteContentItem::class;
  protected $byteItemDataType = '';
  protected $tableType = GooglePrivacyDlpV2Table::class;
  protected $tableDataType = '';
  /**
   * String data to inspect or redact.
   *
   * @var string
   */
  public $value;

  /**
   * Content data to inspect or redact. Replaces `type` and `data`.
   *
   * @param GooglePrivacyDlpV2ByteContentItem $byteItem
   */
  public function setByteItem(GooglePrivacyDlpV2ByteContentItem $byteItem)
  {
    $this->byteItem = $byteItem;
  }
  /**
   * @return GooglePrivacyDlpV2ByteContentItem
   */
  public function getByteItem()
  {
    return $this->byteItem;
  }
  /**
   * Structured content for inspection. See https://cloud.google.com/sensitive-
   * data-protection/docs/inspecting-text#inspecting_a_table to learn more.
   *
   * @param GooglePrivacyDlpV2Table $table
   */
  public function setTable(GooglePrivacyDlpV2Table $table)
  {
    $this->table = $table;
  }
  /**
   * @return GooglePrivacyDlpV2Table
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * String data to inspect or redact.
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
class_alias(GooglePrivacyDlpV2ContentItem::class, 'Google_Service_DLP_GooglePrivacyDlpV2ContentItem');
