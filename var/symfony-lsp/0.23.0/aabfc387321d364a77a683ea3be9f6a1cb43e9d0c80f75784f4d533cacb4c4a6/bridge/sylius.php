<?php














function symfonyLspBridgeSyliusThemePaths(SymfonyLspBridgeContext $context): array
{
try {
if (!$context->hasExtension('sylius_theme')) {
return [];
}
$configuration = $context->configuration('sylius_theme');
} catch (Throwable) {
return [];
}
$filesystem = is_array($configuration) ? $configuration['sources']['filesystem'] ?? null : null;
if (!is_array($filesystem) || false === ($filesystem['enabled'] ?? true)) {
return [];
}
$filename = is_string($filesystem['filename'] ?? null) && '' !== $filesystem['filename'] ? $filesystem['filename'] : 'composer.json';
$depth = is_numeric($filesystem['scan_depth'] ?? null) ? max(0, (int) $filesystem['scan_depth']) : 1;
$project = rtrim($context->project(), '/\\');
$paths = [];
foreach (is_array($filesystem['directories'] ?? null) ? $filesystem['directories'] : [] as $directory) {
if (!is_string($directory) || '' === $directory) {
continue;
}
if (!preg_match('{^(?:/|[A-Za-z]:[\\\\/])}', $directory)) {
$directory = $project.'/'.$directory;
}
foreach (symfonyLspBridgeSyliusThemeDirectories($directory, $filename, $depth) as $theme) {
$templates = $theme.'/templates';
if (!is_dir($templates)) {
continue;
}
$paths[] = ['namespace' => '(None)', 'path' => $templates];
foreach (symfonyLspBridgeDirectoryEntries($templates.'/bundles') as $entry) {

$namespace = str_ends_with($entry, 'Bundle') ? substr($entry, 0, -6) : $entry;
$paths[] = ['namespace' => '@'.$namespace, 'path' => $templates.'/bundles/'.$entry];
}
}
}

return $paths;
}





function symfonyLspBridgeSyliusThemeDirectories(string $directory, string $filename, int $depth): array
{
if (!is_dir($directory)) {
return [];
}
$themes = is_file($directory.'/'.$filename) ? [$directory] : [];
if ($depth > 0) {
foreach (symfonyLspBridgeDirectoryEntries($directory) as $entry) {
$themes = array_merge($themes, symfonyLspBridgeSyliusThemeDirectories($directory.'/'.$entry, $filename, $depth - 1));
}
}

return $themes;
}
