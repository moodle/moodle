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

namespace Google\Service\CustomSearchAPI;

class SearchQueriesNextPage extends \Google\Model
{
  /**
   * Number of search results returned in this set.
   *
   * @var int
   */
  public $count;
  /**
   * Restricts search results to documents originating in a particular country.
   * You may use [Boolean operators](https://developers.google.com/custom-
   * search/docs/json_api_reference#BooleanOrSearch) in the `cr` parameter's
   * value. Google WebSearch determines the country of a document by analyzing
   * the following: * The top-level domain (TLD) of the document's URL. * The
   * geographic location of the web server's IP address. See [Country (cr)
   * Parameter Values](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCollections) for a list of valid
   * values for this parameter.
   *
   * @var string
   */
  public $cr;
  /**
   * The identifier of an engine created using the Programmable Search Engine
   * [Control Panel](https://programmablesearchengine.google.com/). This is a
   * custom property not defined in the OpenSearch spec. This parameter is
   * **required**.
   *
   * @var string
   */
  public $cx;
  /**
   * Restricts results to URLs based on date. Supported values include: *
   * `d[number]`: requests results from the specified number of past days. *
   * `w[number]`: requests results from the specified number of past weeks. *
   * `m[number]`: requests results from the specified number of past months. *
   * `y[number]`: requests results from the specified number of past years.
   *
   * @var string
   */
  public $dateRestrict;
  /**
   * Enables or disables the [Simplified and Traditional Chinese
   * Search](https://developers.google.com/custom-
   * search/docs/json_api_reference#chineseSearch) feature. Supported values
   * are: * `0`: enabled (default) * `1`: disabled
   *
   * @var string
   */
  public $disableCnTwTranslation;
  /**
   * Identifies a phrase that all documents in the search results must contain.
   *
   * @var string
   */
  public $exactTerms;
  /**
   * Identifies a word or phrase that should not appear in any documents in the
   * search results.
   *
   * @var string
   */
  public $excludeTerms;
  /**
   * Restricts results to files of a specified extension. Filetypes supported by
   * Google include: * Adobe Portable Document Format (`pdf`) * Adobe PostScript
   * (`ps`) * Lotus 1-2-3 (`wk1`, `wk2`, `wk3`, `wk4`, `wk5`, `wki`, `wks`,
   * `wku`) * Lotus WordPro (`lwp`) * Macwrite (`mw`) * Microsoft Excel (`xls`)
   * * Microsoft PowerPoint (`ppt`) * Microsoft Word (`doc`) * Microsoft Works
   * (`wks`, `wps`, `wdb`) * Microsoft Write (`wri`) * Rich Text Format (`rtf`)
   * * Shockwave Flash (`swf`) * Text (`ans`, `txt`). Additional filetypes may
   * be added in the future. An up-to-date list can always be found in Google's
   * [file type FAQ](https://support.google.com/webmasters/answer/35287).
   *
   * @var string
   */
  public $fileType;
  /**
   * Activates or deactivates the automatic filtering of Google search results.
   * See [Automatic Filtering](https://developers.google.com/custom-
   * search/docs/json_api_reference#automaticFiltering) for more information
   * about Google's search results filters. Valid values for this parameter are:
   * * `0`: Disabled * `1`: Enabled (default) **Note**: By default, Google
   * applies filtering to all search results to improve the quality of those
   * results.
   *
   * @var string
   */
  public $filter;
  /**
   * Boosts search results whose country of origin matches the parameter value.
   * See [Country Codes](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCodes) for a list of valid values.
   * Specifying a `gl` parameter value in WebSearch requests should improve the
   * relevance of results. This is particularly true for international customers
   * and, even more specifically, for customers in English-speaking countries
   * other than the United States.
   *
   * @var string
   */
  public $gl;
  /**
   * Specifies the Google domain (for example, google.com, google.de, or
   * google.fr) to which the search should be limited.
   *
   * @var string
   */
  public $googleHost;
  /**
   * Specifies the ending value for a search range. Use `cse:lowRange` and
   * `cse:highrange` to append an inclusive search range of
   * `lowRange...highRange` to the query.
   *
   * @var string
   */
  public $highRange;
  /**
   * Specifies the interface language (host language) of your user interface.
   * Explicitly setting this parameter improves the performance and the quality
   * of your search results. See the [Interface
   * Languages](https://developers.google.com/custom-
   * search/docs/json_api_reference#wsInterfaceLanguages) section of
   * [Internationalizing Queries and Results
   * Presentation](https://developers.google.com/custom-
   * search/docs/json_api_reference#wsInternationalizing) for more information,
   * and [Supported Interface Languages](https://developers.google.com/custom-
   * search/docs/json_api_reference#interfaceLanguages) for a list of supported
   * languages.
   *
   * @var string
   */
  public $hl;
  /**
   * Appends the specified query terms to the query, as if they were combined
   * with a logical `AND` operator.
   *
   * @var string
   */
  public $hq;
  /**
   * Restricts results to images of a specified color type. Supported values
   * are: * `mono` (black and white) * `gray` (grayscale) * `color` (color)
   *
   * @var string
   */
  public $imgColorType;
  /**
   * Restricts results to images with a specific dominant color. Supported
   * values are: * `red` * `orange` * `yellow` * `green` * `teal` * `blue` *
   * `purple` * `pink` * `white` * `gray` * `black` * `brown`
   *
   * @var string
   */
  public $imgDominantColor;
  /**
   * Restricts results to images of a specified size. Supported values are: *
   * `icon` (small) * `small | medium | large | xlarge` (medium) * `xxlarge`
   * (large) * `huge` (extra-large)
   *
   * @var string
   */
  public $imgSize;
  /**
   * Restricts results to images of a specified type. Supported values are: *
   * `clipart` (Clip art) * `face` (Face) * `lineart` (Line drawing) * `photo`
   * (Photo) * `animated` (Animated) * `stock` (Stock)
   *
   * @var string
   */
  public $imgType;
  /**
   * The character encoding supported for search requests.
   *
   * @var string
   */
  public $inputEncoding;
  /**
   * The language of the search results.
   *
   * @var string
   */
  public $language;
  /**
   * Specifies that all results should contain a link to a specific URL.
   *
   * @var string
   */
  public $linkSite;
  /**
   * Specifies the starting value for a search range. Use `cse:lowRange` and
   * `cse:highrange` to append an inclusive search range of
   * `lowRange...highRange` to the query.
   *
   * @var string
   */
  public $lowRange;
  /**
   * Provides additional search terms to check for in a document, where each
   * document in the search results must contain at least one of the additional
   * search terms. You can also use the [Boolean
   * OR](https://developers.google.com/custom-
   * search/docs/json_api_reference#BooleanOrSearch) query term for this type of
   * query.
   *
   * @var string
   */
  public $orTerms;
  /**
   * The character encoding supported for search results.
   *
   * @var string
   */
  public $outputEncoding;
  /**
   * Specifies that all search results should be pages that are related to the
   * specified URL. The parameter value should be a URL.
   *
   * @var string
   */
  public $relatedSite;
  /**
   * Filters based on licensing. Supported values include: * `cc_publicdomain` *
   * `cc_attribute` * `cc_sharealike` * `cc_noncommercial` * `cc_nonderived`
   *
   * @var string
   */
  public $rights;
  /**
   * Specifies the [SafeSearch level](https://developers.google.com/custom-
   * search/docs/json_api_reference#safeSearchLevels) used for filtering out
   * adult results. This is a custom property not defined in the OpenSearch
   * spec. Valid parameter values are: * `"off"`: Disable SafeSearch *
   * `"active"`: Enable SafeSearch
   *
   * @var string
   */
  public $safe;
  /**
   * The search terms entered by the user.
   *
   * @var string
   */
  public $searchTerms;
  /**
   * Allowed values are `web` or `image`. If unspecified, results are limited to
   * webpages.
   *
   * @var string
   */
  public $searchType;
  /**
   * Restricts results to URLs from a specified site.
   *
   * @var string
   */
  public $siteSearch;
  /**
   * Specifies whether to include or exclude results from the site named in the
   * `sitesearch` parameter. Supported values are: * `i`: include content from
   * site * `e`: exclude content from site
   *
   * @var string
   */
  public $siteSearchFilter;
  /**
   * Specifies that results should be sorted according to the specified
   * expression. For example, sort by date.
   *
   * @var string
   */
  public $sort;
  /**
   * The index of the current set of search results into the total set of
   * results, where the index of the first result is 1.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The page number of this set of results, where the page length is set by the
   * `count` property.
   *
   * @var int
   */
  public $startPage;
  /**
   * A description of the query.
   *
   * @var string
   */
  public $title;
  /**
   * Estimated number of total search results. May not be accurate.
   *
   * @var string
   */
  public $totalResults;

