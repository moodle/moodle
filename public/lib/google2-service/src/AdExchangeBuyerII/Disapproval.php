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

namespace Google\Service\AdExchangeBuyerII;

class Disapproval extends \Google\Collection
{
  /**
   * The length of the image animation is longer than allowed.
   */
  public const REASON_LENGTH_OF_IMAGE_ANIMATION = 'LENGTH_OF_IMAGE_ANIMATION';
  /**
   * The click through URL doesn't work properly.
   */
  public const REASON_BROKEN_URL = 'BROKEN_URL';
  /**
   * Something is wrong with the creative itself.
   */
  public const REASON_MEDIA_NOT_FUNCTIONAL = 'MEDIA_NOT_FUNCTIONAL';
  /**
   * The ad makes a fourth party call to an unapproved vendor.
   */
  public const REASON_INVALID_FOURTH_PARTY_CALL = 'INVALID_FOURTH_PARTY_CALL';
  /**
   * The ad targets consumers using remarketing lists and/or collects data for
   * subsequent use in retargeting, but does not correctly declare that use.
   */
  public const REASON_INCORRECT_REMARKETING_DECLARATION = 'INCORRECT_REMARKETING_DECLARATION';
  /**
   * Clicking on the ad leads to an error page.
   */
  public const REASON_LANDING_PAGE_ERROR = 'LANDING_PAGE_ERROR';
  /**
   * The ad size when rendered does not match the declaration.
   */
  public const REASON_AD_SIZE_DOES_NOT_MATCH_AD_SLOT = 'AD_SIZE_DOES_NOT_MATCH_AD_SLOT';
  /**
   * Ads with a white background require a border, which was missing.
   */
  public const REASON_NO_BORDER = 'NO_BORDER';
  /**
   * The creative attempts to set cookies from a fourth party that is not
   * certified.
   */
  public const REASON_FOURTH_PARTY_BROWSER_COOKIES = 'FOURTH_PARTY_BROWSER_COOKIES';
  /**
   * The creative sets an LSO object.
   */
  public const REASON_LSO_OBJECTS = 'LSO_OBJECTS';
  /**
   * The ad serves a blank.
   */
  public const REASON_BLANK_CREATIVE = 'BLANK_CREATIVE';
  /**
   * The ad uses rotation, but not all destination URLs were declared.
   */
  public const REASON_DESTINATION_URLS_UNDECLARED = 'DESTINATION_URLS_UNDECLARED';
  /**
   * There is a problem with the way the click macro is used.
   */
  public const REASON_PROBLEM_WITH_CLICK_MACRO = 'PROBLEM_WITH_CLICK_MACRO';
  /**
   * The ad technology declaration is not accurate.
   */
  public const REASON_INCORRECT_AD_TECHNOLOGY_DECLARATION = 'INCORRECT_AD_TECHNOLOGY_DECLARATION';
  /**
   * The actual destination URL does not match the declared destination URL.
   */
  public const REASON_INCORRECT_DESTINATION_URL_DECLARATION = 'INCORRECT_DESTINATION_URL_DECLARATION';
  /**
   * The declared expanding direction does not match the actual direction.
   */
  public const REASON_EXPANDABLE_INCORRECT_DIRECTION = 'EXPANDABLE_INCORRECT_DIRECTION';
  /**
   * The ad does not expand in a supported direction.
   */
  public const REASON_EXPANDABLE_DIRECTION_NOT_SUPPORTED = 'EXPANDABLE_DIRECTION_NOT_SUPPORTED';
  /**
   * The ad uses an expandable vendor that is not supported.
   */
  public const REASON_EXPANDABLE_INVALID_VENDOR = 'EXPANDABLE_INVALID_VENDOR';
  /**
   * There was an issue with the expandable ad.
   */
  public const REASON_EXPANDABLE_FUNCTIONALITY = 'EXPANDABLE_FUNCTIONALITY';
  /**
   * The ad uses a video vendor that is not supported.
   */
  public const REASON_VIDEO_INVALID_VENDOR = 'VIDEO_INVALID_VENDOR';
  /**
   * The length of the video ad is not supported.
   */
  public const REASON_VIDEO_UNSUPPORTED_LENGTH = 'VIDEO_UNSUPPORTED_LENGTH';
  /**
   * The format of the video ad is not supported.
   */
  public const REASON_VIDEO_UNSUPPORTED_FORMAT = 'VIDEO_UNSUPPORTED_FORMAT';
  /**
   * There was an issue with the video ad.
   */
  public const REASON_VIDEO_FUNCTIONALITY = 'VIDEO_FUNCTIONALITY';
  /**
   * The landing page does not conform to Ad Exchange policy.
   */
  public const REASON_LANDING_PAGE_DISABLED = 'LANDING_PAGE_DISABLED';
  /**
   * The ad or the landing page may contain malware.
   */
  public const REASON_MALWARE_SUSPECTED = 'MALWARE_SUSPECTED';
  /**
   * The ad contains adult images or video content.
   */
  public const REASON_ADULT_IMAGE_OR_VIDEO = 'ADULT_IMAGE_OR_VIDEO';
  /**
   * The ad contains text that is unclear or inaccurate.
   */
  public const REASON_INACCURATE_AD_TEXT = 'INACCURATE_AD_TEXT';
  /**
   * The ad promotes counterfeit designer goods.
   */
  public const REASON_COUNTERFEIT_DESIGNER_GOODS = 'COUNTERFEIT_DESIGNER_GOODS';
  /**
   * The ad causes a popup window to appear.
   */
  public const REASON_POP_UP = 'POP_UP';
  /**
   * The creative does not follow policies set for the RTB protocol.
   */
  public const REASON_INVALID_RTB_PROTOCOL_USAGE = 'INVALID_RTB_PROTOCOL_USAGE';
  /**
   * The ad contains a URL that uses a numeric IP address for the domain.
   */
  public const REASON_RAW_IP_ADDRESS_IN_SNIPPET = 'RAW_IP_ADDRESS_IN_SNIPPET';
  /**
   * The ad or landing page contains unacceptable content because it initiated a
   * software or executable download.
   */
  public const REASON_UNACCEPTABLE_CONTENT_SOFTWARE = 'UNACCEPTABLE_CONTENT_SOFTWARE';
  /**
   * The ad set an unauthorized cookie on a Google domain.
   */
  public const REASON_UNAUTHORIZED_COOKIE_ON_GOOGLE_DOMAIN = 'UNAUTHORIZED_COOKIE_ON_GOOGLE_DOMAIN';
  /**
   * Flash content found when no flash was declared.
   */
  public const REASON_UNDECLARED_FLASH_OBJECTS = 'UNDECLARED_FLASH_OBJECTS';
  /**
   * SSL support declared but not working correctly.
   */
  public const REASON_INVALID_SSL_DECLARATION = 'INVALID_SSL_DECLARATION';
  /**
   * Rich Media - Direct Download in Ad (ex. PDF download).
   */
  public const REASON_DIRECT_DOWNLOAD_IN_AD = 'DIRECT_DOWNLOAD_IN_AD';
  /**
   * Maximum download size exceeded.
   */
  public const REASON_MAXIMUM_DOWNLOAD_SIZE_EXCEEDED = 'MAXIMUM_DOWNLOAD_SIZE_EXCEEDED';
  /**
   * Bad Destination URL: Site Not Crawlable.
   */
  public const REASON_DESTINATION_URL_SITE_NOT_CRAWLABLE = 'DESTINATION_URL_SITE_NOT_CRAWLABLE';
  /**
   * Bad URL: Legal disapproval.
   */
  public const REASON_BAD_URL_LEGAL_DISAPPROVAL = 'BAD_URL_LEGAL_DISAPPROVAL';
  /**
   * Pharmaceuticals, Gambling, Alcohol not allowed and at least one was
   * detected.
   */
  public const REASON_PHARMA_GAMBLING_ALCOHOL_NOT_ALLOWED = 'PHARMA_GAMBLING_ALCOHOL_NOT_ALLOWED';
  /**
   * Dynamic DNS at Destination URL.
   */
  public const REASON_DYNAMIC_DNS_AT_DESTINATION_URL = 'DYNAMIC_DNS_AT_DESTINATION_URL';
  /**
   * Poor Image / Video Quality.
   */
  public const REASON_POOR_IMAGE_OR_VIDEO_QUALITY = 'POOR_IMAGE_OR_VIDEO_QUALITY';
  /**
   * For example, Image Trick to Click.
   */
  public const REASON_UNACCEPTABLE_IMAGE_CONTENT = 'UNACCEPTABLE_IMAGE_CONTENT';
  /**
   * Incorrect Image Layout.
   */
  public const REASON_INCORRECT_IMAGE_LAYOUT = 'INCORRECT_IMAGE_LAYOUT';
  /**
   * Irrelevant Image / Video.
   */
  public const REASON_IRRELEVANT_IMAGE_OR_VIDEO = 'IRRELEVANT_IMAGE_OR_VIDEO';
  /**
   * Broken back button.
   */
  public const REASON_DESTINATION_SITE_DOES_NOT_ALLOW_GOING_BACK = 'DESTINATION_SITE_DOES_NOT_ALLOW_GOING_BACK';
  /**
   * Misleading/Inaccurate claims in ads.
   */
  public const REASON_MISLEADING_CLAIMS_IN_AD = 'MISLEADING_CLAIMS_IN_AD';
  /**
   * Restricted Products.
   */
  public const REASON_RESTRICTED_PRODUCTS = 'RESTRICTED_PRODUCTS';
  /**
   * Unacceptable content. For example, malware.
   */
  public const REASON_UNACCEPTABLE_CONTENT = 'UNACCEPTABLE_CONTENT';
  /**
   * The ad automatically redirects to the destination site without a click, or
   * reports a click when none were made.
   */
  public const REASON_AUTOMATED_AD_CLICKING = 'AUTOMATED_AD_CLICKING';
  /**
   * The ad uses URL protocols that do not exist or are not allowed on AdX.
   */
  public const REASON_INVALID_URL_PROTOCOL = 'INVALID_URL_PROTOCOL';
  /**
   * Restricted content (for example, alcohol) was found in the ad but not
   * declared.
   */
  public const REASON_UNDECLARED_RESTRICTED_CONTENT = 'UNDECLARED_RESTRICTED_CONTENT';
  /**
   * Violation of the remarketing list policy.
   */
  public const REASON_INVALID_REMARKETING_LIST_USAGE = 'INVALID_REMARKETING_LIST_USAGE';
  /**
   * The destination site's robot.txt file prevents it from being crawled.
   */
  public const REASON_DESTINATION_SITE_NOT_CRAWLABLE_ROBOTS_TXT = 'DESTINATION_SITE_NOT_CRAWLABLE_ROBOTS_TXT';
  /**
   * Click to download must link to an app.
   */
  public const REASON_CLICK_TO_DOWNLOAD_NOT_AN_APP = 'CLICK_TO_DOWNLOAD_NOT_AN_APP';
  /**
   * A review extension must be an accurate review.
   */
  public const REASON_INACCURATE_REVIEW_EXTENSION = 'INACCURATE_REVIEW_EXTENSION';
  /**
   * Sexually explicit content.
   */
  public const REASON_SEXUALLY_EXPLICIT_CONTENT = 'SEXUALLY_EXPLICIT_CONTENT';
  /**
   * The ad tries to gain an unfair traffic advantage.
   */
  public const REASON_GAINING_AN_UNFAIR_ADVANTAGE = 'GAINING_AN_UNFAIR_ADVANTAGE';
  /**
   * The ad tries to circumvent Google's advertising systems.
   */
  public const REASON_GAMING_THE_GOOGLE_NETWORK = 'GAMING_THE_GOOGLE_NETWORK';
  /**
   * The ad promotes dangerous knives.
   */
  public const REASON_DANGEROUS_PRODUCTS_KNIVES = 'DANGEROUS_PRODUCTS_KNIVES';
  /**
   * The ad promotes explosives.
   */
  public const REASON_DANGEROUS_PRODUCTS_EXPLOSIVES = 'DANGEROUS_PRODUCTS_EXPLOSIVES';
  /**
   * The ad promotes guns & parts.
   */
  public const REASON_DANGEROUS_PRODUCTS_GUNS = 'DANGEROUS_PRODUCTS_GUNS';
  /**
   * The ad promotes recreational drugs/services & related equipment.
   */
  public const REASON_DANGEROUS_PRODUCTS_DRUGS = 'DANGEROUS_PRODUCTS_DRUGS';
  /**
   * The ad promotes tobacco products/services & related equipment.
   */
  public const REASON_DANGEROUS_PRODUCTS_TOBACCO = 'DANGEROUS_PRODUCTS_TOBACCO';
  /**
   * The ad promotes weapons.
   */
  public const REASON_DANGEROUS_PRODUCTS_WEAPONS = 'DANGEROUS_PRODUCTS_WEAPONS';
  /**
   * The ad is unclear or irrelevant to the destination site.
   */
  public const REASON_UNCLEAR_OR_IRRELEVANT_AD = 'UNCLEAR_OR_IRRELEVANT_AD';
  /**
   * The ad does not meet professional standards.
   */
  public const REASON_PROFESSIONAL_STANDARDS = 'PROFESSIONAL_STANDARDS';
  /**
   * The promotion is unnecessarily difficult to navigate.
   */
  public const REASON_DYSFUNCTIONAL_PROMOTION = 'DYSFUNCTIONAL_PROMOTION';
  /**
   * Violation of Google's policy for interest-based ads.
   */
  public const REASON_INVALID_INTEREST_BASED_AD = 'INVALID_INTEREST_BASED_AD';
  /**
   * Misuse of personal information.
   */
  public const REASON_MISUSE_OF_PERSONAL_INFORMATION = 'MISUSE_OF_PERSONAL_INFORMATION';
  /**
   * Omission of relevant information.
   */
  public const REASON_OMISSION_OF_RELEVANT_INFORMATION = 'OMISSION_OF_RELEVANT_INFORMATION';
  /**
   * Unavailable promotions.
   */
  public const REASON_UNAVAILABLE_PROMOTIONS = 'UNAVAILABLE_PROMOTIONS';
  /**
   * Misleading or unrealistic promotions.
   */
  public const REASON_MISLEADING_PROMOTIONS = 'MISLEADING_PROMOTIONS';
  /**
   * Offensive or inappropriate content.
   */
  public const REASON_INAPPROPRIATE_CONTENT = 'INAPPROPRIATE_CONTENT';
  /**
   * Capitalizing on sensitive events.
   */
  public const REASON_SENSITIVE_EVENTS = 'SENSITIVE_EVENTS';
  /**
   * Shocking content.
   */
  public const REASON_SHOCKING_CONTENT = 'SHOCKING_CONTENT';
  /**
   * Products & Services that enable dishonest behavior.
   */
  public const REASON_ENABLING_DISHONEST_BEHAVIOR = 'ENABLING_DISHONEST_BEHAVIOR';
  /**
   * The ad does not meet technical requirements.
   */
  public const REASON_TECHNICAL_REQUIREMENTS = 'TECHNICAL_REQUIREMENTS';
  /**
   * Restricted political content.
   */
  public const REASON_RESTRICTED_POLITICAL_CONTENT = 'RESTRICTED_POLITICAL_CONTENT';
  /**
   * Unsupported content.
   */
  public const REASON_UNSUPPORTED_CONTENT = 'UNSUPPORTED_CONTENT';
  /**
   * Invalid bidding method.
   */
  public const REASON_INVALID_BIDDING_METHOD = 'INVALID_BIDDING_METHOD';
  /**
   * Video length exceeds limits.
   */
  public const REASON_VIDEO_TOO_LONG = 'VIDEO_TOO_LONG';
  /**
   * Unacceptable content: Japanese healthcare.
   */
  public const REASON_VIOLATES_JAPANESE_PHARMACY_LAW = 'VIOLATES_JAPANESE_PHARMACY_LAW';
  /**
   * Online pharmacy ID required.
   */
  public const REASON_UNACCREDITED_PET_PHARMACY = 'UNACCREDITED_PET_PHARMACY';
  /**
   * Unacceptable content: Abortion.
   */
  public const REASON_ABORTION = 'ABORTION';
  /**
   * Unacceptable content: Birth control.
   */
  public const REASON_CONTRACEPTIVES = 'CONTRACEPTIVES';
  /**
   * Restricted in China.
   */
  public const REASON_NEED_CERTIFICATES_TO_ADVERTISE_IN_CHINA = 'NEED_CERTIFICATES_TO_ADVERTISE_IN_CHINA';
  /**
   * Unacceptable content: Korean healthcare.
   */
  public const REASON_KCDSP_REGISTRATION = 'KCDSP_REGISTRATION';
  /**
   * Non-family safe or adult content.
   */
  public const REASON_NOT_FAMILY_SAFE = 'NOT_FAMILY_SAFE';
  /**
   * Clinical trial recruitment.
   */
  public const REASON_CLINICAL_TRIAL_RECRUITMENT = 'CLINICAL_TRIAL_RECRUITMENT';
  /**
   * Maximum number of HTTP calls exceeded.
   */
  public const REASON_MAXIMUM_NUMBER_OF_HTTP_CALLS_EXCEEDED = 'MAXIMUM_NUMBER_OF_HTTP_CALLS_EXCEEDED';
  /**
   * Maximum number of cookies exceeded.
   */
  public const REASON_MAXIMUM_NUMBER_OF_COOKIES_EXCEEDED = 'MAXIMUM_NUMBER_OF_COOKIES_EXCEEDED';
  /**
   * Financial service ad does not adhere to specifications.
   */
  public const REASON_PERSONAL_LOANS = 'PERSONAL_LOANS';
  /**
   * Flash content was found in an unsupported context.
   */
  public const REASON_UNSUPPORTED_FLASH_CONTENT = 'UNSUPPORTED_FLASH_CONTENT';
  /**
   * Misuse by an Open Measurement SDK script.
   */
  public const REASON_MISUSE_BY_OMID_SCRIPT = 'MISUSE_BY_OMID_SCRIPT';
  /**
   * Use of an Open Measurement SDK vendor not on approved vendor list.
   */
  public const REASON_NON_WHITELISTED_OMID_VENDOR = 'NON_WHITELISTED_OMID_VENDOR';
  /**
   * Unacceptable landing page.
   */
  public const REASON_DESTINATION_EXPERIENCE = 'DESTINATION_EXPERIENCE';
  /**
   * Unsupported language.
   */
  public const REASON_UNSUPPORTED_LANGUAGE = 'UNSUPPORTED_LANGUAGE';
  /**
   * Non-SSL compliant.
   */
  public const REASON_NON_SSL_COMPLIANT = 'NON_SSL_COMPLIANT';
  /**
   * Temporary pausing of creative.
   */
  public const REASON_TEMPORARY_PAUSE = 'TEMPORARY_PAUSE';
  /**
   * Promotes services related to bail bonds.
   */
  public const REASON_BAIL_BONDS = 'BAIL_BONDS';
  /**
   * Promotes speculative and/or experimental medical treatments.
   */
  public const REASON_EXPERIMENTAL_MEDICAL_TREATMENT = 'EXPERIMENTAL_MEDICAL_TREATMENT';
  protected $collection_key = 'details';
  /**
   * Additional details about the reason for disapproval.
   *
   * @var string[]
   */
  public $details;
  /**
   * The categorized reason for disapproval.
   *
   * @var string
   */
  public $reason;

