<?php

it('can rerank documents', function () {
    $client = getClient(true);
    $index = getIndexName('-pod-with-embedding');

    // This is not good. Since the test relies on Pinecone having the needed index.
    setIndexHost($client, $index);

    $model = 'rerank-english-v2.0';
    $query = 'What are the health benefits of fruits?';
    $documents = [
        ['id' => '1', 'text' => 'Apples are high in fiber and vitamin C.'],
        ['id' => '2', 'text' => 'Bananas provide potassium and can help with muscle function.'],
        ['id' => '3', 'text' => 'The keyboard is on the desk next to the mouse.']
    ];
    
    $response = $client->data()->vectors()->rerank(
        model: $model,
        query: $query,
        documents: $documents,
        topN: 2,
        returnDocuments: true
    );

    expect($response->status())->toBe(200)
        ->and($response->json('results'))->toBeArray()
        ->and(count($response->json('results')))->toBe(2)
        ->and($response->json('usage'))->toBeArray()
        ->and($response->json('usage.rerank_units'))->toBeNumeric();
}); 