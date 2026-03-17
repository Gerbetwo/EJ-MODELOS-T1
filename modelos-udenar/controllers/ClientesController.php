<?php
class ClientesController extends GenericController
{
    public function __construct($mysqli)
    {
        parent::__construct($mysqli, 'clientes');
    }
}
