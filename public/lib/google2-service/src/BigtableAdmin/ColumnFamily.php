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

namespace Google\Service\BigtableAdmin;

class ColumnFamily extends \Google\Model
{
  protected $gcRuleType = GcRule::class;
  protected $gcRuleDataType = '';
  protected $statsType = ColumnFamilyStats::class;
  protected $statsDataType = '';
  protected $valueTypeType = Type::class;
  protected $valueTypeDataType = '';

  /**
   * Garbage collection rule specified as a protobuf. Must serialize to at most
   * 500 bytes. NOTE: Garbage collection executes opportunistically in the
   * background, and so it's possible for reads to return a cell even if it
   * matches the active GC expression for its family.
   *
   * @param GcRule $gcRule
   */
  public function setGcRule(GcRule $gcRule)
  {
    $this->gcRule = $gcRule;
  }
  /**
   * @return GcRule
   */
  public function getGcRule()
  {
    return $this->gcRule;
  }
  /**
   * Output only. Only available with STATS_VIEW, this includes summary
   * statistics about column family contents. For statistics over an entire
   * table, see TableStats above.
   *
   * @param ColumnFamilyStats $stats
   */
  public function setStats(ColumnFamilyStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return ColumnFamilyStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * The type of data stored in each of this family's cell values, including its
   * full encoding. If omitted, the family only serves raw untyped bytes. For
   * now, only the `Aggregate` type is supported. `Aggregate` can only be set at
   * family creation and is immutable afterwards. This field is mutually
   * exclusive with `sql_type`. If `value_type` is `Aggregate`, written data
   * must be compatible with: * `value_type.input_type` for `AddInput` mutations
   *
   * @param Type $valueType
   */
  public function setValueType(Type $valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return Type
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColumnFamily::class, 'Google_Service_BigtableAdmin_ColumnFamily');
