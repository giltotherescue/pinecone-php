<?php

it('can create an index with integrated embedding', function () {
    $client = getClient();
    $index = getIndexName('-with-embedding');

    $response = $client->control()->index($index)->createForModel(
        embed: [
            'model' => 'multilingual-e5-large'
        ],
        cloud: 'aws',
        region: 'us-east-1'
    );

    expect($response->status())->toBe(201);
}); 