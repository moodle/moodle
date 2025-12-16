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

namespace Google\Service\DisplayVideo;

class Invoice extends \Google\Collection
{
  /**
   * Not specified or is unknown in this version.
   */
  public const INVOICE_TYPE_INVOICE_TYPE_UNSPECIFIED = 'INVOICE_TYPE_UNSPECIFIED';
  /**
   * The invoice has a negative amount.
   */
  public const INVOICE_TYPE_INVOICE_TYPE_CREDIT = 'INVOICE_TYPE_CREDIT';
  /**
   * The invoice has a positive amount.
   */
  public const INVOICE_TYPE_INVOICE_TYPE_INVOICE = 'INVOICE_TYPE_INVOICE';
  protected $collection_key = 'replacedInvoiceIds';
  /**
   * The budget grouping ID for this invoice. This field will only be set if the
   * invoice level of the corresponding billing profile was set to "Budget
   * invoice grouping ID".
   *
   * @var string
   */
  public $budgetInvoiceGroupingId;
  protected $budgetSummariesType = BudgetSummary::class;
  protected $budgetSummariesDataType = 'array';
  /**
   * The ID of the original invoice being adjusted by this invoice, if
   * applicable. May appear on the invoice PDF as `Reference invoice number`. If
   * replaced_invoice_ids is set, this field will be empty.
   *
   * @var string
   */
  public $correctedInvoiceId;
  /**
   * The currency used in the invoice in ISO 4217 format.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * The display name of the invoice.
   *
   * @var string
   */
  public $displayName;
  protected $dueDateType = Date::class;
  protected $dueDateDataType = '';
  /**
   * The unique ID of the invoice.
   *
   * @var string
   */
  public $invoiceId;
  /**
   * The type of invoice document.
   *
   * @var string
   */
  public $invoiceType;
  protected $issueDateType = Date::class;
  protected $issueDateDataType = '';
  /**
   * The resource name of the invoice.
   *
   * @var string
   */
  public $name;
  /**
   * The total amount of costs or adjustments not tied to a particular budget,
   * in micros of the invoice's currency. For example, if currency_code is
   * `USD`, then 1000000 represents one US dollar.
   *
   * @var string
   */
  public $nonBudgetMicros;
  /**
   * The ID of the payments account the invoice belongs to. Appears on the
   * invoice PDF as `Billing Account Number`.
   *
   * @var string
   */
  public $paymentsAccountId;
  /**
   * The ID of the payments profile the invoice belongs to. Appears on the
   * invoice PDF as `Billing ID`.
   *
   * @var string
   */
  public $paymentsProfileId;
  /**
   * The URL to download a PDF copy of the invoice. This URL is user specific
   * and requires a valid OAuth 2.0 access token to access. The access token
   * must be provided in an `Authorization: Bearer` HTTP header and be
   * authorized for one of the following scopes: *
   * `https://www.googleapis.com/auth/display-video-mediaplanning` *
   * `https://www.googleapis.com/auth/display-video` The URL will be valid for 7
   * days after retrieval of this invoice object or until this invoice is
   * retrieved again.
   *
   * @var string
   */
  public $pdfUrl;
  /**
   * Purchase order number associated with the invoice.
   *
   * @var string
   */
  public $purchaseOrderNumber;
  /**
   * The ID(s) of any originally issued invoice that is being cancelled by this
   * invoice, if applicable. Multiple invoices may be listed if those invoices
   * are being consolidated into a single invoice. May appear on invoice PDF as
   * `Replaced invoice numbers`. If corrected_invoice_id is set, this field will
   * be empty.
   *
   * @var string[]
   */
  public $replacedInvoiceIds;
  protected $serviceDateRangeType = DateRange::class;
  protected $serviceDateRangeDataType = '';
  /**
   * The pre-tax subtotal amount, in micros of the invoice's currency. For
   * example, if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @var string
   */
  public $subtotalAmountMicros;
  /**
   * The invoice total amount, in micros of the invoice's currency. For example,
   * if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @var string
   */
  public $totalAmountMicros;
  /**
   * The sum of all taxes in invoice, in micros of the invoice's currency. For
   * example, if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @var string
   */
  public $totalTaxAmountMicros;

