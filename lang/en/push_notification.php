<?php
return [
    'accept' => [
        'title' => 'Order Accepted!',
        'body' => 'order has been accepted successfully.',
        'type' => 'order_update',
    ],
    'reject' => [
        'title' => 'Order Declined!',
        'body' => 'We are sorry to inform you that we are unable to process your order right now.',
        'type' => 'order_update',
        'notification_body' => 'order has been declined.',
    ],
    'inprogress' => [
        'title' => 'Order In Progress!',
        'body' => 'order has been in Progress.',
        'type' => 'order_update',
    ],
    'delivered' => [
        'title' => 'Order Delivered!',
        'body' => 'order has been delivered successfully.',
        'type' => 'order_update',
    ],
    'offer' => [
        'type' => 'offer_update',
    ],
    "chat" => [
        'title' => 'send new message!',
        'body' => '',
        'type' => 'chat',
    ],
    'prescription' => [
        'accept' => [
            'title' => 'Prescription Accepted!',
            'body' => 'Prescription has been accepted successfully.',
            'type' => 'prescription_update',
        ],
        'reject' => [
            'title' => 'Prescription Declined!',
            'body' => 'We are sorry to inform you that we are unable to process your prescription right now.',
            'type' => 'prescription_update',
        ],
        'order_create' => [
            'title' => 'Prescription Order Created!',
            'body' => 'prescription order has been created successfully.',
            'type' => 'order_update',
        ],
    ],
    'offer' => [
        'type' => 'offer_update',
    ],
    'order_product_update' => [
        'title' => 'Order Updated!',
        'body' => 'order has been updated successfully.',
        'type' => 'order_product_update',
    ],
];