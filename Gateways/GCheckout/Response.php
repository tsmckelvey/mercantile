<?php
class Mercantile_Gateways_GCheckout_Response extends Mercantile_Response
{
	public function getRedirectUrl()
	{
		// @TODO: make sure this doesn't conflict
		return $this->_params['redirect-url'];
	}
}