  /**
   * Number of search results returned in this set.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Restricts search results to documents originating in a particular country.
   * You may use [Boolean operators](https://developers.google.com/custom-
   * search/docs/json_api_reference#BooleanOrSearch) in the `cr` parameter's
   * value. Google WebSearch determines the country of a document by analyzing
   * the following: * The top-level domain (TLD) of the document's URL. * The
   * geographic location of the web server's IP address. See [Country (cr)
   * Parameter Values](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCollections) for a list of valid
   * values for this parameter.
   *
   * @param string $cr
   */
  public function setCr($cr)
  {
    $this->cr = $cr;
  }
  /**
   * @return string
   */
  public function getCr()
  {
    return $this->cr;
  }
  /**
   * The identifier of an engine created using the Programmable Search Engine
   * [Control Panel](https://programmablesearchengine.google.com/). This is a
   * custom property not defined in the OpenSearch spec. This parameter is
   * **required**.
   *
   * @param string $cx
   */
  public function setCx($cx)
  {
    $this->cx = $cx;
  }
  /**
   * @return string
   */
  public function getCx()
  {
    return $this->cx;
  }
  /**
   * Restricts results to URLs based on date. Supported values include: *
   * `d[number]`: requests results from the specified number of past days. *
   * `w[number]`: requests results from the specified number of past weeks. *
   * `m[number]`: requests results from the specified number of past months. *
   * `y[number]`: requests results from the specified number of past years.
   *
   * @param string $dateRestrict
   */
  public function setDateRestrict($dateRestrict)
  {
    $this->dateRestrict = $dateRestrict;
  }
  /**
   * @return string
   */
  public function getDateRestrict()
  {
    return $this->dateRestrict;
  }
  /**
   * Enables or disables the [Simplified and Traditional Chinese
   * Search](https://developers.google.com/custom-
   * search/docs/json_api_reference#chineseSearch) feature. Supported values
   * are: * `0`: enabled (default) * `1`: disabled
   *
   * @param string $disableCnTwTranslation
   */
  public function setDisableCnTwTranslation($disableCnTwTranslation)
  {
    $this->disableCnTwTranslation = $disableCnTwTranslation;
  }
  /**
   * @return string
   */
  public function getDisableCnTwTranslation()
  {
    return $this->disableCnTwTranslation;
  }
  /**
   * Identifies a phrase that all documents in the search results must contain.
   *
   * @param string $exactTerms
   */
  public function setExactTerms($exactTerms)
  {
    $this->exactTerms = $exactTerms;
  }
  /**
   * @return string
   */
  public function getExactTerms()
  {
    return $this->exactTerms;
  }
  /**
   * Identifies a word or phrase that should not appear in any documents in the
   * search results.
   *
   * @param string $excludeTerms
   */
  public function setExcludeTerms($excludeTerms)
  {
    $this->excludeTerms = $excludeTerms;
  }
  /**
   * @return string
   */
  public function getExcludeTerms()
  {
    return $this->excludeTerms;
  }
  /**
   * Restricts results to files of a specified extension. Filetypes supported by
   * Google include: * Adobe Portable Document Format (`pdf`) * Adobe PostScript
   * (`ps`) * Lotus 1-2-3 (`wk1`, `wk2`, `wk3`, `wk4`, `wk5`, `wki`, `wks`,
   * `wku`) * Lotus WordPro (`lwp`) * Macwrite (`mw`) * Microsoft Excel (`xls`)
   * * Microsoft PowerPoint (`ppt`) * Microsoft Word (`doc`) * Microsoft Works
   * (`wks`, `wps`, `wdb`) * Microsoft Write (`wri`) * Rich Text Format (`rtf`)
   * * Shockwave Flash (`swf`) * Text (`ans`, `txt`). Additional filetypes may
   * be added in the future. An up-to-date list can always be found in Google's
   * [file type FAQ](https://support.google.com/webmasters/answer/35287).
   *
   * @param string $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return string
   */
  public function getFileType()
  {
    return $this->fileType;
  }
  /**
   * Activates or deactivates the automatic filtering of Google search results.
   * See [Automatic Filtering](https://developers.google.com/custom-
   * search/docs/json_api_reference#automaticFiltering) for more information
   * about Google's search results filters. Valid values for this parameter are:
   * * `0`: Disabled * `1`: Enabled (default) **Note**: By default, Google
   * applies filtering to all search results to improve the quality of those
   * results.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Boosts search results whose country of origin matches the parameter value.
   * See [Country Codes](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCodes) for a list of valid values.
   * Specifying a `gl` parameter value in WebSearch requests should improve the
   * relevance of results. This is particularly true for international customers
   * and, even more specifically, for customers in English-speaking countries
   * other than the United States.
   *
   * @param string $gl
   */
  public function setGl($gl)
  {
    $this->gl = $gl;
  }
  /**
   * @return string
   */
  public function getGl()
  {
    return $this->gl;
  }
  /**
   * Specifies the Google domain (for example, google.com, google.de, or
   * google.fr) to which the search should be limited.
   *
   * @param string $googleHost
   */
  public function setGoogleHost($googleHost)
  {
    $this->googleHost = $googleHost;
  }
  /**
   * @return string
   */
  public function getGoogleHost()
  {
    return $this->googleHost;
  }
  /**
   * Specifies the ending value for a search range. Use `cse:lowRange` and
   * `cse:highrange` to append an inclusive search range of
   * `lowRange...highRange` to the query.
   *
   * @param string $highRange
   */
  public function setHighRange($highRange)
  {
    $this->highRange = $highRange;
  }
  /**
   * @return string
   */
  public function getHighRange()
  {
    return $this->highRange;
  }
  /**
   * Specifies the interface language (host language) of your user interface.
   * Explicitly setting this parameter improves the performance and the quality
   * of your search results. See the [Interface
   * Languages](https://developers.google.com/custom-
   * search/docs/json_api_reference#wsInterfaceLanguages) section of
   * [Internationalizing Queries and Results
   * Presentation](https://developers.google.com/custom-
   * search/docs/json_api_reference#wsInternationalizing) for more information,
   * and [Supported Interface Languages](https://developers.google.com/custom-
   * search/docs/json_api_reference#interfaceLanguages) for a list of supported
   * languages.
   *
   * @param string $hl
   */
  public function setHl($hl)
  {
    $this->hl = $hl;
  }
  /**
   * @return string
   */
  public function getHl()
  {
    return $this->hl;
  }
  /**
   * Appends the specified query terms to the query, as if they were combined
   * with a logical `AND` operator.
   *
   * @param string $hq
   */
  public function setHq($hq)
  {
    $this->hq = $hq;
  }
  /**
   * @return string
   */
  public function getHq()
  {
    return $this->hq;
  }
  /**
   * Restricts results to images of a specified color type. Supported values
   * are: * `mono` (black and white) * `gray` (grayscale) * `color` (color)
   *
   * @param string $imgColorType
   */
  public function setImgColorType($imgColorType)
  {
    $this->imgColorType = $imgColorType;
  }
  /**
   * @return string
   */
  public function getImgColorType()
  {
    return $this->imgColorType;
  }
  /**
   * Restricts results to images with a specific dominant color. Supported
   * values are: * `red` * `orange` * `yellow` * `green` * `teal` * `blue` *
   * `purple` * `pink` * `white` * `gray` * `black` * `brown`
   *
   * @param string $imgDominantColor
   */
  public function setImgDominantColor($imgDominantColor)
  {
    $this->imgDominantColor = $imgDominantColor;
  }
  /**
   * @return string
   */
  public function getImgDominantColor()
  {
    return $this->imgDominantColor;
  }
  /**
   * Restricts results to images of a specified size. Supported values are: *
   * `icon` (small) * `small | medium | large | xlarge` (medium) * `xxlarge`
   * (large) * `huge` (extra-large)
   *
   * @param string $imgSize
   */
  public function setImgSize($imgSize)
  {
    $this->imgSize = $imgSize;
  }
  /**
   * @return string
   */
  public function getImgSize()
  {
    return $this->imgSize;
  }
  /**
   * Restricts results to images of a specified type. Supported values are: *
   * `clipart` (Clip art) * `face` (Face) * `lineart` (Line drawing) * `photo`
   * (Photo) * `animated` (Animated) * `stock` (Stock)
   *
   * @param string $imgType
   */
  public function setImgType($imgType)
  {
    $this->imgType = $imgType;
  }
  /**
   * @return string
   */
  public function getImgType()
  {
    return $this->imgType;
  }
  /**
   * The character encoding supported for search requests.
   *
   * @param string $inputEncoding
   */
  public function setInputEncoding($inputEncoding)
  {
    $this->inputEncoding = $inputEncoding;
  }
  /**
   * @return string
   */
  public function getInputEncoding()
  {
    return $this->inputEncoding;
  }
  /**
   * The language of the search results.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Specifies that all results should contain a link to a specific URL.
   *
   * @param string $linkSite
   */
  public function setLinkSite($linkSite)
  {
    $this->linkSite = $linkSite;
  }
  /**
   * @return string
   */
  public function getLinkSite()
  {
    return $this->linkSite;
  }
  /**
   * Specifies the starting value for a search range. Use `cse:lowRange` and
   * `cse:highrange` to append an inclusive search range of
   * `lowRange...highRange` to the query.
   *
   * @param string $lowRange
   */
  public function setLowRange($lowRange)
  {
    $this->lowRange = $lowRange;
  }
  /**
   * @return string
   */
  public function getLowRange()
  {
    return $this->lowRange;
  }
  /**
   * Provides additional search terms to check for in a document, where each
   * document in the search results must contain at least one of the additional
   * search terms. You can also use the [Boolean
   * OR](https://developers.google.com/custom-
   * search/docs/json_api_reference#BooleanOrSearch) query term for this type of
   * query.
   *
   * @param string $orTerms
   */
  public function setOrTerms($orTerms)
  {
    $this->orTerms = $orTerms;
  }
  /**
   * @return string
   */
  public function getOrTerms()
  {
    return $this->orTerms;
  }
  /**
   * The character encoding supported for search results.
   *
   * @param string $outputEncoding
   */
  public function setOutputEncoding($outputEncoding)
  {
    $this->outputEncoding = $outputEncoding;
  }
  /**
   * @return string
   */
  public function getOutputEncoding()
  {
    return $this->outputEncoding;
  }
  /**
   * Specifies that all search results should be pages that are related to the
   * specified URL. The parameter value should be a URL.
   *
   * @param string $relatedSite
   */
  public function setRelatedSite($relatedSite)
  {
    $this->relatedSite = $relatedSite;
  }
  /**
   * @return string
   */
  public function getRelatedSite()
  {
    return $this->relatedSite;
  }
  /**
   * Filters based on licensing. Supported values include: * `cc_publicdomain` *
   * `cc_attribute` * `cc_sharealike` * `cc_noncommercial` * `cc_nonderived`
   *
   * @param string $rights
   */
  public function setRights($rights)
  {
    $this->rights = $rights;
  }
  /**
   * @return string
   */
  public function getRights()
  {
    return $this->rights;
  }
  /**
   * Specifies the [SafeSearch level](https://developers.google.com/custom-
   * search/docs/json_api_reference#safeSearchLevels) used for filtering out
   * adult results. This is a custom property not defined in the OpenSearch
   * spec. Valid parameter values are: * `"off"`: Disable SafeSearch *
   * `"active"`: Enable SafeSearch
   *
   * @param string $safe
   */
  public function setSafe($safe)
  {
    $this->safe = $safe;
  }
  /**
   * @return string
   */
  public function getSafe()
  {
    return $this->safe;
  }
  /**
   * The search terms entered by the user.
   *
   * @param string $searchTerms
   */
  public function setSearchTerms($searchTerms)
  {
    $this->searchTerms = $searchTerms;
  }
  /**
   * @return string
   */
  public function getSearchTerms()
  {
    return $this->searchTerms;
  }
  /**
   * Allowed values are `web` or `image`. If unspecified, results are limited to
   * webpages.
   *
   * @param string $searchType
   */
  public function setSearchType($searchType)
  {
    $this->searchType = $searchType;
  }
  /**
   * @return string
   */
  public function getSearchType()
  {
    return $this->searchType;
  }
  /**
   * Restricts results to URLs from a specified site.
   *
   * @param string $siteSearch
   */
  public function setSiteSearch($siteSearch)
  {
    $this->siteSearch = $siteSearch;
  }
  /**
   * @return string
   */
  public function getSiteSearch()
  {
    return $this->siteSearch;
  }
  /**
   * Specifies whether to include or exclude results from the site named in the
   * `sitesearch` parameter. Supported values are: * `i`: include content from
   * site * `e`: exclude content from site
   *
   * @param string $siteSearchFilter
   */
  public function setSiteSearchFilter($siteSearchFilter)
  {
    $this->siteSearchFilter = $siteSearchFilter;
  }
  /**
   * @return string
   */
  public function getSiteSearchFilter()
  {
    return $this->siteSearchFilter;
  }
  /**
   * Specifies that results should be sorted according to the specified
   * expression. For example, sort by date.
   *
   * @param string $sort
   */
  public function setSort($sort)
  {
    $this->sort = $sort;
  }
  /**
   * @return string
   */
  public function getSort()
  {
    return $this->sort;
  }
  /**
   * The index of the current set of search results into the total set of
   * results, where the index of the first result is 1.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The page number of this set of results, where the page length is set by the
   * `count` property.
   *
   * @param int $startPage
   */
  public function setStartPage($startPage)
  {
    $this->startPage = $startPage;
  }
  /**
   * @return int
   */
  public function getStartPage()
  {
    return $this->startPage;
  }
  /**
   * A description of the query.
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
  /**
   * Estimated number of total search results. May not be accurate.
   *
   * @param string $totalResults
   */
  public function setTotalResults($totalResults)
  {
    $this->totalResults = $totalResults;
  }
  /**
   * @return string
   */
  public function getTotalResults()
  {
    return $this->totalResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchQueriesNextPage::class, 'Google_Service_CustomSearchAPI_SearchQueriesNextPage');
