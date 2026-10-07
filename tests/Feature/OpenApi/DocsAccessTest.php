<?php

it('serves the interactive docs and spec in local', function () {
    app()->detectEnvironment(fn () => 'local');

    $this->getJson('/docs/api.json')->assertOk();
});

it('denies the interactive docs and spec outside local', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->getJson('/docs/api.json')->assertForbidden();
    $this->get('/docs/api')->assertForbidden();
});
