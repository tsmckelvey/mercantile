<?php
require_once dirname(dirname(__FILE__)) . '/Response.php';

class Mercantile_Gateways_AuthNetArb_Response extends Mercantile_Response
{
	public function getSubscriptionId()
	{
		$params = $this->getParams();

		return $params['subscriptionId'];
	}
}
