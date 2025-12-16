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

namespace Google\Service\Sheets;

class Spreadsheet extends \Google\Collection
{
  protected $collection_key = 'sheets';
  protected $dataSourceSchedulesType = DataSourceRefreshSchedule::class;
  protected $dataSourceSchedulesDataType = 'array';
  protected $dataSourcesType = DataSource::class;
  protected $dataSourcesDataType = 'array';
  protected $developerMetadataType = DeveloperMetadata::class;
  protected $developerMetadataDataType = 'array';
  protected $namedRangesType = NamedRange::class;
  protected $namedRangesDataType = 'array';
  protected $propertiesType = SpreadsheetProperties::class;
  protected $propertiesDataType = '';
  protected $sheetsType = Sheet::class;
  protected $sheetsDataType = 'array';
  /**
   * The ID of the spreadsheet. This field is read-only.
   *
   * @var string
   */
  public $spreadsheetId;
  /**
   * The url of the spreadsheet. This field is read-only.
   *
   * @var string
   */
  public $spreadsheetUrl;

  /**
   * Output only. A list of data source refresh schedules.
   *
   * @param DataSourceRefreshSchedule[] $dataSourceSchedules
   */
  public function setDataSourceSchedules($dataSourceSchedules)
  {
    $this->dataSourceSchedules = $dataSourceSchedules;
  }
  /**
   * @return DataSourceRefreshSchedule[]
   */
  public function getDataSourceSchedules()
  {
    return $this->dataSourceSchedules;
  }
  /**
   * A list of external data sources connected with the spreadsheet.
   *
   * @param DataSource[] $dataSources
   */
  public function setDataSources($dataSources)
  {
    $this->dataSources = $dataSources;
  }
  /**
   * @return DataSource[]
   */
  public function getDataSources()
  {
    return $this->dataSources;
  }
  /**
   * The developer metadata associated with a spreadsheet.
   *
   * @param DeveloperMetadata[] $developerMetadata
   */
  public function setDeveloperMetadata($developerMetadata)
  {
    $this->developerMetadata = $developerMetadata;
  }
  /**
   * @return DeveloperMetadata[]
   */
  public function getDeveloperMetadata()
  {
    return $this->developerMetadata;
  }
  /**
   * The named ranges defined in a spreadsheet.
   *
   * @param NamedRange[] $namedRanges
   */
  public function setNamedRanges($namedRanges)
  {
    $this->namedRanges = $namedRanges;
  }
  /**
   * @return NamedRange[]
   */
  public function getNamedRanges()
  {
    return $this->namedRanges;
  }
  /**
   * Overall properties of a spreadsheet.
   *
   * @param SpreadsheetProperties $properties
   */
  public function setProperties(SpreadsheetProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return SpreadsheetProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The sheets that are part of a spreadsheet.
   *
   * @param Sheet[] $sheets
   */
  public function setSheets($sheets)
  {
    $this->sheets = $sheets;
  }
  /**
   * @return Sheet[]
   */
  public function getSheets()
  {
    return $this->sheets;
  }
  /**
   * The ID of the spreadsheet. This field is read-only.
   *
   * @param string $spreadsheetId
   */
  public function setSpreadsheetId($spreadsheetId)
  {
    $this->spreadsheetId = $spreadsheetId;
  }
  /**
   * @return string
   */
  public function getSpreadsheetId()
  {
    return $this->spreadsheetId;
  }
  /**
   * The url of the spreadsheet. This field is read-only.
   *
   * @param string $spreadsheetUrl
   */
  public function setSpreadsheetUrl($spreadsheetUrl)
  {
    $this->spreadsheetUrl = $spreadsheetUrl;
  }
  /**
   * @return string
   */
  public function getSpreadsheetUrl()
  {
    return $this->spreadsheetUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Spreadsheet::class, 'Google_Service_Sheets_Spreadsheet');
