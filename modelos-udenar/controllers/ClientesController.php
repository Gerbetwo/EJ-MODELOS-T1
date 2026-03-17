<?php
class ClientesController extends GenericController
{
    public function __construct($mysqli)
    {
        parent::__construct($mysqli, 'clientes');
    }
    // Aquí puedes añadir métodos específicos para clientes después
}
