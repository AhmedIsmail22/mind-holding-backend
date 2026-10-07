<?php

function specSchema(string $name): array
{
    return json_decode(file_get_contents(base_path('docs/openapi.json')), true)['components']['schemas'][$name];
}

it('types translatable fields as strings in public list and detail resources', function () {
    expect(specSchema('ServiceListResource')['properties']['name']['type'])->toBe('string');
    expect(specSchema('ServiceResource')['properties']['description']['type'])->toBe('string');
    expect(specSchema('SolutionListResource')['properties']['audience']['type'])->toBe('string');
    expect(specSchema('SolutionListResource')['properties']['industry']['properties']['name']['type'])->toBe('string');
    expect(specSchema('FaqResource')['properties']['question']['type'])->toBe('string');
    expect(specSchema('PageResource')['properties']['title']['type'])->toBe('string');
    expect(specSchema('ProjectResource')['properties']['overview']['type'])->toBe('string');
});

it('types image width and height as integers', function () {
    $thumbnail = specSchema('SolutionListResource')['properties']['thumbnail'];

    expect($thumbnail['type'])->toContain('object');
    expect($thumbnail['properties']['width']['type'])->toContain('integer');
    expect($thumbnail['properties']['height']['type'])->toContain('integer');
});

it('types the technology id as an integer', function () {
    expect(specSchema('TechnologyResource')['properties']['id']['type'])->toBe('integer');
});

it('documents a summary on the public project list', function () {
    expect(specSchema('ProjectListResource')['properties'])->toHaveKey('summary');
});

it('types settings gulf_countries as a string array and social_links as a string map', function () {
    foreach (['SettingsResource', 'App.Http.Resources.Admin.SettingsResource'] as $name) {
        $schema = specSchema($name);
        expect($schema['properties']['gulf_countries']['type'])->toBe('array');
        expect($schema['properties']['gulf_countries']['items']['type'])->toBe('string');
        expect($schema['properties']['social_links']['type'])->toBe('object');
        expect($schema['properties']['social_links']['additionalProperties']['type'])->toBe('string');
    }
});

it('types seo title and description as nullable strings, not arrays', function () {
    $seo = specSchema('SeoResource');
    expect($seo['properties']['title']['type'])->toBe(['string', 'null']);
    expect($seo['properties']['description']['type'])->toBe(['string', 'null']);

    // Each union branch covers a different fallback case (has its own SEO
    // row, or falls back to the entity's own name/description, or neither),
    // so title/description land on "string", "null", or ["string","null"]
    // depending on the branch - never "array", which is the bug being fixed.
    $serviceSeoBranches = specSchema('ServiceResource')['properties']['seo']['anyOf'];

    foreach ($serviceSeoBranches as $branch) {
        expect($branch['properties']['title']['type'])->not->toBe('array');
        expect($branch['properties']['description']['type'])->not->toBe('array');
    }
});
