<?php

it('can search records with integrated reranking', function () {
    $client = getClient(true, '.search.rerank'); // Fixture name updated
    $indexName = getIndexName('-pod-with-embedding');
    $namespace = 'test-namespace';

    setIndexHost($client, $indexName);

    $queryInput = ['text' => 'Disease prevention'];
    $queryTopK = 4;
    $rerankModel = 'bge-reranker-v2-m3';
    $rerankTopN = 2;
    $rerankRankFields = ['chunk_text'];
    $fields = ['category', 'chunk_text'];

    $response = $client->data()->vectors()->search(
        queryInput: $queryInput,
        queryTopK: $queryTopK,
        rerankModel: $rerankModel,
        rerankTopN: $rerankTopN,
        rerankRankFields: $rerankRankFields,
        fields: $fields,
        namespace: $namespace
    );

    expect($response->status())->toBe(200);
    $responseData = $response->json();

    expect($responseData)->toBeArray()
        ->and(isset($responseData['records']))->toBeTrue()
        ->and($responseData['records'])->toBeArray()
        ->and(count($responseData['records']))->toBeLessThanOrEqual($rerankTopN);

    if (count($responseData['records']) > 0) {
        $firstRecord = $responseData['records'][0];
        expect($firstRecord)->toHaveKeys(['_id', '_score', 'fields']);
        expect($firstRecord['_score'])->toBeNumeric();
        expect($firstRecord['fields'])->toBeArray();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                expect(isset($firstRecord['fields'][$field]))->toBeTrue("Field '{$field}' should exist in record fields");
            }
        }
    }
});

it('can search records without reranking', function () {
    $client = getClient(true, '.search.norerank'); // Fixture name updated
    $indexName = getIndexName('-pod-with-embedding');
    $namespace = 'test-namespace';

    setIndexHost($client, $indexName);

    $queryInput = ['text' => 'Healthy snacks'];
    $queryTopK = 3;
    $fields = ['category'];

    $response = $client->data()->vectors()->search(
        queryInput: $queryInput,
        queryTopK: $queryTopK,
        fields: $fields,
        namespace: $namespace
    );

    expect($response->status())->toBe(200);
    $responseData = $response->json();
    
    expect($responseData)->toBeArray()
        ->and(isset($responseData['records']))->toBeTrue()
        ->and($responseData['records'])->toBeArray()
        ->and(count($responseData['records']))->toBeLessThanOrEqual($queryTopK);

    if (count($responseData['records']) > 0) {
        $firstRecord = $responseData['records'][0];
        expect($firstRecord)->toHaveKeys(['_id', '_score', 'fields']);
        expect($firstRecord['fields'])->toHaveKey('category');
    }
});

it('can search records with integrated reranking and filter', function () {
    $client = getClient(true, '.search.filter.rerank'); // Fixture name updated
    $indexName = getIndexName('-pod-with-embedding');
    $namespace = 'test-namespace';

    setIndexHost($client, $indexName);

    $queryInput = ['text' => 'Disease prevention'];
    $queryTopK = 4;
    $queryFilter = ['category' => 'digestive system'];
    $rerankModel = 'bge-reranker-v2-m3';
    $rerankTopN = 2;
    $rerankRankFields = ['chunk_text'];
    $fields = ['category', 'chunk_text'];

    $response = $client->data()->vectors()->search(
        queryInput: $queryInput,
        queryTopK: $queryTopK,
        queryFilter: $queryFilter,
        rerankModel: $rerankModel,
        rerankTopN: $rerankTopN,
        rerankRankFields: $rerankRankFields,
        fields: $fields,
        namespace: $namespace
    );

    expect($response->status())->toBe(200);
    $responseData = $response->json();

    expect($responseData)->toBeArray()
        ->and(isset($responseData['records']))->toBeTrue()
        ->and($responseData['records'])->toBeArray();

    if (isset($responseData['records']) && count($responseData['records']) > 0) {
         expect(count($responseData['records']))->toBeLessThanOrEqual($rerankTopN);
        $firstRecord = $responseData['records'][0];
        expect($firstRecord)->toHaveKeys(['_id', '_score', 'fields']);
        expect($firstRecord['_score'])->toBeNumeric();
        expect($firstRecord['fields'])->toBeArray();
        expect($firstRecord['fields']['category'])->toBe('digestive system');
        if (!empty($fields)) {
            foreach ($fields as $field) {
                expect(isset($firstRecord['fields'][$field]))->toBeTrue("Field '{$field}' should exist in record fields");
            }
        }
    }
});

it('can search records without reranking but with filter', function () {
    $client = getClient(true, '.search.filter.norerank'); // Fixture name updated
    $indexName = getIndexName('-pod-with-embedding');
    $namespace = 'test-namespace';

    setIndexHost($client, $indexName);

    $queryInput = ['text' => 'Healthy snacks'];
    $queryTopK = 3;
    $queryFilter = ['food_group' => 'fruits'];
    $fields = ['category', 'food_group'];

    $response = $client->data()->vectors()->search(
        queryInput: $queryInput,
        queryTopK: $queryTopK,
        queryFilter: $queryFilter,
        fields: $fields,
        namespace: $namespace
    );

    expect($response->status())->toBe(200);
    $responseData = $response->json();
    
    expect($responseData)->toBeArray()
        ->and(isset($responseData['records']))->toBeTrue()
        ->and($responseData['records'])->toBeArray();

    if (isset($responseData['records']) && count($responseData['records']) > 0) {
        expect(count($responseData['records']))->toBeLessThanOrEqual($queryTopK);
        $firstRecord = $responseData['records'][0];
        expect($firstRecord)->toHaveKeys(['_id', '_score', 'fields']);
        expect($firstRecord['fields']['food_group'])->toBe('fruits');
    }
}); 