  /**
   * The budget grouping ID for this invoice. This field will only be set if the
   * invoice level of the corresponding billing profile was set to "Budget
   * invoice grouping ID".
   *
   * @param string $budgetInvoiceGroupingId
   */
  public function setBudgetInvoiceGroupingId($budgetInvoiceGroupingId)
  {
    $this->budgetInvoiceGroupingId = $budgetInvoiceGroupingId;
  }
  /**
   * @return string
   */
  public function getBudgetInvoiceGroupingId()
  {
    return $this->budgetInvoiceGroupingId;
  }
  /**
   * The list of summarized information for each budget associated with this
   * invoice. This field will only be set if the invoice detail level of the
   * corresponding billing profile was set to "Budget level PO".
   *
   * @param BudgetSummary[] $budgetSummaries
   */
  public function setBudgetSummaries($budgetSummaries)
  {
    $this->budgetSummaries = $budgetSummaries;
  }
  /**
   * @return BudgetSummary[]
   */
  public function getBudgetSummaries()
  {
    return $this->budgetSummaries;
  }
  /**
   * The ID of the original invoice being adjusted by this invoice, if
   * applicable. May appear on the invoice PDF as `Reference invoice number`. If
   * replaced_invoice_ids is set, this field will be empty.
   *
   * @param string $correctedInvoiceId
   */
  public function setCorrectedInvoiceId($correctedInvoiceId)
  {
    $this->correctedInvoiceId = $correctedInvoiceId;
  }
  /**
   * @return string
   */
  public function getCorrectedInvoiceId()
  {
    return $this->correctedInvoiceId;
  }
  /**
   * The currency used in the invoice in ISO 4217 format.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * The display name of the invoice.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The date when the invoice is due.
   *
   * @param Date $dueDate
   */
  public function setDueDate(Date $dueDate)
  {
    $this->dueDate = $dueDate;
  }
  /**
   * @return Date
   */
  public function getDueDate()
  {
    return $this->dueDate;
  }
  /**
   * The unique ID of the invoice.
   *
   * @param string $invoiceId
   */
  public function setInvoiceId($invoiceId)
  {
    $this->invoiceId = $invoiceId;
  }
  /**
   * @return string
   */
  public function getInvoiceId()
  {
    return $this->invoiceId;
  }
  /**
   * The type of invoice document.
   *
   * Accepted values: INVOICE_TYPE_UNSPECIFIED, INVOICE_TYPE_CREDIT,
   * INVOICE_TYPE_INVOICE
   *
   * @param self::INVOICE_TYPE_* $invoiceType
   */
  public function setInvoiceType($invoiceType)
  {
    $this->invoiceType = $invoiceType;
  }
  /**
   * @return self::INVOICE_TYPE_*
   */
  public function getInvoiceType()
  {
    return $this->invoiceType;
  }
  /**
   * The date when the invoice was issued.
   *
   * @param Date $issueDate
   */
  public function setIssueDate(Date $issueDate)
  {
    $this->issueDate = $issueDate;
  }
  /**
   * @return Date
   */
  public function getIssueDate()
  {
    return $this->issueDate;
  }
  /**
   * The resource name of the invoice.
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
   * The total amount of costs or adjustments not tied to a particular budget,
   * in micros of the invoice's currency. For example, if currency_code is
   * `USD`, then 1000000 represents one US dollar.
   *
   * @param string $nonBudgetMicros
   */
  public function setNonBudgetMicros($nonBudgetMicros)
  {
    $this->nonBudgetMicros = $nonBudgetMicros;
  }
  /**
   * @return string
   */
  public function getNonBudgetMicros()
  {
    return $this->nonBudgetMicros;
  }
  /**
   * The ID of the payments account the invoice belongs to. Appears on the
   * invoice PDF as `Billing Account Number`.
   *
   * @param string $paymentsAccountId
   */
  public function setPaymentsAccountId($paymentsAccountId)
  {
    $this->paymentsAccountId = $paymentsAccountId;
  }
  /**
   * @return string
   */
  public function getPaymentsAccountId()
  {
    return $this->paymentsAccountId;
  }
  /**
   * The ID of the payments profile the invoice belongs to. Appears on the
   * invoice PDF as `Billing ID`.
   *
   * @param string $paymentsProfileId
   */
  public function setPaymentsProfileId($paymentsProfileId)
  {
    $this->paymentsProfileId = $paymentsProfileId;
  }
  /**
   * @return string
   */
  public function getPaymentsProfileId()
  {
    return $this->paymentsProfileId;
  }
  /**
   * The URL to download a PDF copy of the invoice. This URL is user specific
   * and requires a valid OAuth 2.0 access token to access. The access token
   * must be provided in an `Authorization: Bearer` HTTP header and be
   * authorized for one of the following scopes: *
   * `https://www.googleapis.com/auth/display-video-mediaplanning` *
   * `https://www.googleapis.com/auth/display-video` The URL will be valid for 7
   * days after retrieval of this invoice object or until this invoice is
   * retrieved again.
   *
   * @param string $pdfUrl
   */
  public function setPdfUrl($pdfUrl)
  {
    $this->pdfUrl = $pdfUrl;
  }
  /**
   * @return string
   */
  public function getPdfUrl()
  {
    return $this->pdfUrl;
  }
  /**
   * Purchase order number associated with the invoice.
   *
   * @param string $purchaseOrderNumber
   */
  public function setPurchaseOrderNumber($purchaseOrderNumber)
  {
    $this->purchaseOrderNumber = $purchaseOrderNumber;
  }
  /**
   * @return string
   */
  public function getPurchaseOrderNumber()
  {
    return $this->purchaseOrderNumber;
  }
  /**
   * The ID(s) of any originally issued invoice that is being cancelled by this
   * invoice, if applicable. Multiple invoices may be listed if those invoices
   * are being consolidated into a single invoice. May appear on invoice PDF as
   * `Replaced invoice numbers`. If corrected_invoice_id is set, this field will
   * be empty.
   *
   * @param string[] $replacedInvoiceIds
   */
  public function setReplacedInvoiceIds($replacedInvoiceIds)
  {
    $this->replacedInvoiceIds = $replacedInvoiceIds;
  }
  /**
   * @return string[]
   */
  public function getReplacedInvoiceIds()
  {
    return $this->replacedInvoiceIds;
  }
  /**
   * The service start and end dates which are covered by this invoice.
   *
   * @param DateRange $serviceDateRange
   */
  public function setServiceDateRange(DateRange $serviceDateRange)
  {
    $this->serviceDateRange = $serviceDateRange;
  }
  /**
   * @return DateRange
   */
  public function getServiceDateRange()
  {
    return $this->serviceDateRange;
  }
  /**
   * The pre-tax subtotal amount, in micros of the invoice's currency. For
   * example, if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @param string $subtotalAmountMicros
   */
  public function setSubtotalAmountMicros($subtotalAmountMicros)
  {
    $this->subtotalAmountMicros = $subtotalAmountMicros;
  }
  /**
   * @return string
   */
  public function getSubtotalAmountMicros()
  {
    return $this->subtotalAmountMicros;
  }
  /**
   * The invoice total amount, in micros of the invoice's currency. For example,
   * if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @param string $totalAmountMicros
   */
  public function setTotalAmountMicros($totalAmountMicros)
  {
    $this->totalAmountMicros = $totalAmountMicros;
  }
  /**
   * @return string
   */
  public function getTotalAmountMicros()
  {
    return $this->totalAmountMicros;
  }
  /**
   * The sum of all taxes in invoice, in micros of the invoice's currency. For
   * example, if currency_code is `USD`, then 1000000 represents one US dollar.
   *
   * @param string $totalTaxAmountMicros
   */
  public function setTotalTaxAmountMicros($totalTaxAmountMicros)
  {
    $this->totalTaxAmountMicros = $totalTaxAmountMicros;
  }
  /**
   * @return string
   */
  public function getTotalTaxAmountMicros()
  {
    return $this->totalTaxAmountMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Invoice::class, 'Google_Service_DisplayVideo_Invoice');
