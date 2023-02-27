<?php

namespace Module\Tenancy\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class TenantConnection extends Connection
{
    /**
     * @param string $database
     * @return void
     * @throws Exception
     */
    public function changeDatabase(string $database): void
    {
        if ($this->isConnected()) {
            $this->close();
        }

        $params = $this->getParams();
        $params['dbname'] = $database;
        parent::__construct($params, $this->_driver, $this->_config, $this->_eventManager);
    }
}
