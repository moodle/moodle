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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction extends \Google\Model
{
  protected $structuredFormatType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat::class;
  protected $structuredFormatDataType = '';
  protected $textType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText::class;
  protected $textDataType = '';

  /**
   * A breakdown of the query prediction into main text containing the query and
   * secondary text containing additional disambiguating features (such as a
   * city or region). `structured_format` is recommended for developers who wish
   * to show two separate, but related, UI elements. Developers who wish to show
   * a single UI element may want to use `text` instead. They are two different
   * ways to represent a query prediction. Users should not try to parse
   * `structured_format` into `text` or vice versa.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat $structuredFormat
   */
  public function setStructuredFormat(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat $structuredFormat)
  {
    $this->structuredFormat = $structuredFormat;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat
   */
  public function getStructuredFormat()
  {
    return $this->structuredFormat;
  }
  /**
   * The predicted text. This text does not represent a Place, but rather a text
   * query that could be used in a search endpoint (for example, Text Search).
   * `text` is recommended for developers who wish to show a single UI element.
   * Developers who wish to show two separate, but related, UI elements may want
   * to use `structured_format` instead. They are two different ways to
   * represent a query prediction. Users should not try to parse
   * `structured_format` into `text` or vice versa. May be in mixed languages if
   * the request `input` and `language_code` are in different languages or if
   * part of the query does not have a translation from the local language to
   * `language_code`.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $text
   */
  public function setText(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction');
