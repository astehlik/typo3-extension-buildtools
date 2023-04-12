<?php

$dockerComposePath = __DIR__ . '/../testing-docker/docker-compose.yml';

$dockerCompose = file_get_contents($dockerComposePath);

$dockerCompose = str_replace(
    'find typo3/ -name \\\\*.php',
    'find . -name \\\\*.php ! -path "./.Build/\\\\*"',
    $dockerCompose
);

file_put_contents($dockerComposePath, $dockerCompose);
