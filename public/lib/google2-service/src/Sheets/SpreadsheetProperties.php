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

class SpreadsheetProperties extends \Google\Model
{
  /**
   * Default value. This value must not be used.
   */
  public const AUTO_RECALC_RECALCULATION_INTERVAL_UNSPECIFIED = 'RECALCULATION_INTERVAL_UNSPECIFIED';
  /**
   * Volatile functions are updated on every change.
   */
  public const AUTO_RECALC_ON_CHANGE = 'ON_CHANGE';
  /**
   * Volatile functions are updated on every change and every minute.
   */
  public const AUTO_RECALC_MINUTE = 'MINUTE';
  /**
   * Volatile functions are updated on every change and hourly.
   */
  public const AUTO_RECALC_HOUR = 'HOUR';
  /**
   * The amount of time to wait before volatile functions are recalculated.
   *
   * @var string
   */
  public $autoRecalc;
  protected $defaultFormatType = CellFormat::class;
  protected $defaultFormatDataType = '';
  /**
   * Whether to allow external URL access for image and import functions. Read
   * only when true. When false, you can set to true. This value will be
   * bypassed and always return true if the admin has enabled the [allowlisting
   * feature](https://support.google.com/a?p=url_allowlist).
   *
   * @var bool
   */
  public $importFunctionsExternalUrlAccessAllowed;
  protected $iterativeCalculationSettingsType = IterativeCalculationSettings::class;
  protected $iterativeCalculationSettingsDataType = '';
  /**
   * The locale of the spreadsheet in one of the following formats: * an ISO
   * 639-1 language code such as `en` * an ISO 639-2 language code such as
   * `fil`, if no 639-1 code exists * a combination of the ISO language code and
   * country code, such as `en_US` Note: when updating this field, not all
   * locales/languages are supported.
   *
   * @var string
   */
  public $locale;
  protected $spreadsheetThemeType = SpreadsheetTheme::class;
  protected $spreadsheetThemeDataType = '';
  /**
   * The time zone of the spreadsheet, in CLDR format such as
   * `America/New_York`. If the time zone isn't recognized, this may be a custom
   * time zone such as `GMT-07:00`.
   *
   * @var string
   */
  public $timeZone;
  /**
   * The title of the spreadsheet.
   *
   * @var string
   */
  public $title;

  /**
   * The amount of time to wait before volatile functions are recalculated.
   *
   * Accepted values: RECALCULATION_INTERVAL_UNSPECIFIED, ON_CHANGE, MINUTE,
   * HOUR
   *
   * @param self::AUTO_RECALC_* $autoRecalc
   */
  public function setAutoRecalc($autoRecalc)
  {
    $this->autoRecalc = $autoRecalc;
  }
  /**
   * @return self::AUTO_RECALC_*
   */
  public function getAutoRecalc()
  {
    return $this->autoRecalc;
  }
  /**
   * The default format of all cells in the spreadsheet.
   * CellData.effectiveFormat will not be set if the cell's format is equal to
   * this default format. This field is read-only.
   *
   * @param CellFormat $defaultFormat
   */
  public function setDefaultFormat(CellFormat $defaultFormat)
  {
    $this->defaultFormat = $defaultFormat;
  }
  /**
   * @return CellFormat
   */
  public function getDefaultFormat()
  {
    return $this->defaultFormat;
  }
  /**
   * Whether to allow external URL access for image and import functions. Read
   * only when true. When false, you can set to true. This value will be
   * bypassed and always return true if the admin has enabled the [allowlisting
   * feature](https://support.google.com/a?p=url_allowlist).
   *
   * @param bool $importFunctionsExternalUrlAccessAllowed
   */
  public function setImportFunctionsExternalUrlAccessAllowed($importFunctionsExternalUrlAccessAllowed)
  {
    $this->importFunctionsExternalUrlAccessAllowed = $importFunctionsExternalUrlAccessAllowed;
  }
  /**
   * @return bool
   */
  public function getImportFunctionsExternalUrlAccessAllowed()
  {
    return $this->importFunctionsExternalUrlAccessAllowed;
  }
  /**
   * Determines whether and how circular references are resolved with iterative
   * calculation. Absence of this field means that circular references result in
   * calculation errors.
   *
   * @param IterativeCalculationSettings $iterativeCalculationSettings
   */
  public function setIterativeCalculationSettings(IterativeCalculationSettings $iterativeCalculationSettings)
  {
    $this->iterativeCalculationSettings = $iterativeCalculationSettings;
  }
  /**
   * @return IterativeCalculationSettings
   */
  public function getIterativeCalculationSettings()
  {
    return $this->iterativeCalculationSettings;
  }
  /**
   * The locale of the spreadsheet in one of the following formats: * an ISO
   * 639-1 language code such as `en` * an ISO 639-2 language code such as
   * `fil`, if no 639-1 code exists * a combination of the ISO language code and
   * country code, such as `en_US` Note: when updating this field, not all
   * locales/languages are supported.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Theme applied to the spreadsheet.
   *
   * @param SpreadsheetTheme $spreadsheetTheme
   */
  public function setSpreadsheetTheme(SpreadsheetTheme $spreadsheetTheme)
  {
    $this->spreadsheetTheme = $spreadsheetTheme;
  }
  /**
   * @return SpreadsheetTheme
   */
  public function getSpreadsheetTheme()
  {
    return $this->spreadsheetTheme;
  }
  /**
   * The time zone of the spreadsheet, in CLDR format such as
   * `America/New_York`. If the time zone isn't recognized, this may be a custom
   * time zone such as `GMT-07:00`.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * The title of the spreadsheet.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpreadsheetProperties::class, 'Google_Service_Sheets_SpreadsheetProperties');
