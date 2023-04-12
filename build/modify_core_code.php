<?php

$dockerComposePath = __DIR__ . '/../testing-docker/docker-compose.yml';

$dockerCompose = file_get_contents($dockerComposePath);

$dockerCompose = str_replace(
    'find typo3/ -name \\\\*.php',
    'find . -name \\\\*.php ! -path "./.Build/\\\\*"',
    $dockerCompose
);

file_put_contents($dockerComposePath, $dockerCompose);


$t3RunTestsPath = __DIR__ . '/../bin/t3_run_tests.sh';

$t3RunTests = file_get_contents($t3RunTestsPath);

$t3RunTests = str_replace(
    [
        './Build/Scripts/runTests.sh',
        '.Build/Scripts/runTests.sh',
    ],
    [
        './.Build/bin/t3_run_tests.sh',
        './.Build/bin/t3_run_tests.sh',
    ],
    $t3RunTests
);

file_put_contents($t3RunTestsPath, $t3RunTests);
