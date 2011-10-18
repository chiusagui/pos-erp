<?php
/**
  * GET api/cliente/lista
  * Obtener la lista de clientes.
  *
  * Regresa una lista de clientes. Puede filtrarse por empresa, sucursal, activos, as?omo ordenarse seg?us atributs con el par?tro orden. Es posible que algunos clientes sean dados de alta por un admnistrador que no les asigne algun id_empresa, o id_sucursal.

Update :  �Es correcto que contenga el argumento id_sucursal? Ya que as?omo esta entiendo que solo te regresara los datos de los clientes de una sola sucursal.
  *
  *
  *
  **/
require_once("../../../../../../server/bootstrap.php");
require_once("api/api.cliente.lista.php");

$api = new ApiClienteLista();
$apiOutput = ApiOutputFormatter::getInstance();
$apiOutput->PrintOuput($api);