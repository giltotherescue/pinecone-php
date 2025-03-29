<?php

it('can query by text', function () {

    $client = getClient(true);
    $index = getIndexName('-pod-with-embedding');

    // This is not good. Since the test relies on Pinecone having the needed index.
    setIndexHost($client, $index);

    $response = $client->data()->vectors()->queryText(
        text: "What are the benefits of apples?",
        topK: 3,
        includeMetadata: true
    );

    expect($response->status())->toBe(200)
        ->and($response->json('matches'))->toBeArray();

}); 