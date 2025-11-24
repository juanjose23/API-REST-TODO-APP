<?php

test('the Application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
