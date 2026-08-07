#!/usr/bin/env php
<?php

declare(strict_types=1);

$emConfPath = getcwd() . '/ext_emconf.php';
if (is_file($emConfPath)) {
    echo "ext_emconf.php already exists, skipping generation.\n";

    exit(0);
}

$composerJsonPath = getcwd() . '/composer.json';
if (!is_file($composerJsonPath)) {
    fwrite(STDERR, 'composer.json not found in ' . getcwd() . "\n");

    exit(1);
}

$composerJson = json_decode((string)file_get_contents($composerJsonPath), true, 512, JSON_THROW_ON_ERROR);

$extensionKey = getenv('TYPO3_EXTENSION_KEY');
if ($extensionKey === false || $extensionKey === '') {
    fwrite(STDERR, "The TYPO3_EXTENSION_KEY env var is not set.\n");

    exit(1);
}

$version = $composerJson['version'] ?? null;
if (!is_string($version) || $version === '') {
    fwrite(STDERR, "composer.json does not contain a \"version\" field.\n");

    exit(1);
}

$typo3Constraint = $composerJson['require']['typo3/cms-core'] ?? null;
if (!is_string($typo3Constraint) || $typo3Constraint === '') {
    fwrite(STDERR, "composer.json does not require \"typo3/cms-core\".\n");

    exit(1);
}

if (!preg_match('/(\\d+)\\.(\\d+)/', $typo3Constraint, $matches)) {
    fwrite(STDERR, "Could not parse a TYPO3 version from constraint \"{$typo3Constraint}\".\n");

    exit(1);
}
// Assumes a single supported TYPO3 major/minor per branch, matching this
// project's "one core version per buildtools version" convention.
[, $major, $minor] = $matches;
$typo3DependsRange = "{$major}.{$minor}.0-{$major}.99.99";

$description = $composerJson['description'] ?? '';

$content = sprintf(
    <<<'PHP'
        <?php

        $EM_CONF[$_EXTKEY] = [
            'title' => %s,
            'description' => %s,
            'category' => 'misc',
            'state' => 'stable',
            'version' => %s,
            'constraints' => [
                'depends' => [
                    'typo3' => %s,
                ],
            ],
        ];

        PHP,
    var_export($extensionKey, true),
    var_export($description, true),
    var_export($version, true),
    var_export($typo3DependsRange, true),
);

file_put_contents($emConfPath, $content);

echo "Generated minimal ext_emconf.php for {$extensionKey} {$version}.\n";
