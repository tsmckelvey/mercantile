<?php
/**
 * Response to MerchantCalculationCallback
 */
class Mercantile_Gateways_GCheckout_MerchantCalculationResults extends DomDocument
{
	const MERCHANT_CALCULATION_RESULTS = 'merchant-calculation-results';

	const RESULTS = 'results';

	const RESULT = 'result';

	protected $_resultsNode = null;

	public function __construct()
	{
		parent::__construct('1.0', 'utf-8');

		$this->formatOutput = true;

		$this->appendChild(new DomElement(self::MERCHANT_CALCULATION_RESULTS));

		$this->documentElement->setAttribute('xmlns', 
			Mercanilte_Gateways_GCheckout_Checkout::CHECKOUT_XML_SCHEMA);
		
		$this->_resultsNode = $this->documentElement->appendChild(new DomElement(self::RESULTS));
	}

	public function addResult(Mercantile_Gateways_GCheckout_MerchantCalculationResults_Result $result)
	{
		$resultElement = $this->importNode($result->documentElement, $deep = true);

		$this->_resultsNode->appendChild($resultElement);

		return $this;
	}

	public function __toString()
	{
		return $this->saveXML();
	}
}
