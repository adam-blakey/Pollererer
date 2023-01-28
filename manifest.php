<?php

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$manifest = [
    'short_name' => $config["software_name"],
    'name' => $config["software_name"],
    'id' => '/?source=pwa',
    'icons' => [
        [
            'src' => 'static/Blackwood-Icon.png',
            'type' => 'image/png',
            'sizes' => '512x512'
        ]
    ],
    'start_url' => '/?source=pwa',
    'background_color' => '#e6e6e6',
    'display' => 'standalone',
    'scope' => '/',
    'theme_color' => '#b3954c'
  ];

header('Content-Type: application/json');
echo json_encode($manifest);

?>