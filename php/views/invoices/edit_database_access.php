<?php

//making  context visible
global $context;

function new_invoice_insert ($insert) {
    $conn_id = $context->db;
    $conn_id->beginTransaction ();
    //hopefully this will change
    $conn_id->query($insert);
}

function update_invoice ($update) {
    $conn_id = $context->db;
    $conn_id->beginTransaction ();
    $conn_id->query($update);
}
