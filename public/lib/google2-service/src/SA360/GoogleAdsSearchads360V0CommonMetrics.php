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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonMetrics extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const HISTORICAL_CREATIVE_QUALITY_SCORE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const HISTORICAL_CREATIVE_QUALITY_SCORE_UNKNOWN = 'UNKNOWN';
  /**
   * Quality of the creative is below average.
   */
  public const HISTORICAL_CREATIVE_QUALITY_SCORE_BELOW_AVERAGE = 'BELOW_AVERAGE';
  /**
   * Quality of the creative is average.
   */
  public const HISTORICAL_CREATIVE_QUALITY_SCORE_AVERAGE = 'AVERAGE';
  /**
   * Quality of the creative is above average.
   */
  public const HISTORICAL_CREATIVE_QUALITY_SCORE_ABOVE_AVERAGE = 'ABOVE_AVERAGE';
  /**
   * Not specified.
   */
  public const HISTORICAL_LANDING_PAGE_QUALITY_SCORE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const HISTORICAL_LANDING_PAGE_QUALITY_SCORE_UNKNOWN = 'UNKNOWN';
  /**
   * Quality of the creative is below average.
   */
  public const HISTORICAL_LANDING_PAGE_QUALITY_SCORE_BELOW_AVERAGE = 'BELOW_AVERAGE';
  /**
   * Quality of the creative is average.
   */
  public const HISTORICAL_LANDING_PAGE_QUALITY_SCORE_AVERAGE = 'AVERAGE';
  /**
   * Quality of the creative is above average.
   */
  public const HISTORICAL_LANDING_PAGE_QUALITY_SCORE_ABOVE_AVERAGE = 'ABOVE_AVERAGE';
  /**
   * Not specified.
   */
  public const HISTORICAL_SEARCH_PREDICTED_CTR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const HISTORICAL_SEARCH_PREDICTED_CTR_UNKNOWN = 'UNKNOWN';
  /**
   * Quality of the creative is below average.
   */
  public const HISTORICAL_SEARCH_PREDICTED_CTR_BELOW_AVERAGE = 'BELOW_AVERAGE';
  /**
   * Quality of the creative is average.
   */
  public const HISTORICAL_SEARCH_PREDICTED_CTR_AVERAGE = 'AVERAGE';
  /**
   * Quality of the creative is above average.
   */
  public const HISTORICAL_SEARCH_PREDICTED_CTR_ABOVE_AVERAGE = 'ABOVE_AVERAGE';
  protected $collection_key = 'rawEventConversionMetrics';
  /**
   * Search absolute top impression share is the percentage of your Search ad
   * impressions that are shown in the most prominent Search position.
   *
   * @var 
   */
  public $absoluteTopImpressionPercentage;
  /**
   * The total number of conversions. This includes all conversions regardless
   * of the value of include_in_conversions_metric.
   *
   * @var 
   */
  public $allConversions;
  /**
   * The total number of conversions. This includes all conversions regardless
   * of the value of include_in_conversions_metric. When this column is selected
   * with date, the values in date column means the conversion date. Details for
   * the by_conversion_date columns are available at
   * https://support.google.com/sa360/answer/9250611.
   *
   * @var 
   */
  public $allConversionsByConversionDate;
  /**
   * The number of times people clicked the "Call" button to call a store during
   * or after clicking an ad. This number doesn't include whether or not calls
   * were connected, or the duration of any calls. This metric applies to feed
   * items only.
   *
   * @var 
   */
  public $allConversionsFromClickToCall;
  /**
   * The number of times people clicked a "Get directions" button to navigate to
   * a store after clicking an ad. This metric applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromDirections;
  /**
   * All conversions from interactions (as oppose to view through conversions)
   * divided by the number of ad interactions.
   *
   * @var 
   */
  public $allConversionsFromInteractionsRate;
  /**
   * The value of all conversions from interactions divided by the total number
   * of interactions.
   *
   * @var 
   */
  public $allConversionsFromInteractionsValuePerInteraction;
  /**
   * The number of times people clicked a link to view a store's menu after
   * clicking an ad. This metric applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromMenu;
  /**
   * The number of times people placed an order at a store after clicking an ad.
   * This metric applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromOrder;
  /**
   * The number of other conversions (for example, posting a review or saving a
   * location for a store) that occurred after people clicked an ad. This metric
   * applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromOtherEngagement;
  /**
   * Estimated number of times people visited a store after clicking an ad. This
   * metric applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromStoreVisit;
  /**
   * The number of times that people were taken to a store's URL after clicking
   * an ad. This metric applies to feed items only.
   *
   * @var 
   */
  public $allConversionsFromStoreWebsite;
  /**
   * The value of all conversions.
   *
   * @var 
   */
  public $allConversionsValue;
  /**
   * The value of all conversions. When this column is selected with date, the
   * values in date column means the conversion date. Details for the
   * by_conversion_date columns are available at
   * https://support.google.com/sa360/answer/9250611.
   *
   * @var 
   */
  public $allConversionsValueByConversionDate;
  /**
   * The value of all conversions divided by the total cost of ad interactions
   * (such as clicks for text ads or views for video ads).
   *
   * @var 
   */
  public $allConversionsValuePerCost;
  /**
   * Average cart size is the average number of products in each order
   * attributed to your ads. How it works: You report conversions with cart data
   * for completed purchases on your website. Average cart size is the total
   * number of products sold divided by the total number of orders you received.
   * Example: You received 2 orders, the first included 3 products and the
   * second included 2. The average cart size is 2.5 products = (3+2)/2. This
   * metric is only available if you report conversions with cart data.
   *
   * @var 
   */
  public $averageCartSize;
  /**
   * The average amount you pay per interaction. This amount is the total cost
   * of your ads divided by the total number of interactions.
   *
   * @var 
   */
  public $averageCost;
  /**
   * The total cost of all clicks divided by the total number of clicks
   * received. This metric is a monetary value and returned in the customer's
   * currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var 
   */
  public $averageCpc;
  /**
   * Average cost-per-thousand impressions (CPM). This metric is a monetary
   * value and returned in the customer's currency by default. See the
   * metrics_currency parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var 
   */
  public $averageCpm;
  /**
   * The average number of times a unique user saw your ad during the requested
   * time period. This metric cannot be aggregated, and can only be requested
   * for date ranges of 92 days or less. This metric is available for following
   * campaign types - Display, Video, Discovery and App.
   *
   * @var 
   */
  public $averageImpressionFrequencyPerUser;
  /**
   * Average order value is the average revenue you made per order attributed to
   * your ads. How it works: You report conversions with cart data for completed
   * purchases on your website. Average order value is the total revenue from
   * your orders divided by the total number of orders. Example: You received 3
   * orders which made $10, $15 and $20 worth of revenue. The average order
   * value is $15 = ($10 + $15 + $20)/3. This metric is only available if you
   * report conversions with cart data.
   *
   * @var string
   */
  public $averageOrderValueMicros;
  /**
   * The average quality score.
   *
   * @var 
   */
  public $averageQualityScore;
  /**
   * The number of clicks.
   *
   * @var string
   */
  public $clicks;
  /**
   * The number of client account conversions. This only includes conversion
   * actions which include_in_client_account_conversions_metric attribute is set
   * to true. If you use conversion-based bidding, your bid strategies will
   * optimize for these conversions.
   *
   * @var 
   */
  public $clientAccountConversions;
  /**
   * The value of client account conversions. This only includes conversion
   * actions which include_in_client_account_conversions_metric attribute is set
   * to true. If you use conversion-based bidding, your bid strategies will
   * optimize for these conversions.
   *
   * @var 
   */
  public $clientAccountConversionsValue;
  /**
   * Client account cross-sell cost of goods sold (COGS) is the total cost of
   * products sold as a result of advertising a different product. How it works:
   * You report conversions with cart data for completed purchases on your
   * website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If these products don't match then this is considered
   * cross-sell. Cross-sell cost of goods sold is the total cost of the products
   * sold that weren't advertised. Example: Someone clicked on a Shopping ad for
   * a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The
   * cross-sell cost of goods sold for this order is $5. This metric is only
   * available if you report conversions with cart data. This metric is a
   * monetary value and returned in the customer's currency by default. See the
   * metrics_currency parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountCrossSellCostOfGoodsSoldMicros;
  /**
   * Client account cross-sell gross profit is the profit you made from products
   * sold as a result of advertising a different product, minus cost of goods
   * sold (COGS). How it works: You report conversions with cart data for
   * completed purchases on your website. If the ad that was interacted with
   * before the purchase has an associated product (see Shopping Ads) then this
   * product is considered the advertised product. Any product included in the
   * purchase is a sold product. If these products don't match then this is
   * considered cross-sell. Cross-sell gross profit is the revenue you made from
   * cross-sell attributed to your ads minus the cost of the goods sold.
   * Example: Someone clicked on a Shopping ad for a hat then bought the same
   * hat and a shirt. The shirt is priced $20 and has a cost of goods sold value
   * of $5. The cross-sell gross profit of this order is $15 = $20 - $5. This
   * metric is only available if you report conversions with cart data. This
   * metric is a monetary value and returned in the customer's currency by
   * default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountCrossSellGrossProfitMicros;
  /**
   * Client account cross-sell revenue is the total amount you made from
   * products sold as a result of advertising a different product. How it works:
   * You report conversions with cart data for completed purchases on your
   * website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If these products don't match then this is considered
   * cross-sell. Cross-sell revenue is the total value you made from cross-sell
   * attributed to your ads. Example: Someone clicked on a Shopping ad for a hat
   * then bought the same hat and a shirt. The hat is priced $10 and the shirt
   * is priced $20. The cross-sell revenue of this order is $20. This metric is
   * only available if you report conversions with cart data. This metric is a
   * monetary value and returned in the customer's currency by default. See the
   * metrics_currency parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountCrossSellRevenueMicros;
  /**
   * Client account cross-sell units sold is the total number of products sold
   * as a result of advertising a different product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * these products don't match then this is considered cross-sell. Cross-sell
   * units sold is the total number of cross-sold products from all orders
   * attributed to your ads. Example: Someone clicked on a Shopping ad for a hat
   * then bought the same hat, a shirt and a jacket. The cross-sell units sold
   * in this order is 2. This metric is only available if you report conversions
   * with cart data.
   *
   * @var 
   */
  public $clientAccountCrossSellUnitsSold;
  /**
   * Client account lead cost of goods sold (COGS) is the total cost of products
   * sold as a result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with has an associated product (see Shopping Ads)
   * then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the cost of these goods is counted
   * under lead cost of goods sold. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The lead
   * cost of goods sold for this order is $3. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountLeadCostOfGoodsSoldMicros;
  /**
   * Client account lead gross profit is the profit you made from products sold
   * as a result of advertising the same product, minus cost of goods sold
   * (COGS). How it works: You report conversions with cart data for completed
   * purchases on your website. If the ad that was interacted with before the
   * purchase has an associated product (see Shopping Ads) then this product is
   * considered the advertised product. Any product included in the order the
   * customer places is a sold product. If the advertised and sold products
   * match, then the revenue you made from these sales minus the cost of goods
   * sold is your lead gross profit. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat is priced $10 and
   * has a cost of goods sold value of $3. The lead gross profit of this order
   * is $7 = $10 - $3. This metric is only available if you report conversions
   * with cart data. This metric is a monetary value and returned in the
   * customer's currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountLeadGrossProfitMicros;
  /**
   * Client account lead revenue is the total amount you made from products sold
   * as a result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * the advertised and sold products match, then the total value you made from
   * the sales of these products is shown under lead revenue. Example: Someone
   * clicked on a Shopping ad for a hat then bought the same hat and a shirt.
   * The hat is priced $10 and the shirt is priced $20. The lead revenue of this
   * order is $10. This metric is only available if you report conversions with
   * cart data. This metric is a monetary value and returned in the customer's
   * currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $clientAccountLeadRevenueMicros;
  /**
   * Client account lead units sold is the total number of products sold as a
   * result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * the advertised and sold products match, then the total number of these
   * products sold is shown under lead units sold. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat, a shirt and a jacket. The
   * lead units sold in this order is 1. This metric is only available if you
   * report conversions with cart data.
   *
   * @var 
   */
  public $clientAccountLeadUnitsSold;
  /**
   * The total number of view-through conversions. These happen when a customer
   * sees an image or rich media ad, then later completes a conversion on your
   * site without interacting with (for example, clicking on) another ad.
   *
   * @var string
   */
  public $clientAccountViewThroughConversions;
  /**
   * The estimated percent of times that your ad was eligible to show on the
   * Display Network but didn't because your budget was too low. Note: Content
   * budget lost impression share is reported in the range of 0 to 0.9. Any
   * value above 0.9 is reported as 0.9001.
   *
   * @var 
   */
  public $contentBudgetLostImpressionShare;
  /**
   * The impressions you've received on the Display Network divided by the
   * estimated number of impressions you were eligible to receive. Note: Content
   * impression share is reported in the range of 0.1 to 1. Any value below 0.1
   * is reported as 0.0999.
   *
   * @var 
   */
  public $contentImpressionShare;
  /**
   * The estimated percentage of impressions on the Display Network that your
   * ads didn't receive due to poor Ad Rank. Note: Content rank lost impression
   * share is reported in the range of 0 to 0.9. Any value above 0.9 is reported
   * as 0.9001.
   *
   * @var 
   */
  public $contentRankLostImpressionShare;
  protected $conversionCustomMetricsType = GoogleAdsSearchads360V0CommonValue::class;
  protected $conversionCustomMetricsDataType = 'array';
  /**
   * The number of conversions. This only includes conversion actions which
   * include_in_conversions_metric attribute is set to true. If you use
   * conversion-based bidding, your bid strategies will optimize for these
   * conversions.
   *
   * @var 
   */
  public $conversions;
  /**
   * The sum of conversions by conversion date for biddable conversion types.
   * Can be fractional due to attribution modeling. When this column is selected
   * with date, the values in date column means the conversion date.
   *
   * @var 
   */
  public $conversionsByConversionDate;
  /**
   * Average biddable conversions (from interaction) per conversion eligible
   * interaction. Shows how often, on average, an ad interaction leads to a
   * biddable conversion.
   *
   * @var 
   */
  public $conversionsFromInteractionsRate;
  /**
   * The value of conversions from interactions divided by the number of ad
   * interactions. This only includes conversion actions which
   * include_in_conversions_metric attribute is set to true. If you use
   * conversion-based bidding, your bid strategies will optimize for these
   * conversions.
   *
   * @var 
   */
  public $conversionsFromInteractionsValuePerInteraction;
  /**
   * The sum of conversion values for the conversions included in the
   * "conversions" field. This metric is useful only if you entered a value for
   * your conversion actions.
   *
   * @var 
   */
  public $conversionsValue;
  /**
   * The sum of biddable conversions value by conversion date. When this column
   * is selected with date, the values in date column means the conversion date.
   *
   * @var 
   */
  public $conversionsValueByConversionDate;
  /**
   * The value of biddable conversion divided by the total cost of conversion
   * eligible interactions.
   *
   * @var 
   */
  public $conversionsValuePerCost;
  /**
   * The sum of your cost-per-click (CPC) and cost-per-thousand impressions
   * (CPM) costs during this period. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $costMicros;
  /**
   * Cost of goods sold (COGS) is the total cost of the products you sold in
   * orders attributed to your ads. How it works: You can add a cost of goods
   * sold value to every product in Merchant Center. If you report conversions
   * with cart data, the products you sold are matched with their cost of goods
   * sold value and this can be used to calculate the gross profit you made on
   * each order. Example: Someone clicked on a Shopping ad for a hat then bought
   * the same hat and a shirt. The hat has a cost of goods sold value of $3, the
   * shirt has a cost of goods sold value of $5. The cost of goods sold for this
   * order is $8 = $3 + $5. This metric is only available if you report
   * conversions with cart data.
   *
   * @var string
   */
  public $costOfGoodsSoldMicros;
  /**
   * The cost of ad interactions divided by all conversions.
   *
   * @var 
   */
  public $costPerAllConversions;
  /**
   * Average conversion eligible cost per biddable conversion.
   *
   * @var 
   */
  public $costPerConversion;
  /**
   * The cost of ad interactions divided by current model attributed
   * conversions. This only includes conversion actions which
   * include_in_conversions_metric attribute is set to true. If you use
   * conversion-based bidding, your bid strategies will optimize for these
   * conversions.
   *
   * @var 
   */
  public $costPerCurrentModelAttributedConversion;
  /**
   * Conversions from when a customer clicks on an ad on one device, then
   * converts on a different device or browser. Cross-device conversions are
   * already included in all_conversions.
   *
   * @var 
   */
  public $crossDeviceConversions;
  /**
   * The number of cross-device conversions by conversion date. Details for the
   * by_conversion_date columns are available at
   * https://support.google.com/sa360/answer/9250611.
   *
   * @var 
   */
  public $crossDeviceConversionsByConversionDate;
  /**
   * The sum of the value of cross-device conversions.
   *
   * @var 
   */
  public $crossDeviceConversionsValue;
  /**
   * The sum of cross-device conversions value by conversion date. Details for
   * the by_conversion_date columns are available at
   * https://support.google.com/sa360/answer/9250611.
   *
   * @var 
   */
  public $crossDeviceConversionsValueByConversionDate;
  /**
   * Cross-sell cost of goods sold (COGS) is the total cost of products sold as
   * a result of advertising a different product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * these products don't match then this is considered cross-sell. Cross-sell
   * cost of goods sold is the total cost of the products sold that weren't
   * advertised. Example: Someone clicked on a Shopping ad for a hat then bought
   * the same hat and a shirt. The hat has a cost of goods sold value of $3, the
   * shirt has a cost of goods sold value of $5. The cross-sell cost of goods
   * sold for this order is $5. This metric is only available if you report
   * conversions with cart data. This metric is a monetary value and returned in
   * the customer's currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $crossSellCostOfGoodsSoldMicros;
  /**
   * Cross-sell gross profit is the profit you made from products sold as a
   * result of advertising a different product, minus cost of goods sold (COGS).
   * How it works: You report conversions with cart data for completed purchases
   * on your website. If the ad that was interacted with before the purchase has
   * an associated product (see Shopping Ads) then this product is considered
   * the advertised product. Any product included in the purchase is a sold
   * product. If these products don't match then this is considered cross-sell.
   * Cross-sell gross profit is the revenue you made from cross-sell attributed
   * to your ads minus the cost of the goods sold. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat and a shirt. The shirt is
   * priced $20 and has a cost of goods sold value of $5. The cross-sell gross
   * profit of this order is $15 = $20 - $5. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $crossSellGrossProfitMicros;
  /**
   * Cross-sell revenue is the total amount you made from products sold as a
   * result of advertising a different product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * these products don't match then this is considered cross-sell. Cross-sell
   * revenue is the total value you made from cross-sell attributed to your ads.
   * Example: Someone clicked on a Shopping ad for a hat then bought the same
   * hat and a shirt. The hat is priced $10 and the shirt is priced $20. The
   * cross-sell revenue of this order is $20. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $crossSellRevenueMicros;
  /**
   * Cross-sell units sold is the total number of products sold as a result of
   * advertising a different product. How it works: You report conversions with
   * cart data for completed purchases on your website. If the ad that was
   * interacted with before the purchase has an associated product (see Shopping
   * Ads) then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If these
   * products don't match then this is considered cross-sell. Cross-sell units
   * sold is the total number of cross-sold products from all orders attributed
   * to your ads. Example: Someone clicked on a Shopping ad for a hat then
   * bought the same hat, a shirt and a jacket. The cross-sell units sold in
   * this order is 2. This metric is only available if you report conversions
   * with cart data.
   *
   * @var 
   */
  public $crossSellUnitsSold;
  /**
   * The number of clicks your ad receives (Clicks) divided by the number of
   * times your ad is shown (Impressions).
   *
   * @var 
   */
  public $ctr;
  /**
   * The percentage of clicks that have been filtered out of your total number
   * of clicks (filtered + non-filtered clicks) due to being general invalid
   * clicks. These are clicks Google considers illegitimate that are detected
   * through routine means of filtration (that is, known invalid data-center
   * traffic, bots and spiders or other crawlers, irregular patterns, etc).
   * You're not charged for them, and they don't affect your account statistics.
   * See the help page at
   * https://support.google.com/campaignmanager/answer/6076504 for details.
   *
   * @var 
   */
  public $generalInvalidClickRate;
  /**
   * Number of general invalid clicks. These are a subset of your invalid clicks
   * that are detected through routine means of filtration (such as known
   * invalid data-center traffic, bots and spiders or other crawlers, irregular
   * patterns, etc.). You're not charged for them, and they don't affect your
   * account statistics. See the help page at
   * https://support.google.com/campaignmanager/answer/6076504 for details.
   *
   * @var string
   */
  public $generalInvalidClicks;
  /**
   * Gross profit margin is the percentage gross profit you made from orders
   * attributed to your ads, after taking out the cost of goods sold (COGS). How
   * it works: You report conversions with cart data for completed purchases on
   * your website. Gross profit margin is the gross profit you made divided by
   * your total revenue and multiplied by 100%. Gross profit margin calculations
   * only include products that have a cost of goods sold value in Merchant
   * Center. Example: Someone bought a hat and a shirt in an order on your
   * website. The hat is priced $10 and has a cost of goods sold value of $3.
   * The shirt is priced $20 but has no cost of goods sold value. Gross profit
   * margin for this order will only take into account the hat because it has a
   * cost of goods sold value, so it's 70% = ($10 - $3)/$10 x 100%. This metric
   * is only available if you report conversions with cart data.
   *
   * @var 
   */
  public $grossProfitMargin;
  /**
   * Gross profit is the profit you made from orders attributed to your ads
   * minus the cost of goods sold (COGS). How it works: Gross profit is the
   * revenue you made from sales attributed to your ads minus cost of goods
   * sold. Gross profit calculations only include products that have a cost of
   * goods sold value in Merchant Center. Example: Someone clicked on a Shopping
   * ad for a hat then bought the same hat and a shirt in an order from your
   * website. The hat is priced $10 and the shirt is priced $20. The hat has a
   * cost of goods sold value of $3, but the shirt has no cost of goods sold
   * value. Gross profit for this order will only take into account the hat, so
   * it's $7 = $10 - $3. This metric is only available if you report conversions
   * with cart data.
   *
   * @var string
   */
  public $grossProfitMicros;
  /**
   * The creative historical quality score.
   *
   * @var string
   */
  public $historicalCreativeQualityScore;
  /**
   * The quality of historical landing page experience.
   *
   * @var string
   */
  public $historicalLandingPageQualityScore;
  /**
   * The historical quality score.
   *
   * @var string
   */
  public $historicalQualityScore;
  /**
   * The historical search predicted click through rate (CTR).
   *
   * @var string
   */
  public $historicalSearchPredictedCtr;
  /**
   * Count of how often your ad has appeared on a search results page or website
   * on the Google Network.
   *
   * @var string
   */
  public $impressions;
  /**
   * The types of payable and free interactions.
   *
   * @var string[]
   */
  public $interactionEventTypes;
  /**
   * How often people interact with your ad after it is shown to them. This is
   * the number of interactions divided by the number of times your ad is shown.
   *
   * @var 
   */
  public $interactionRate;
  /**
   * The number of interactions. An interaction is the main user action
   * associated with an ad format-clicks for text and shopping ads, views for
   * video ads, and so on.
   *
   * @var string
   */
  public $interactions;
  /**
   * The percentage of clicks filtered out of your total number of clicks
   * (filtered + non-filtered clicks) during the reporting period.
   *
   * @var 
   */
  public $invalidClickRate;
  /**
   * Number of clicks Google considers illegitimate and doesn't charge you for.
   *
   * @var string
   */
  public $invalidClicks;
  /**
   * Lead cost of goods sold (COGS) is the total cost of products sold as a
   * result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with has an associated product (see Shopping Ads)
   * then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the cost of these goods is counted
   * under lead cost of goods sold. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The lead
   * cost of goods sold for this order is $3. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @var string
   */
  public $leadCostOfGoodsSoldMicros;
  /**
   * Lead gross profit is the profit you made from products sold as a result of
   * advertising the same product, minus cost of goods sold (COGS). How it
   * works: You report conversions with cart data for completed purchases on
   * your website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If the advertised and sold products match, then the
   * revenue you made from these sales minus the cost of goods sold is your lead
   * gross profit. Example: Someone clicked on a Shopping ad for a hat then
   * bought the same hat and a shirt. The hat is priced $10 and has a cost of
   * goods sold value of $3. The lead gross profit of this order is $7 = $10 -
   * $3. This metric is only available if you report conversions with cart data.
   * This metric is a monetary value and returned in the customer's currency by
   * default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $leadGrossProfitMicros;
  /**
   * Lead revenue is the total amount you made from products sold as a result of
   * advertising the same product. How it works: You report conversions with
   * cart data for completed purchases on your website. If the ad that was
   * interacted with before the purchase has an associated product (see Shopping
   * Ads) then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the total value you made from the
   * sales of these products is shown under lead revenue. Example: Someone
   * clicked on a Shopping ad for a hat then bought the same hat and a shirt.
   * The hat is priced $10 and the shirt is priced $20. The lead revenue of this
   * order is $10. This metric is only available if you report conversions with
   * cart data. This metric is a monetary value and returned in the customer's
   * currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @var string
   */
  public $leadRevenueMicros;
  /**
   * Lead units sold is the total number of products sold as a result of
   * advertising the same product. How it works: You report conversions with
   * cart data for completed purchases on your website. If the ad that was
   * interacted with before the purchase has an associated product (see Shopping
   * Ads) then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the total number of these products
   * sold is shown under lead units sold. Example: Someone clicked on a Shopping
   * ad for a hat then bought the same hat, a shirt and a jacket. The lead units
   * sold in this order is 1. This metric is only available if you report
   * conversions with cart data.
   *
   * @var 
   */
  public $leadUnitsSold;
  /**
   * The percentage of mobile clicks that go to a mobile-friendly page.
   *
   * @var 
   */
  public $mobileFriendlyClicksPercentage;
  /**
   * Orders is the total number of purchase conversions you received attributed
   * to your ads. How it works: You report conversions with cart data for
   * completed purchases on your website. If a conversion is attributed to
   * previous interactions with your ads (clicks for text or Shopping ads, views
   * for video ads etc.) it's counted as an order. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat and a shirt in an order on
   * your website. Even though they bought 2 products, this would count as 1
   * order. This metric is only available if you report conversions with cart
   * data.
   *
   * @var 
   */
  public $orders;
  protected $rawEventConversionMetricsType = GoogleAdsSearchads360V0CommonValue::class;
  protected $rawEventConversionMetricsDataType = 'array';
  /**
   * Revenue is the total amount you made from orders attributed to your ads.
   * How it works: You report conversions with cart data for completed purchases
   * on your website. Revenue is the total value of all the orders you received
   * attributed to your ads, minus any discount. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat and a shirt in an order from
   * your website. The hat is priced $10 and the shirt is priced $20. The entire
   * order has a $5 discount. The revenue from this order is $25 = ($10 + $20) -
   * $5. This metric is only available if you report conversions with cart data.
   *
   * @var string
   */
  public $revenueMicros;
  /**
   * The percentage of the customer's Shopping or Search ad impressions that are
   * shown in the most prominent Shopping position. See
   * https://support.google.com/sa360/answer/9566729 for details. Any value
   * below 0.1 is reported as 0.0999.
   *
   * @var 
   */
  public $searchAbsoluteTopImpressionShare;
  /**
   * The number estimating how often your ad wasn't the very first ad among the
   * top ads in the search results due to a low budget. Note: Search budget lost
   * absolute top impression share is reported in the range of 0 to 0.9. Any
   * value above 0.9 is reported as 0.9001.
   *
   * @var 
   */
  public $searchBudgetLostAbsoluteTopImpressionShare;
  /**
   * The estimated percent of times that your ad was eligible to show on the
   * Search Network but didn't because your budget was too low. Note: Search
   * budget lost impression share is reported in the range of 0 to 0.9. Any
   * value above 0.9 is reported as 0.9001.
   *
   * @var 
   */
  public $searchBudgetLostImpressionShare;
  /**
   * The number estimating how often your ad didn't show adjacent to the top
   * organic search results due to a low budget. Note: Search budget lost top
   * impression share is reported in the range of 0 to 0.9. Any value above 0.9
   * is reported as 0.9001.
   *
   * @var 
   */
  public $searchBudgetLostTopImpressionShare;
  /**
   * The number of clicks you've received on the Search Network divided by the
   * estimated number of clicks you were eligible to receive. Note: Search click
   * share is reported in the range of 0.1 to 1. Any value below 0.1 is reported
   * as 0.0999.
   *
   * @var 
   */
  public $searchClickShare;
  /**
   * The impressions you've received divided by the estimated number of
   * impressions you were eligible to receive on the Search Network for search
   * terms that matched your keywords exactly (or were close variants of your
   * keyword), regardless of your keyword match types. Note: Search exact match
   * impression share is reported in the range of 0.1 to 1. Any value below 0.1
   * is reported as 0.0999.
   *
   * @var 
   */
  public $searchExactMatchImpressionShare;
  /**
   * The impressions you've received on the Search Network divided by the
   * estimated number of impressions you were eligible to receive. Note: Search
   * impression share is reported in the range of 0.1 to 1. Any value below 0.1
   * is reported as 0.0999.
   *
   * @var 
   */
  public $searchImpressionShare;
  /**
   * The number estimating how often your ad wasn't the very first ad among the
   * top ads in the search results due to poor Ad Rank. Note: Search rank lost
   * absolute top impression share is reported in the range of 0 to 0.9. Any
   * value above 0.9 is reported as 0.9001.
   *
   * @var 
   */
  public $searchRankLostAbsoluteTopImpressionShare;
  /**
   * The estimated percentage of impressions on the Search Network that your ads
   * didn't receive due to poor Ad Rank. Note: Search rank lost impression share
   * is reported in the range of 0 to 0.9. Any value above 0.9 is reported as
   * 0.9001.
   *
   * @var 
   */
  public $searchRankLostImpressionShare;
  /**
   * The number estimating how often your ad didn't show adjacent to the top
   * organic search results due to poor Ad Rank. Note: Search rank lost top
   * impression share is reported in the range of 0 to 0.9. Any value above 0.9
   * is reported as 0.9001.
   *
   * @var 
   */
  public $searchRankLostTopImpressionShare;
  /**
   * The impressions you've received among the top ads compared to the estimated
   * number of impressions you were eligible to receive among the top ads. Note:
   * Search top impression share is reported in the range of 0.1 to 1. Any value
   * below 0.1 is reported as 0.0999. Top ads are generally above the top
   * organic results, although they may show below the top organic results on
   * certain queries.
   *
   * @var 
   */
  public $searchTopImpressionShare;
  /**
   * The percent of your ad impressions that are shown adjacent to the top
   * organic search results.
   *
   * @var 
   */
  public $topImpressionPercentage;
  /**
   * The number of unique users who saw your ad during the requested time
   * period. This metric cannot be aggregated, and can only be requested for
   * date ranges of 92 days or less. This metric is available for following
   * campaign types - Display, Video, Discovery and App.
   *
   * @var string
   */
  public $uniqueUsers;
  /**
   * Units sold is the total number of products sold from orders attributed to
   * your ads. How it works: You report conversions with cart data for completed
   * purchases on your website. Units sold is the total number of products sold
   * from all orders attributed to your ads. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat, a shirt and a jacket. The
   * units sold in this order is 3. This metric is only available if you report
   * conversions with cart data.
   *
   * @var 
   */
  public $unitsSold;
  /**
   * The value of all conversions divided by the number of all conversions.
   *
   * @var 
   */
  public $valuePerAllConversions;
  /**
   * The value of all conversions divided by the number of all conversions. When
   * this column is selected with date, the values in date column means the
   * conversion date. Details for the by_conversion_date columns are available
   * at https://support.google.com/sa360/answer/9250611.
   *
   * @var 
   */
  public $valuePerAllConversionsByConversionDate;
  /**
   * The value of biddable conversion divided by the number of biddable
   * conversions. Shows how much, on average, each of the biddable conversions
   * is worth.
   *
   * @var 
   */
  public $valuePerConversion;
  /**
   * Biddable conversions value by conversion date divided by biddable
   * conversions by conversion date. Shows how much, on average, each of the
   * biddable conversions is worth (by conversion date). When this column is
   * selected with date, the values in date column means the conversion date.
   *
   * @var 
   */
  public $valuePerConversionsByConversionDate;
  /**
   * Clicks that Search Ads 360 has successfully recorded and forwarded to an
   * advertiser's landing page.
   *
   * @var 
   */
  public $visits;

  public function setAbsoluteTopImpressionPercentage($absoluteTopImpressionPercentage)
  {
    $this->absoluteTopImpressionPercentage = $absoluteTopImpressionPercentage;
  }
  public function getAbsoluteTopImpressionPercentage()
  {
    return $this->absoluteTopImpressionPercentage;
  }
  public function setAllConversions($allConversions)
  {
    $this->allConversions = $allConversions;
  }
  public function getAllConversions()
  {
    return $this->allConversions;
  }
  public function setAllConversionsByConversionDate($allConversionsByConversionDate)
  {
    $this->allConversionsByConversionDate = $allConversionsByConversionDate;
  }
  public function getAllConversionsByConversionDate()
  {
    return $this->allConversionsByConversionDate;
  }
  public function setAllConversionsFromClickToCall($allConversionsFromClickToCall)
  {
    $this->allConversionsFromClickToCall = $allConversionsFromClickToCall;
  }
  public function getAllConversionsFromClickToCall()
  {
    return $this->allConversionsFromClickToCall;
  }
  public function setAllConversionsFromDirections($allConversionsFromDirections)
  {
    $this->allConversionsFromDirections = $allConversionsFromDirections;
  }
  public function getAllConversionsFromDirections()
  {
    return $this->allConversionsFromDirections;
  }
  public function setAllConversionsFromInteractionsRate($allConversionsFromInteractionsRate)
  {
    $this->allConversionsFromInteractionsRate = $allConversionsFromInteractionsRate;
  }
  public function getAllConversionsFromInteractionsRate()
  {
    return $this->allConversionsFromInteractionsRate;
  }
  public function setAllConversionsFromInteractionsValuePerInteraction($allConversionsFromInteractionsValuePerInteraction)
  {
    $this->allConversionsFromInteractionsValuePerInteraction = $allConversionsFromInteractionsValuePerInteraction;
  }
  public function getAllConversionsFromInteractionsValuePerInteraction()
  {
    return $this->allConversionsFromInteractionsValuePerInteraction;
  }
  public function setAllConversionsFromMenu($allConversionsFromMenu)
  {
    $this->allConversionsFromMenu = $allConversionsFromMenu;
  }
  public function getAllConversionsFromMenu()
  {
    return $this->allConversionsFromMenu;
  }
  public function setAllConversionsFromOrder($allConversionsFromOrder)
  {
    $this->allConversionsFromOrder = $allConversionsFromOrder;
  }
  public function getAllConversionsFromOrder()
  {
    return $this->allConversionsFromOrder;
  }
  public function setAllConversionsFromOtherEngagement($allConversionsFromOtherEngagement)
  {
    $this->allConversionsFromOtherEngagement = $allConversionsFromOtherEngagement;
  }
  public function getAllConversionsFromOtherEngagement()
  {
    return $this->allConversionsFromOtherEngagement;
  }
  public function setAllConversionsFromStoreVisit($allConversionsFromStoreVisit)
  {
    $this->allConversionsFromStoreVisit = $allConversionsFromStoreVisit;
  }
  public function getAllConversionsFromStoreVisit()
  {
    return $this->allConversionsFromStoreVisit;
  }
  public function setAllConversionsFromStoreWebsite($allConversionsFromStoreWebsite)
  {
    $this->allConversionsFromStoreWebsite = $allConversionsFromStoreWebsite;
  }
  public function getAllConversionsFromStoreWebsite()
  {
    return $this->allConversionsFromStoreWebsite;
  }
  public function setAllConversionsValue($allConversionsValue)
  {
    $this->allConversionsValue = $allConversionsValue;
  }
  public function getAllConversionsValue()
  {
    return $this->allConversionsValue;
  }
  public function setAllConversionsValueByConversionDate($allConversionsValueByConversionDate)
  {
    $this->allConversionsValueByConversionDate = $allConversionsValueByConversionDate;
  }
  public function getAllConversionsValueByConversionDate()
  {
    return $this->allConversionsValueByConversionDate;
  }
  public function setAllConversionsValuePerCost($allConversionsValuePerCost)
  {
    $this->allConversionsValuePerCost = $allConversionsValuePerCost;
  }
  public function getAllConversionsValuePerCost()
  {
    return $this->allConversionsValuePerCost;
  }
  public function setAverageCartSize($averageCartSize)
  {
    $this->averageCartSize = $averageCartSize;
  }
  public function getAverageCartSize()
  {
    return $this->averageCartSize;
  }
  public function setAverageCost($averageCost)
  {
    $this->averageCost = $averageCost;
  }
  public function getAverageCost()
  {
    return $this->averageCost;
  }
  public function setAverageCpc($averageCpc)
  {
    $this->averageCpc = $averageCpc;
  }
  public function getAverageCpc()
  {
    return $this->averageCpc;
  }
  public function setAverageCpm($averageCpm)
  {
    $this->averageCpm = $averageCpm;
  }
  public function getAverageCpm()
  {
    return $this->averageCpm;
  }
  public function setAverageImpressionFrequencyPerUser($averageImpressionFrequencyPerUser)
  {
    $this->averageImpressionFrequencyPerUser = $averageImpressionFrequencyPerUser;
  }
  public function getAverageImpressionFrequencyPerUser()
  {
    return $this->averageImpressionFrequencyPerUser;
  }
  /**
   * Average order value is the average revenue you made per order attributed to
   * your ads. How it works: You report conversions with cart data for completed
   * purchases on your website. Average order value is the total revenue from
   * your orders divided by the total number of orders. Example: You received 3
   * orders which made $10, $15 and $20 worth of revenue. The average order
   * value is $15 = ($10 + $15 + $20)/3. This metric is only available if you
   * report conversions with cart data.
   *
   * @param string $averageOrderValueMicros
   */
  public function setAverageOrderValueMicros($averageOrderValueMicros)
  {
    $this->averageOrderValueMicros = $averageOrderValueMicros;
  }
  /**
   * @return string
   */
  public function getAverageOrderValueMicros()
  {
    return $this->averageOrderValueMicros;
  }
  public function setAverageQualityScore($averageQualityScore)
  {
    $this->averageQualityScore = $averageQualityScore;
  }
  public function getAverageQualityScore()
  {
    return $this->averageQualityScore;
  }
  /**
   * The number of clicks.
   *
   * @param string $clicks
   */
  public function setClicks($clicks)
  {
    $this->clicks = $clicks;
  }
  /**
   * @return string
   */
  public function getClicks()
  {
    return $this->clicks;
  }
  public function setClientAccountConversions($clientAccountConversions)
  {
    $this->clientAccountConversions = $clientAccountConversions;
  }
  public function getClientAccountConversions()
  {
    return $this->clientAccountConversions;
  }
  public function setClientAccountConversionsValue($clientAccountConversionsValue)
  {
    $this->clientAccountConversionsValue = $clientAccountConversionsValue;
  }
  public function getClientAccountConversionsValue()
  {
    return $this->clientAccountConversionsValue;
  }
  /**
   * Client account cross-sell cost of goods sold (COGS) is the total cost of
   * products sold as a result of advertising a different product. How it works:
   * You report conversions with cart data for completed purchases on your
   * website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If these products don't match then this is considered
   * cross-sell. Cross-sell cost of goods sold is the total cost of the products
   * sold that weren't advertised. Example: Someone clicked on a Shopping ad for
   * a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The
   * cross-sell cost of goods sold for this order is $5. This metric is only
   * available if you report conversions with cart data. This metric is a
   * monetary value and returned in the customer's currency by default. See the
   * metrics_currency parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $clientAccountCrossSellCostOfGoodsSoldMicros
   */
  public function setClientAccountCrossSellCostOfGoodsSoldMicros($clientAccountCrossSellCostOfGoodsSoldMicros)
  {
    $this->clientAccountCrossSellCostOfGoodsSoldMicros = $clientAccountCrossSellCostOfGoodsSoldMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountCrossSellCostOfGoodsSoldMicros()
  {
    return $this->clientAccountCrossSellCostOfGoodsSoldMicros;
  }
  /**
   * Client account cross-sell gross profit is the profit you made from products
   * sold as a result of advertising a different product, minus cost of goods
   * sold (COGS). How it works: You report conversions with cart data for
   * completed purchases on your website. If the ad that was interacted with
   * before the purchase has an associated product (see Shopping Ads) then this
   * product is considered the advertised product. Any product included in the
   * purchase is a sold product. If these products don't match then this is
   * considered cross-sell. Cross-sell gross profit is the revenue you made from
   * cross-sell attributed to your ads minus the cost of the goods sold.
   * Example: Someone clicked on a Shopping ad for a hat then bought the same
   * hat and a shirt. The shirt is priced $20 and has a cost of goods sold value
   * of $5. The cross-sell gross profit of this order is $15 = $20 - $5. This
   * metric is only available if you report conversions with cart data. This
   * metric is a monetary value and returned in the customer's currency by
   * default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $clientAccountCrossSellGrossProfitMicros
   */
  public function setClientAccountCrossSellGrossProfitMicros($clientAccountCrossSellGrossProfitMicros)
  {
    $this->clientAccountCrossSellGrossProfitMicros = $clientAccountCrossSellGrossProfitMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountCrossSellGrossProfitMicros()
  {
    return $this->clientAccountCrossSellGrossProfitMicros;
  }
  /**
   * Client account cross-sell revenue is the total amount you made from
   * products sold as a result of advertising a different product. How it works:
   * You report conversions with cart data for completed purchases on your
   * website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If these products don't match then this is considered
   * cross-sell. Cross-sell revenue is the total value you made from cross-sell
   * attributed to your ads. Example: Someone clicked on a Shopping ad for a hat
   * then bought the same hat and a shirt. The hat is priced $10 and the shirt
   * is priced $20. The cross-sell revenue of this order is $20. This metric is
   * only available if you report conversions with cart data. This metric is a
   * monetary value and returned in the customer's currency by default. See the
   * metrics_currency parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $clientAccountCrossSellRevenueMicros
   */
  public function setClientAccountCrossSellRevenueMicros($clientAccountCrossSellRevenueMicros)
  {
    $this->clientAccountCrossSellRevenueMicros = $clientAccountCrossSellRevenueMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountCrossSellRevenueMicros()
  {
    return $this->clientAccountCrossSellRevenueMicros;
  }
  public function setClientAccountCrossSellUnitsSold($clientAccountCrossSellUnitsSold)
  {
    $this->clientAccountCrossSellUnitsSold = $clientAccountCrossSellUnitsSold;
  }
  public function getClientAccountCrossSellUnitsSold()
  {
    return $this->clientAccountCrossSellUnitsSold;
  }
  /**
   * Client account lead cost of goods sold (COGS) is the total cost of products
   * sold as a result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with has an associated product (see Shopping Ads)
   * then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the cost of these goods is counted
   * under lead cost of goods sold. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The lead
   * cost of goods sold for this order is $3. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $clientAccountLeadCostOfGoodsSoldMicros
   */
  public function setClientAccountLeadCostOfGoodsSoldMicros($clientAccountLeadCostOfGoodsSoldMicros)
  {
    $this->clientAccountLeadCostOfGoodsSoldMicros = $clientAccountLeadCostOfGoodsSoldMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountLeadCostOfGoodsSoldMicros()
  {
    return $this->clientAccountLeadCostOfGoodsSoldMicros;
  }
  /**
   * Client account lead gross profit is the profit you made from products sold
   * as a result of advertising the same product, minus cost of goods sold
   * (COGS). How it works: You report conversions with cart data for completed
   * purchases on your website. If the ad that was interacted with before the
   * purchase has an associated product (see Shopping Ads) then this product is
   * considered the advertised product. Any product included in the order the
   * customer places is a sold product. If the advertised and sold products
   * match, then the revenue you made from these sales minus the cost of goods
   * sold is your lead gross profit. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat is priced $10 and
   * has a cost of goods sold value of $3. The lead gross profit of this order
   * is $7 = $10 - $3. This metric is only available if you report conversions
   * with cart data. This metric is a monetary value and returned in the
   * customer's currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $clientAccountLeadGrossProfitMicros
   */
  public function setClientAccountLeadGrossProfitMicros($clientAccountLeadGrossProfitMicros)
  {
    $this->clientAccountLeadGrossProfitMicros = $clientAccountLeadGrossProfitMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountLeadGrossProfitMicros()
  {
    return $this->clientAccountLeadGrossProfitMicros;
  }
  /**
   * Client account lead revenue is the total amount you made from products sold
   * as a result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * the advertised and sold products match, then the total value you made from
   * the sales of these products is shown under lead revenue. Example: Someone
   * clicked on a Shopping ad for a hat then bought the same hat and a shirt.
   * The hat is priced $10 and the shirt is priced $20. The lead revenue of this
   * order is $10. This metric is only available if you report conversions with
   * cart data. This metric is a monetary value and returned in the customer's
   * currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $clientAccountLeadRevenueMicros
   */
  public function setClientAccountLeadRevenueMicros($clientAccountLeadRevenueMicros)
  {
    $this->clientAccountLeadRevenueMicros = $clientAccountLeadRevenueMicros;
  }
  /**
   * @return string
   */
  public function getClientAccountLeadRevenueMicros()
  {
    return $this->clientAccountLeadRevenueMicros;
  }
  public function setClientAccountLeadUnitsSold($clientAccountLeadUnitsSold)
  {
    $this->clientAccountLeadUnitsSold = $clientAccountLeadUnitsSold;
  }
  public function getClientAccountLeadUnitsSold()
  {
    return $this->clientAccountLeadUnitsSold;
  }
  /**
   * The total number of view-through conversions. These happen when a customer
   * sees an image or rich media ad, then later completes a conversion on your
   * site without interacting with (for example, clicking on) another ad.
   *
   * @param string $clientAccountViewThroughConversions
   */
  public function setClientAccountViewThroughConversions($clientAccountViewThroughConversions)
  {
    $this->clientAccountViewThroughConversions = $clientAccountViewThroughConversions;
  }
  /**
   * @return string
   */
  public function getClientAccountViewThroughConversions()
  {
    return $this->clientAccountViewThroughConversions;
  }
  public function setContentBudgetLostImpressionShare($contentBudgetLostImpressionShare)
  {
    $this->contentBudgetLostImpressionShare = $contentBudgetLostImpressionShare;
  }
  public function getContentBudgetLostImpressionShare()
  {
    return $this->contentBudgetLostImpressionShare;
  }
  public function setContentImpressionShare($contentImpressionShare)
  {
    $this->contentImpressionShare = $contentImpressionShare;
  }
  public function getContentImpressionShare()
  {
    return $this->contentImpressionShare;
  }
  public function setContentRankLostImpressionShare($contentRankLostImpressionShare)
  {
    $this->contentRankLostImpressionShare = $contentRankLostImpressionShare;
  }
  public function getContentRankLostImpressionShare()
  {
    return $this->contentRankLostImpressionShare;
  }
  /**
   * The conversion custom metrics.
   *
   * @param GoogleAdsSearchads360V0CommonValue[] $conversionCustomMetrics
   */
  public function setConversionCustomMetrics($conversionCustomMetrics)
  {
    $this->conversionCustomMetrics = $conversionCustomMetrics;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonValue[]
   */
  public function getConversionCustomMetrics()
  {
    return $this->conversionCustomMetrics;
  }
  public function setConversions($conversions)
  {
    $this->conversions = $conversions;
  }
  public function getConversions()
  {
    return $this->conversions;
  }
  public function setConversionsByConversionDate($conversionsByConversionDate)
  {
    $this->conversionsByConversionDate = $conversionsByConversionDate;
  }
  public function getConversionsByConversionDate()
  {
    return $this->conversionsByConversionDate;
  }
  public function setConversionsFromInteractionsRate($conversionsFromInteractionsRate)
  {
    $this->conversionsFromInteractionsRate = $conversionsFromInteractionsRate;
  }
  public function getConversionsFromInteractionsRate()
  {
    return $this->conversionsFromInteractionsRate;
  }
  public function setConversionsFromInteractionsValuePerInteraction($conversionsFromInteractionsValuePerInteraction)
  {
    $this->conversionsFromInteractionsValuePerInteraction = $conversionsFromInteractionsValuePerInteraction;
  }
  public function getConversionsFromInteractionsValuePerInteraction()
  {
    return $this->conversionsFromInteractionsValuePerInteraction;
  }
  public function setConversionsValue($conversionsValue)
  {
    $this->conversionsValue = $conversionsValue;
  }
  public function getConversionsValue()
  {
    return $this->conversionsValue;
  }
  public function setConversionsValueByConversionDate($conversionsValueByConversionDate)
  {
    $this->conversionsValueByConversionDate = $conversionsValueByConversionDate;
  }
  public function getConversionsValueByConversionDate()
  {
    return $this->conversionsValueByConversionDate;
  }
  public function setConversionsValuePerCost($conversionsValuePerCost)
  {
    $this->conversionsValuePerCost = $conversionsValuePerCost;
  }
  public function getConversionsValuePerCost()
  {
    return $this->conversionsValuePerCost;
  }
  /**
   * The sum of your cost-per-click (CPC) and cost-per-thousand impressions
   * (CPM) costs during this period. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $costMicros
   */
  public function setCostMicros($costMicros)
  {
    $this->costMicros = $costMicros;
  }
  /**
   * @return string
   */
  public function getCostMicros()
  {
    return $this->costMicros;
  }
  /**
   * Cost of goods sold (COGS) is the total cost of the products you sold in
   * orders attributed to your ads. How it works: You can add a cost of goods
   * sold value to every product in Merchant Center. If you report conversions
   * with cart data, the products you sold are matched with their cost of goods
   * sold value and this can be used to calculate the gross profit you made on
   * each order. Example: Someone clicked on a Shopping ad for a hat then bought
   * the same hat and a shirt. The hat has a cost of goods sold value of $3, the
   * shirt has a cost of goods sold value of $5. The cost of goods sold for this
   * order is $8 = $3 + $5. This metric is only available if you report
   * conversions with cart data.
   *
   * @param string $costOfGoodsSoldMicros
   */
  public function setCostOfGoodsSoldMicros($costOfGoodsSoldMicros)
  {
    $this->costOfGoodsSoldMicros = $costOfGoodsSoldMicros;
  }
  /**
   * @return string
   */
  public function getCostOfGoodsSoldMicros()
  {
    return $this->costOfGoodsSoldMicros;
  }
  public function setCostPerAllConversions($costPerAllConversions)
  {
    $this->costPerAllConversions = $costPerAllConversions;
  }
  public function getCostPerAllConversions()
  {
    return $this->costPerAllConversions;
  }
  public function setCostPerConversion($costPerConversion)
  {
    $this->costPerConversion = $costPerConversion;
  }
  public function getCostPerConversion()
  {
    return $this->costPerConversion;
  }
  public function setCostPerCurrentModelAttributedConversion($costPerCurrentModelAttributedConversion)
  {
    $this->costPerCurrentModelAttributedConversion = $costPerCurrentModelAttributedConversion;
  }
  public function getCostPerCurrentModelAttributedConversion()
  {
    return $this->costPerCurrentModelAttributedConversion;
  }
  public function setCrossDeviceConversions($crossDeviceConversions)
  {
    $this->crossDeviceConversions = $crossDeviceConversions;
  }
  public function getCrossDeviceConversions()
  {
    return $this->crossDeviceConversions;
  }
  public function setCrossDeviceConversionsByConversionDate($crossDeviceConversionsByConversionDate)
  {
    $this->crossDeviceConversionsByConversionDate = $crossDeviceConversionsByConversionDate;
  }
  public function getCrossDeviceConversionsByConversionDate()
  {
    return $this->crossDeviceConversionsByConversionDate;
  }
  public function setCrossDeviceConversionsValue($crossDeviceConversionsValue)
  {
    $this->crossDeviceConversionsValue = $crossDeviceConversionsValue;
  }
  public function getCrossDeviceConversionsValue()
  {
    return $this->crossDeviceConversionsValue;
  }
  public function setCrossDeviceConversionsValueByConversionDate($crossDeviceConversionsValueByConversionDate)
  {
    $this->crossDeviceConversionsValueByConversionDate = $crossDeviceConversionsValueByConversionDate;
  }
  public function getCrossDeviceConversionsValueByConversionDate()
  {
    return $this->crossDeviceConversionsValueByConversionDate;
  }
  /**
   * Cross-sell cost of goods sold (COGS) is the total cost of products sold as
   * a result of advertising a different product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * these products don't match then this is considered cross-sell. Cross-sell
   * cost of goods sold is the total cost of the products sold that weren't
   * advertised. Example: Someone clicked on a Shopping ad for a hat then bought
   * the same hat and a shirt. The hat has a cost of goods sold value of $3, the
   * shirt has a cost of goods sold value of $5. The cross-sell cost of goods
   * sold for this order is $5. This metric is only available if you report
   * conversions with cart data. This metric is a monetary value and returned in
   * the customer's currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $crossSellCostOfGoodsSoldMicros
   */
  public function setCrossSellCostOfGoodsSoldMicros($crossSellCostOfGoodsSoldMicros)
  {
    $this->crossSellCostOfGoodsSoldMicros = $crossSellCostOfGoodsSoldMicros;
  }
  /**
   * @return string
   */
  public function getCrossSellCostOfGoodsSoldMicros()
  {
    return $this->crossSellCostOfGoodsSoldMicros;
  }
  /**
   * Cross-sell gross profit is the profit you made from products sold as a
   * result of advertising a different product, minus cost of goods sold (COGS).
   * How it works: You report conversions with cart data for completed purchases
   * on your website. If the ad that was interacted with before the purchase has
   * an associated product (see Shopping Ads) then this product is considered
   * the advertised product. Any product included in the purchase is a sold
   * product. If these products don't match then this is considered cross-sell.
   * Cross-sell gross profit is the revenue you made from cross-sell attributed
   * to your ads minus the cost of the goods sold. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat and a shirt. The shirt is
   * priced $20 and has a cost of goods sold value of $5. The cross-sell gross
   * profit of this order is $15 = $20 - $5. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $crossSellGrossProfitMicros
   */
  public function setCrossSellGrossProfitMicros($crossSellGrossProfitMicros)
  {
    $this->crossSellGrossProfitMicros = $crossSellGrossProfitMicros;
  }
  /**
   * @return string
   */
  public function getCrossSellGrossProfitMicros()
  {
    return $this->crossSellGrossProfitMicros;
  }
  /**
   * Cross-sell revenue is the total amount you made from products sold as a
   * result of advertising a different product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with before the purchase has an associated product
   * (see Shopping Ads) then this product is considered the advertised product.
   * Any product included in the order the customer places is a sold product. If
   * these products don't match then this is considered cross-sell. Cross-sell
   * revenue is the total value you made from cross-sell attributed to your ads.
   * Example: Someone clicked on a Shopping ad for a hat then bought the same
   * hat and a shirt. The hat is priced $10 and the shirt is priced $20. The
   * cross-sell revenue of this order is $20. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $crossSellRevenueMicros
   */
  public function setCrossSellRevenueMicros($crossSellRevenueMicros)
  {
    $this->crossSellRevenueMicros = $crossSellRevenueMicros;
  }
  /**
   * @return string
   */
  public function getCrossSellRevenueMicros()
  {
    return $this->crossSellRevenueMicros;
  }
  public function setCrossSellUnitsSold($crossSellUnitsSold)
  {
    $this->crossSellUnitsSold = $crossSellUnitsSold;
  }
  public function getCrossSellUnitsSold()
  {
    return $this->crossSellUnitsSold;
  }
  public function setCtr($ctr)
  {
    $this->ctr = $ctr;
  }
  public function getCtr()
  {
    return $this->ctr;
  }
  public function setGeneralInvalidClickRate($generalInvalidClickRate)
  {
    $this->generalInvalidClickRate = $generalInvalidClickRate;
  }
  public function getGeneralInvalidClickRate()
  {
    return $this->generalInvalidClickRate;
  }
  /**
   * Number of general invalid clicks. These are a subset of your invalid clicks
   * that are detected through routine means of filtration (such as known
   * invalid data-center traffic, bots and spiders or other crawlers, irregular
   * patterns, etc.). You're not charged for them, and they don't affect your
   * account statistics. See the help page at
   * https://support.google.com/campaignmanager/answer/6076504 for details.
   *
   * @param string $generalInvalidClicks
   */
  public function setGeneralInvalidClicks($generalInvalidClicks)
  {
    $this->generalInvalidClicks = $generalInvalidClicks;
  }
  /**
   * @return string
   */
  public function getGeneralInvalidClicks()
  {
    return $this->generalInvalidClicks;
  }
  public function setGrossProfitMargin($grossProfitMargin)
  {
    $this->grossProfitMargin = $grossProfitMargin;
  }
  public function getGrossProfitMargin()
  {
    return $this->grossProfitMargin;
  }
  /**
   * Gross profit is the profit you made from orders attributed to your ads
   * minus the cost of goods sold (COGS). How it works: Gross profit is the
   * revenue you made from sales attributed to your ads minus cost of goods
   * sold. Gross profit calculations only include products that have a cost of
   * goods sold value in Merchant Center. Example: Someone clicked on a Shopping
   * ad for a hat then bought the same hat and a shirt in an order from your
   * website. The hat is priced $10 and the shirt is priced $20. The hat has a
   * cost of goods sold value of $3, but the shirt has no cost of goods sold
   * value. Gross profit for this order will only take into account the hat, so
   * it's $7 = $10 - $3. This metric is only available if you report conversions
   * with cart data.
   *
   * @param string $grossProfitMicros
   */
  public function setGrossProfitMicros($grossProfitMicros)
  {
    $this->grossProfitMicros = $grossProfitMicros;
  }
  /**
   * @return string
   */
  public function getGrossProfitMicros()
  {
    return $this->grossProfitMicros;
  }
  /**
   * The creative historical quality score.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BELOW_AVERAGE, AVERAGE,
   * ABOVE_AVERAGE
   *
   * @param self::HISTORICAL_CREATIVE_QUALITY_SCORE_* $historicalCreativeQualityScore
   */
  public function setHistoricalCreativeQualityScore($historicalCreativeQualityScore)
  {
    $this->historicalCreativeQualityScore = $historicalCreativeQualityScore;
  }
  /**
   * @return self::HISTORICAL_CREATIVE_QUALITY_SCORE_*
   */
  public function getHistoricalCreativeQualityScore()
  {
    return $this->historicalCreativeQualityScore;
  }
  /**
   * The quality of historical landing page experience.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BELOW_AVERAGE, AVERAGE,
   * ABOVE_AVERAGE
   *
   * @param self::HISTORICAL_LANDING_PAGE_QUALITY_SCORE_* $historicalLandingPageQualityScore
   */
  public function setHistoricalLandingPageQualityScore($historicalLandingPageQualityScore)
  {
    $this->historicalLandingPageQualityScore = $historicalLandingPageQualityScore;
  }
  /**
   * @return self::HISTORICAL_LANDING_PAGE_QUALITY_SCORE_*
   */
  public function getHistoricalLandingPageQualityScore()
  {
    return $this->historicalLandingPageQualityScore;
  }
  /**
   * The historical quality score.
   *
   * @param string $historicalQualityScore
   */
  public function setHistoricalQualityScore($historicalQualityScore)
  {
    $this->historicalQualityScore = $historicalQualityScore;
  }
  /**
   * @return string
   */
  public function getHistoricalQualityScore()
  {
    return $this->historicalQualityScore;
  }
  /**
   * The historical search predicted click through rate (CTR).
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BELOW_AVERAGE, AVERAGE,
   * ABOVE_AVERAGE
   *
   * @param self::HISTORICAL_SEARCH_PREDICTED_CTR_* $historicalSearchPredictedCtr
   */
  public function setHistoricalSearchPredictedCtr($historicalSearchPredictedCtr)
  {
    $this->historicalSearchPredictedCtr = $historicalSearchPredictedCtr;
  }
  /**
   * @return self::HISTORICAL_SEARCH_PREDICTED_CTR_*
   */
  public function getHistoricalSearchPredictedCtr()
  {
    return $this->historicalSearchPredictedCtr;
  }
  /**
   * Count of how often your ad has appeared on a search results page or website
   * on the Google Network.
   *
   * @param string $impressions
   */
  public function setImpressions($impressions)
  {
    $this->impressions = $impressions;
  }
  /**
   * @return string
   */
  public function getImpressions()
  {
    return $this->impressions;
  }
  /**
   * The types of payable and free interactions.
   *
   * @param string[] $interactionEventTypes
   */
  public function setInteractionEventTypes($interactionEventTypes)
  {
    $this->interactionEventTypes = $interactionEventTypes;
  }
  /**
   * @return string[]
   */
  public function getInteractionEventTypes()
  {
    return $this->interactionEventTypes;
  }
  public function setInteractionRate($interactionRate)
  {
    $this->interactionRate = $interactionRate;
  }
  public function getInteractionRate()
  {
    return $this->interactionRate;
  }
  /**
   * The number of interactions. An interaction is the main user action
   * associated with an ad format-clicks for text and shopping ads, views for
   * video ads, and so on.
   *
   * @param string $interactions
   */
  public function setInteractions($interactions)
  {
    $this->interactions = $interactions;
  }
  /**
   * @return string
   */
  public function getInteractions()
  {
    return $this->interactions;
  }
  public function setInvalidClickRate($invalidClickRate)
  {
    $this->invalidClickRate = $invalidClickRate;
  }
  public function getInvalidClickRate()
  {
    return $this->invalidClickRate;
  }
  /**
   * Number of clicks Google considers illegitimate and doesn't charge you for.
   *
   * @param string $invalidClicks
   */
  public function setInvalidClicks($invalidClicks)
  {
    $this->invalidClicks = $invalidClicks;
  }
  /**
   * @return string
   */
  public function getInvalidClicks()
  {
    return $this->invalidClicks;
  }
  /**
   * Lead cost of goods sold (COGS) is the total cost of products sold as a
   * result of advertising the same product. How it works: You report
   * conversions with cart data for completed purchases on your website. If the
   * ad that was interacted with has an associated product (see Shopping Ads)
   * then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the cost of these goods is counted
   * under lead cost of goods sold. Example: Someone clicked on a Shopping ad
   * for a hat then bought the same hat and a shirt. The hat has a cost of goods
   * sold value of $3, the shirt has a cost of goods sold value of $5. The lead
   * cost of goods sold for this order is $3. This metric is only available if
   * you report conversions with cart data. This metric is a monetary value and
   * returned in the customer's currency by default. See the metrics_currency
   * parameter at https://developers.google.com/search-
   * ads/reporting/query/query-structure#parameters_clause
   *
   * @param string $leadCostOfGoodsSoldMicros
   */
  public function setLeadCostOfGoodsSoldMicros($leadCostOfGoodsSoldMicros)
  {
    $this->leadCostOfGoodsSoldMicros = $leadCostOfGoodsSoldMicros;
  }
  /**
   * @return string
   */
  public function getLeadCostOfGoodsSoldMicros()
  {
    return $this->leadCostOfGoodsSoldMicros;
  }
  /**
   * Lead gross profit is the profit you made from products sold as a result of
   * advertising the same product, minus cost of goods sold (COGS). How it
   * works: You report conversions with cart data for completed purchases on
   * your website. If the ad that was interacted with before the purchase has an
   * associated product (see Shopping Ads) then this product is considered the
   * advertised product. Any product included in the order the customer places
   * is a sold product. If the advertised and sold products match, then the
   * revenue you made from these sales minus the cost of goods sold is your lead
   * gross profit. Example: Someone clicked on a Shopping ad for a hat then
   * bought the same hat and a shirt. The hat is priced $10 and has a cost of
   * goods sold value of $3. The lead gross profit of this order is $7 = $10 -
   * $3. This metric is only available if you report conversions with cart data.
   * This metric is a monetary value and returned in the customer's currency by
   * default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $leadGrossProfitMicros
   */
  public function setLeadGrossProfitMicros($leadGrossProfitMicros)
  {
    $this->leadGrossProfitMicros = $leadGrossProfitMicros;
  }
  /**
   * @return string
   */
  public function getLeadGrossProfitMicros()
  {
    return $this->leadGrossProfitMicros;
  }
  /**
   * Lead revenue is the total amount you made from products sold as a result of
   * advertising the same product. How it works: You report conversions with
   * cart data for completed purchases on your website. If the ad that was
   * interacted with before the purchase has an associated product (see Shopping
   * Ads) then this product is considered the advertised product. Any product
   * included in the order the customer places is a sold product. If the
   * advertised and sold products match, then the total value you made from the
   * sales of these products is shown under lead revenue. Example: Someone
   * clicked on a Shopping ad for a hat then bought the same hat and a shirt.
   * The hat is priced $10 and the shirt is priced $20. The lead revenue of this
   * order is $10. This metric is only available if you report conversions with
   * cart data. This metric is a monetary value and returned in the customer's
   * currency by default. See the metrics_currency parameter at
   * https://developers.google.com/search-ads/reporting/query/query-
   * structure#parameters_clause
   *
   * @param string $leadRevenueMicros
   */
  public function setLeadRevenueMicros($leadRevenueMicros)
  {
    $this->leadRevenueMicros = $leadRevenueMicros;
  }
  /**
   * @return string
   */
  public function getLeadRevenueMicros()
  {
    return $this->leadRevenueMicros;
  }
  public function setLeadUnitsSold($leadUnitsSold)
  {
    $this->leadUnitsSold = $leadUnitsSold;
  }
  public function getLeadUnitsSold()
  {
    return $this->leadUnitsSold;
  }
  public function setMobileFriendlyClicksPercentage($mobileFriendlyClicksPercentage)
  {
    $this->mobileFriendlyClicksPercentage = $mobileFriendlyClicksPercentage;
  }
  public function getMobileFriendlyClicksPercentage()
  {
    return $this->mobileFriendlyClicksPercentage;
  }
  public function setOrders($orders)
  {
    $this->orders = $orders;
  }
  public function getOrders()
  {
    return $this->orders;
  }
  /**
   * The raw event conversion metrics.
   *
   * @param GoogleAdsSearchads360V0CommonValue[] $rawEventConversionMetrics
   */
  public function setRawEventConversionMetrics($rawEventConversionMetrics)
  {
    $this->rawEventConversionMetrics = $rawEventConversionMetrics;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonValue[]
   */
  public function getRawEventConversionMetrics()
  {
    return $this->rawEventConversionMetrics;
  }
  /**
   * Revenue is the total amount you made from orders attributed to your ads.
   * How it works: You report conversions with cart data for completed purchases
   * on your website. Revenue is the total value of all the orders you received
   * attributed to your ads, minus any discount. Example: Someone clicked on a
   * Shopping ad for a hat then bought the same hat and a shirt in an order from
   * your website. The hat is priced $10 and the shirt is priced $20. The entire
   * order has a $5 discount. The revenue from this order is $25 = ($10 + $20) -
   * $5. This metric is only available if you report conversions with cart data.
   *
   * @param string $revenueMicros
   */
  public function setRevenueMicros($revenueMicros)
  {
    $this->revenueMicros = $revenueMicros;
  }
  /**
   * @return string
   */
  public function getRevenueMicros()
  {
    return $this->revenueMicros;
  }
  public function setSearchAbsoluteTopImpressionShare($searchAbsoluteTopImpressionShare)
  {
    $this->searchAbsoluteTopImpressionShare = $searchAbsoluteTopImpressionShare;
  }
  public function getSearchAbsoluteTopImpressionShare()
  {
    return $this->searchAbsoluteTopImpressionShare;
  }
  public function setSearchBudgetLostAbsoluteTopImpressionShare($searchBudgetLostAbsoluteTopImpressionShare)
  {
    $this->searchBudgetLostAbsoluteTopImpressionShare = $searchBudgetLostAbsoluteTopImpressionShare;
  }
  public function getSearchBudgetLostAbsoluteTopImpressionShare()
  {
    return $this->searchBudgetLostAbsoluteTopImpressionShare;
  }
  public function setSearchBudgetLostImpressionShare($searchBudgetLostImpressionShare)
  {
    $this->searchBudgetLostImpressionShare = $searchBudgetLostImpressionShare;
  }
  public function getSearchBudgetLostImpressionShare()
  {
    return $this->searchBudgetLostImpressionShare;
  }
  public function setSearchBudgetLostTopImpressionShare($searchBudgetLostTopImpressionShare)
  {
    $this->searchBudgetLostTopImpressionShare = $searchBudgetLostTopImpressionShare;
  }
  public function getSearchBudgetLostTopImpressionShare()
  {
    return $this->searchBudgetLostTopImpressionShare;
  }
  public function setSearchClickShare($searchClickShare)
  {
    $this->searchClickShare = $searchClickShare;
  }
  public function getSearchClickShare()
  {
    return $this->searchClickShare;
  }
  public function setSearchExactMatchImpressionShare($searchExactMatchImpressionShare)
  {
    $this->searchExactMatchImpressionShare = $searchExactMatchImpressionShare;
  }
  public function getSearchExactMatchImpressionShare()
  {
    return $this->searchExactMatchImpressionShare;
  }
  public function setSearchImpressionShare($searchImpressionShare)
  {
    $this->searchImpressionShare = $searchImpressionShare;
  }
  public function getSearchImpressionShare()
  {
    return $this->searchImpressionShare;
  }
  public function setSearchRankLostAbsoluteTopImpressionShare($searchRankLostAbsoluteTopImpressionShare)
  {
    $this->searchRankLostAbsoluteTopImpressionShare = $searchRankLostAbsoluteTopImpressionShare;
  }
  public function getSearchRankLostAbsoluteTopImpressionShare()
  {
    return $this->searchRankLostAbsoluteTopImpressionShare;
  }
  public function setSearchRankLostImpressionShare($searchRankLostImpressionShare)
  {
    $this->searchRankLostImpressionShare = $searchRankLostImpressionShare;
  }
  public function getSearchRankLostImpressionShare()
  {
    return $this->searchRankLostImpressionShare;
  }
  public function setSearchRankLostTopImpressionShare($searchRankLostTopImpressionShare)
  {
    $this->searchRankLostTopImpressionShare = $searchRankLostTopImpressionShare;
  }
  public function getSearchRankLostTopImpressionShare()
  {
    return $this->searchRankLostTopImpressionShare;
  }
  public function setSearchTopImpressionShare($searchTopImpressionShare)
  {
    $this->searchTopImpressionShare = $searchTopImpressionShare;
  }
  public function getSearchTopImpressionShare()
  {
    return $this->searchTopImpressionShare;
  }
  public function setTopImpressionPercentage($topImpressionPercentage)
  {
    $this->topImpressionPercentage = $topImpressionPercentage;
  }
  public function getTopImpressionPercentage()
  {
    return $this->topImpressionPercentage;
  }
  /**
   * The number of unique users who saw your ad during the requested time
   * period. This metric cannot be aggregated, and can only be requested for
   * date ranges of 92 days or less. This metric is available for following
   * campaign types - Display, Video, Discovery and App.
   *
   * @param string $uniqueUsers
   */
  public function setUniqueUsers($uniqueUsers)
  {
    $this->uniqueUsers = $uniqueUsers;
  }
  /**
   * @return string
   */
  public function getUniqueUsers()
  {
    return $this->uniqueUsers;
  }
  public function setUnitsSold($unitsSold)
  {
    $this->unitsSold = $unitsSold;
  }
  public function getUnitsSold()
  {
    return $this->unitsSold;
  }
  public function setValuePerAllConversions($valuePerAllConversions)
  {
    $this->valuePerAllConversions = $valuePerAllConversions;
  }
  public function getValuePerAllConversions()
  {
    return $this->valuePerAllConversions;
  }
  public function setValuePerAllConversionsByConversionDate($valuePerAllConversionsByConversionDate)
  {
    $this->valuePerAllConversionsByConversionDate = $valuePerAllConversionsByConversionDate;
  }
  public function getValuePerAllConversionsByConversionDate()
  {
    return $this->valuePerAllConversionsByConversionDate;
  }
  public function setValuePerConversion($valuePerConversion)
  {
    $this->valuePerConversion = $valuePerConversion;
  }
  public function getValuePerConversion()
  {
    return $this->valuePerConversion;
  }
  public function setValuePerConversionsByConversionDate($valuePerConversionsByConversionDate)
  {
    $this->valuePerConversionsByConversionDate = $valuePerConversionsByConversionDate;
  }
  public function getValuePerConversionsByConversionDate()
  {
    return $this->valuePerConversionsByConversionDate;
  }
  public function setVisits($visits)
  {
    $this->visits = $visits;
  }
  public function getVisits()
  {
    return $this->visits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonMetrics::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonMetrics');
