<?php

/**
 *
 */
/**
 *
 */
require_once('../model/clientes.vo.php');

/**
 *
 */
require_once('../model/clientes.dao.php');

/**
 *
 * @param <type> $rfc
 * @param <type> $nombre
 * @param <type> $direccion
 * @param <type> $limite_credito
 * @param <type> $descuento
 * @param <type> $telefono
 * @param <type> $e_mail
 * @return string JSON con la respuesta para el cliente.
 * @todo validad RFC con expresión regular.
 * @access private
 */
function save_customer($id, $rfc, $nombre, $direccion, $limite_credito, $descuento = 0, $telefono = null, $e_mail = null) {
    $data = array('rfc' => $rfc, 'nombre' => $nombre, 'telefono' => $telefono, 'e_mail' => $e_mail, 'limite_credito' => $limite_credito, 'descuento' => $descuento, 'direccion' => $direccion);

    if ($limite_credito < 0.0) {
        return "{success: false, reason: 'El limite de crédito debe ser mayor a cero.' }";
    }

    if ($descuento < 0.0 || $descuento > 100) {
        return "{success: false, reason: 'El limite de crédito debe estar entre cero y cien.' }";
    }

    //validar RFC

    $cliente = new Cliente($data);
    $ans = ClienteDAO::save($cliente);

    if ($ans) {
        return sprintf("{success: true, reason: 'Se inserto el cliente con id %s'}", $cliente->getIdCliente());
    } else {
        return "{success: false, reason: 'No se inserto el cliente.' }";
    }
}

/**
 *
 * @param <type> $id
 * @param <type> $rfc
 * @param <type> $nombre
 * @param <type> $direccion
 * @param <type> $limite_credito
 * @param <type> $descuento
 * @param <type> $telefono
 * @param <type> $e_mail
 */
function update_customer($id, $rfc, $nombre, $direccion, $limite_credito, $descuento = 0, $telefono = null, $e_mail = null) {
    save_customer($id, $rfc, $nombre, $direccion, $limite_credito, $descuento, $telefono, $e_mail);
}

/**
 *
 * @param <type> $rfc
 * @param <type> $nombre
 * @param <type> $direccion
 * @param <type> $limite_credito
 * @param <type> $descuento
 * @param <type> $telefono
 * @param <type> $e_mail 
 */
function insert_customer($rfc, $nombre, $direccion, $limite_credito, $descuento = 0, $telefono = null, $e_mail = null) {
    save_customer(null, $rfc, $nombre, $direccion, $limite_credito, $descuento, $telefono, $e_mail);
}

/**
 *
 * @param <type> $id_cliente 
 */
function delete_customer($id_cliente) {
    if (!is_int($id_cliente))
        return "{success: false, reason: 'Id no válido.' }";
    }
    $cliente = ClienteDAO::getByPK($id_cliente);
    if (is_object($cliente)) {
        $ans = ClienteDAO::delete($cliente);

        if ($ans) {
            return sprintf("{success: true, reason: 'Se borro el cliente con id %s'}", $cliente->getIdCliente());
        } else {
            return "{success: false, reason: 'No se borro el cliente.' }";
        }
    } else {
        return "{success: false, reason: 'No existe cliente con ese id.' }";
    }
}

function list_customers() {
    $clientes = ClienteDAO::getAll();

    $ans = '';
    foreach ($clientes as $cliente) :
            $ans .= sprintf("%s,", $cliente->getJSON());
    endforeach;

    $ans = sprint("datos:[%s]", $ans);

    return str_replace("},]", "}]", $ans);
    /*
        $clientes = ClienteDAO::customQuery($query, $params);

       //clientes almacena el record set que envia adodb

     */
}

/**
 *
 * @param <type> $id_cliente 
 */
function show_customer($id_cliente) {
    if (!is_int($id_cliente)) {
        return "{success: false, reason: 'Id no válido.' }";
    }
    $cliente = ClienteDAO::getByPK($id_cliente);
    if (is_object($cliente)) {
        return sprintf("success: true, datos: %s", $cliente->getJSON());
    } else {
        return "{success: false, reason: 'No existe cliente con ese id.' }";
    }
}