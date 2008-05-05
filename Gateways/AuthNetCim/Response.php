<?php
class Mercantile_Gateways_AuthNetCim_Response extends Mercantile_Response
{
    public function getCustomerProfileId()
    {
        $params = $this->getParams();

        return $params['customerProfileId'];
    }
    public function getCustomerProfile()
    {
        $params = $this->getParams();

        return $params['customerProfile'];
    }
}
