<?php
require_once dirname(dirname(dirname(__FILE__))) . '/Response.php';

class Mercantile_Gateways_AuthNetArb_Response extends Mercantile_Response
{
	const SUBSCRIPTION_ID = 'subscriptionId';

	public function getSubscriptionId()
	{
		$params = $this->getParams();

		return $params[self::SUBSCRIPTION_ID];
	}
}
