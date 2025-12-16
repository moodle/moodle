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

class TableListTables extends \Google\Model
{
  protected $clusteringType = Clustering::class;
  protected $clusteringDataType = '';
  /**
   * Output only. The time when this table was created, in milliseconds since
   * the epoch.
   *
   * @var string
   */
  public $creationTime;
  /**
   * The time when this table expires, in milliseconds since the epoch. If not
   * present, the table will persist indefinitely. Expired tables will be
   * deleted and their storage reclaimed.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The user-friendly name for this table.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * An opaque ID of the table.
   *
   * @var string
   */
  public $id;
  /**
   * The resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * The labels associated with this table. You can use these to organize and
   * group your tables.
   *
   * @var string[]
   */
  public $labels;
  protected $rangePartitioningType = RangePartitioning::class;
  protected $rangePartitioningDataType = '';
  /**
   * Optional. If set to true, queries including this table must specify a
   * partition filter. This filter is used for partition elimination.
   *
   * @var bool
   */
  public $requirePartitionFilter;
  protected $tableReferenceType = TableReference::class;
  protected $tableReferenceDataType = '';
  protected $timePartitioningType = TimePartitioning::class;
  protected $timePartitioningDataType = '';
  /**
   * The type of table.
   *
   * @var string
   */
  public $type;
  protected $viewType = TableListTablesView::class;
  protected $viewDataType = '';

  /**
   * Clustering specification for this table, if configured.
   *
   * @param Clustering $clustering
   */
  public function setClustering(Clustering $clustering)
  {
    $this->clustering = $clustering;
  }
  /**
   * @return Clustering
   */
  public function getClustering()
  {
    return $this->clustering;
  }
  /**
   * Output only. The time when this table was created, in milliseconds since
   * the epoch.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * The time when this table expires, in milliseconds since the epoch. If not
   * present, the table will persist indefinitely. Expired tables will be
   * deleted and their storage reclaimed.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The user-friendly name for this table.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * An opaque ID of the table.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The resource type.
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
   * The labels associated with this table. You can use these to organize and
   * group your tables.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The range partitioning for this table.
   *
   * @param RangePartitioning $rangePartitioning
   */
  public function setRangePartitioning(RangePartitioning $rangePartitioning)
  {
    $this->rangePartitioning = $rangePartitioning;
  }
  /**
   * @return RangePartitioning
   */
  public function getRangePartitioning()
  {
    return $this->rangePartitioning;
  }
  /**
   * Optional. If set to true, queries including this table must specify a
   * partition filter. This filter is used for partition elimination.
   *
   * @param bool $requirePartitionFilter
   */
  public function setRequirePartitionFilter($requirePartitionFilter)
  {
    $this->requirePartitionFilter = $requirePartitionFilter;
  }
  /**
   * @return bool
   */
  public function getRequirePartitionFilter()
  {
    return $this->requirePartitionFilter;
  }
  /**
   * A reference uniquely identifying table.
   *
   * @param TableReference $tableReference
   */
  public function setTableReference(TableReference $tableReference)
  {
    $this->tableReference = $tableReference;
  }
  /**
   * @return TableReference
   */
  public function getTableReference()
  {
    return $this->tableReference;
  }
  /**
   * The time-based partitioning for this table.
   *
   * @param TimePartitioning $timePartitioning
   */
  public function setTimePartitioning(TimePartitioning $timePartitioning)
  {
    $this->timePartitioning = $timePartitioning;
  }
  /**
   * @return TimePartitioning
   */
  public function getTimePartitioning()
  {
    return $this->timePartitioning;
  }
  /**
   * The type of table.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Information about a logical view.
   *
   * @param TableListTablesView $view
   */
  public function setView(TableListTablesView $view)
  {
    $this->view = $view;
  }
  /**
   * @return TableListTablesView
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableListTables::class, 'Google_Service_Bigquery_TableListTables');
