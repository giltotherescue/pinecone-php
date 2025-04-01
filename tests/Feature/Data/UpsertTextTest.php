<?php

it('can upsert records', function () {

    $client = getClient(true);
    $index = getIndexName('-pod-with-embedding');

    // This is not good. Since the test relies on Pinecone having the needed index.
    setIndexHost($client, $index);

    $response = $client->data()->vectors()->upsertText(
        records: [
            [
                '_id'       => 'record_1',
                'text' => 'This is a test text for record 1',
                'category' => 'test',
            ],
            [
                '_id'       => 'record_2',
                'text' => 'This is another test text for record 2',
                'category' => 'test',
            ]
        ],
        namespace: 'test-namespace'
    );

    expect($response->status())->toBe(201)
        ->and($response->json('upsertedCount'))->toBe(2);

}); 