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
