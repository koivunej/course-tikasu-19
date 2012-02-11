<?php

function new_invoice_insert ($insert) {
    //making  context visible                                                                                                                                     
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction ();
    //hopefully this will change
    $conn_id->query($insert);
}

function update_invoice ($update) {
    //making  context visible                                                                                                                                     
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction ();
    $conn_id->query($update);
}

function select_invoices ($select) {
    //making  context visible                                                                                                                                     
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction();
    return $conn_id->query ($select);
}