  /**
   * Additional details about the reason for disapproval.
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The categorized reason for disapproval.
   *
   * Accepted values: LENGTH_OF_IMAGE_ANIMATION, BROKEN_URL,
   * MEDIA_NOT_FUNCTIONAL, INVALID_FOURTH_PARTY_CALL,
   * INCORRECT_REMARKETING_DECLARATION, LANDING_PAGE_ERROR,
   * AD_SIZE_DOES_NOT_MATCH_AD_SLOT, NO_BORDER, FOURTH_PARTY_BROWSER_COOKIES,
   * LSO_OBJECTS, BLANK_CREATIVE, DESTINATION_URLS_UNDECLARED,
   * PROBLEM_WITH_CLICK_MACRO, INCORRECT_AD_TECHNOLOGY_DECLARATION,
   * INCORRECT_DESTINATION_URL_DECLARATION, EXPANDABLE_INCORRECT_DIRECTION,
   * EXPANDABLE_DIRECTION_NOT_SUPPORTED, EXPANDABLE_INVALID_VENDOR,
   * EXPANDABLE_FUNCTIONALITY, VIDEO_INVALID_VENDOR, VIDEO_UNSUPPORTED_LENGTH,
   * VIDEO_UNSUPPORTED_FORMAT, VIDEO_FUNCTIONALITY, LANDING_PAGE_DISABLED,
   * MALWARE_SUSPECTED, ADULT_IMAGE_OR_VIDEO, INACCURATE_AD_TEXT,
   * COUNTERFEIT_DESIGNER_GOODS, POP_UP, INVALID_RTB_PROTOCOL_USAGE,
   * RAW_IP_ADDRESS_IN_SNIPPET, UNACCEPTABLE_CONTENT_SOFTWARE,
   * UNAUTHORIZED_COOKIE_ON_GOOGLE_DOMAIN, UNDECLARED_FLASH_OBJECTS,
   * INVALID_SSL_DECLARATION, DIRECT_DOWNLOAD_IN_AD,
   * MAXIMUM_DOWNLOAD_SIZE_EXCEEDED, DESTINATION_URL_SITE_NOT_CRAWLABLE,
   * BAD_URL_LEGAL_DISAPPROVAL, PHARMA_GAMBLING_ALCOHOL_NOT_ALLOWED,
   * DYNAMIC_DNS_AT_DESTINATION_URL, POOR_IMAGE_OR_VIDEO_QUALITY,
   * UNACCEPTABLE_IMAGE_CONTENT, INCORRECT_IMAGE_LAYOUT,
   * IRRELEVANT_IMAGE_OR_VIDEO, DESTINATION_SITE_DOES_NOT_ALLOW_GOING_BACK,
   * MISLEADING_CLAIMS_IN_AD, RESTRICTED_PRODUCTS, UNACCEPTABLE_CONTENT,
   * AUTOMATED_AD_CLICKING, INVALID_URL_PROTOCOL, UNDECLARED_RESTRICTED_CONTENT,
   * INVALID_REMARKETING_LIST_USAGE, DESTINATION_SITE_NOT_CRAWLABLE_ROBOTS_TXT,
   * CLICK_TO_DOWNLOAD_NOT_AN_APP, INACCURATE_REVIEW_EXTENSION,
   * SEXUALLY_EXPLICIT_CONTENT, GAINING_AN_UNFAIR_ADVANTAGE,
   * GAMING_THE_GOOGLE_NETWORK, DANGEROUS_PRODUCTS_KNIVES,
   * DANGEROUS_PRODUCTS_EXPLOSIVES, DANGEROUS_PRODUCTS_GUNS,
   * DANGEROUS_PRODUCTS_DRUGS, DANGEROUS_PRODUCTS_TOBACCO,
   * DANGEROUS_PRODUCTS_WEAPONS, UNCLEAR_OR_IRRELEVANT_AD,
   * PROFESSIONAL_STANDARDS, DYSFUNCTIONAL_PROMOTION, INVALID_INTEREST_BASED_AD,
   * MISUSE_OF_PERSONAL_INFORMATION, OMISSION_OF_RELEVANT_INFORMATION,
   * UNAVAILABLE_PROMOTIONS, MISLEADING_PROMOTIONS, INAPPROPRIATE_CONTENT,
   * SENSITIVE_EVENTS, SHOCKING_CONTENT, ENABLING_DISHONEST_BEHAVIOR,
   * TECHNICAL_REQUIREMENTS, RESTRICTED_POLITICAL_CONTENT, UNSUPPORTED_CONTENT,
   * INVALID_BIDDING_METHOD, VIDEO_TOO_LONG, VIOLATES_JAPANESE_PHARMACY_LAW,
   * UNACCREDITED_PET_PHARMACY, ABORTION, CONTRACEPTIVES,
   * NEED_CERTIFICATES_TO_ADVERTISE_IN_CHINA, KCDSP_REGISTRATION,
   * NOT_FAMILY_SAFE, CLINICAL_TRIAL_RECRUITMENT,
   * MAXIMUM_NUMBER_OF_HTTP_CALLS_EXCEEDED, MAXIMUM_NUMBER_OF_COOKIES_EXCEEDED,
   * PERSONAL_LOANS, UNSUPPORTED_FLASH_CONTENT, MISUSE_BY_OMID_SCRIPT,
   * NON_WHITELISTED_OMID_VENDOR, DESTINATION_EXPERIENCE, UNSUPPORTED_LANGUAGE,
   * NON_SSL_COMPLIANT, TEMPORARY_PAUSE, BAIL_BONDS,
   * EXPERIMENTAL_MEDICAL_TREATMENT
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Disapproval::class, 'Google_Service_AdExchangeBuyerII_Disapproval');
