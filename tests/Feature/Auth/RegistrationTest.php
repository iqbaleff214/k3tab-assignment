<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen cannot be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});
