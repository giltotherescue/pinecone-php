# Pinecone PHP

A beautiful, extendable PHP Package to communicate with your [pinecone.io](https://pinecone.io) indices, collections and
vectors, powered by [Saloon](https://github.com/saloonphp/saloon).

> **Info**
> From Version 1.x onwards we are using the latest Pinecone API which support serverless. If you need the legacy API
> please use a version before
> 1.0.0!

[![probots.io](art/probots-banner-1000x400.png)](https://probots.io)

## Introduction

This API provides a feature rich, elegant baseline for working with the [pinecone.io](https://pinecone.io) API.
Developers
can
install and leverage this API to help them integrate [pinecone.io](https://pinecone.io) easily and beautifully.

## Installation

`composer require probots-io/pinecone-php`

## Features

Currently supports all of the available endpoints in the [pinecone.io](https://pinecone.io) API based
on [the official documentation](https://docs.pinecone.io/reference)

### Authentication

Authentication via Api Key is the only available authentication methods currently supported.
First, you will need to create an Api Key in your [pinecone.io](https://pinecone.io) instance.

```php
use \Probots\Pinecone\Client as Pinecone;

$apiKey = 'YOUR_PINECONE_API_KEY';

// Initialize Pinecone
$pinecone = new Pinecone($apiKey);

// Now you are ready to make requests, all requests will be authenticated automatically.
```

## Quick Start

There are two ways to initialize the SDK. You can either provide an index during initialization or you can provide it
later on.

```php
use \Probots\Pinecone\Client as Pinecone;

$apiKey = 'YOUR_PINECONE_API_KEY';
$pinecone = new Pinecone($apiKey);

// all control methods are available now, create an index or similar
// e.g. $pinecone->control()->index()

// later on you can provide the index
$pinecone->setIndexHost('INDEX_HOST_FROM_PINECONE');

// data methods are available now

// e.g. $pinecone->data()->vectors()
```

or

```php
use \Probots\Pinecone\Client as Pinecone;

$apiKey = 'YOUR_PINECONE_API_KEY';
$indexHost = 'INDEX_HOST_FROM_PINECONE';

$pinecone = new Pinecone($apiKey, $indexHost);

// all control AND data methods are available now
```
> **Info**
> The index host should include `https://`, which you may need to prepend to the value returned from Pinecone.

> **Info**
> The index host should include `https://`, which you may need to prepend to the value returned from Pinecone.

## Responses

All responses are returned as a `Response` object.
Please check the [Saloon documentation](https://docs.saloon.dev/the-basics/responses#available-methods) to see all
available methods.

# Control Pane

## Index Operations

Work(s) with your indices.

### Create Index (POD)

[Pinecone Docs](https://docs.pinecone.io/reference/create_index)

```php
$response = $pinecone->control()->index('my-index')->createPod(
    dimension: 1536,
    metric: 'cosine',
    podType: 'p1.x1',
    replicas: 1
    // ... more options    
);

if($response->successful()) {
    // 
}
```

### Create Index (Serverless)

[Pinecone Docs](https://docs.pinecone.io/reference/create_index)

```php
$response = $pinecone->control()->index('my-index')->createServerless(
    dimension: 1536,
    metric: 'cosine',
    cloud: 'aws',
    region: 'us-west-2'
    // ... more options    
);

if($response->successful()) {
    // 
}
```

### Create Index with Integrated Embedding

[Pinecone Docs](https://docs.pinecone.io/reference/api/2025-01/control-plane/create_for_model)

```php
$response = $pinecone->control()->index('my-index')->createForModel(
    cloud: 'aws',
    region: 'us-east-1',
    embed: [
        'model' => 'multilingual-e5-large'
    ]
);

if($response->successful()) {
    // 
}
```

### Describe Index

[Pinecone Docs](https://docs.pinecone.io/reference/describe_index)

```php
$response = $pinecone->control()->index('my-index')->describe();

if($response->successful()) {
    // 
}
```

### List Indices

[Pinecone Docs](https://docs.pinecone.io/reference/list_indexes)

```php
$response = $pinecone->control()->index()->list();

if($response->successful()) {
    // 
}
```

### Configure Index

[Pinecone Docs](https://docs.pinecone.io/reference/configure_index)

```php
$response = $pinecone->control()->index('my-index')->configure(
    pod_type: 'p1.x1',
    replicas: 1
);

if($response->successful()) {
    // 
}
```

### Delete Index

[Pinecone Docs](https://docs.pinecone.io/reference/delete_index)

```php
$response = $pinecone->control()->index('my-index')->delete();

if($response->successful()) {
    // 
}
```

## Collection Operations

Work(s) with your collections too.

### Create Collection

[Pinecone Docs](https://docs.pinecone.io/reference/create_collection)

```php
$response = $pinecone->control()->collection('my-collection')->create(
    source: 'my-index'
);

if($response->successful()) {
    // 
}
```

### Describe Collection

[Pinecone Docs](https://docs.pinecone.io/reference/describe_collection)

```php
$response = $pinecone->control()->collection('my-collection')->describe();

if($response->successful()) {
    // 
}
```

### List Collections

[Pinecone Docs](https://docs.pinecone.io/reference/list_collections)

```php
$response = $pinecone->control()->collection()->list();

if($response->successful()) {
    // 
}
```

### Delete Collections

[Pinecone Docs](https://docs.pinecone.io/reference/delete_collection)

```php
$response = $pinecone->control()->collection('my-collection')->delete();

if($response->successful()) {
    // 
}
```

# Data Pane

> **Info**
> These operations need the index to be set. You can set the index during initialization or later on.
> See description at the beginning.

## Vector Operations

Vectors are the basic unit of data in Pinecone. Use them.

### Get Index Stats

[Pinecone Docs](https://docs.pinecone.io/reference/describe_index_stats)

```php
$response = $pinecone->data()->vectors()->stats();

if($response->successful()) {
    // 
}
```

### Update Vector

[Pinecone Docs](https://docs.pinecone.io/reference/update)

```php
$response = $pinecone->data()->vectors()->update(
    id: 'vector_1',
    values: array_fill(0, 128, 0.14),
    setMetadata: [
        'meta1' => 'value1',
    ]
);

if($response->successful()) {
    // 
}
```

### Upsert Vectors

[Pinecone Docs](https://docs.pinecone.io/reference/upsert)

```php
$response = $pinecone->data()->vectors()->upsert(vectors: [
    'id' => 'vector_1',
    'values' => array_fill(0, 128, 0.14),
    'metadata' => [
        'meta1' => 'value1',
    ]
]);

if($response->successful()) {
    // 
}
```

### Upsert Text

[Pinecone Docs](https://docs.pinecone.io/reference/api/2025-01/data-plane/upsert_records)

```php
$response = $pinecone->data()->vectors()->upsertText(records: [
    [
        '_id' => 'record_1',
        'text' => 'This is text that will be converted to a vector by Pinecone',
        'category' => 'example-metadata',
    ]
], namespace: 'my-namespace');

if($response->successful()) {
    // 
}
```

### Query Vectors

[Pinecone Docs](https://docs.pinecone.io/reference/query)

```php
$response = $pinecone->data()->vectors()->query(
    vector: array_fill(0, 128, 0.12),
    namespace: 'my-namespace',
    filter: ['genre' => 'documentary'],
    topK: 5,
    includeMetadata: true,
    includeValues: false
);

if($response->successful()) {
    $matches = $response->json('matches');
    // Process search results
}
```

### Query By Text

[Pinecone Docs](https://docs.pinecone.io/reference/api/2025-01/data-plane/query_text)

```php
$response = $pinecone->data()->vectors()->queryText(
    text: "What are the benefits of apples?",
    namespace: 'health-data',
    filter: ['category' => 'nutrition'],
    topK: 3,
    includeMetadata: true,
    includeValues: false
);

if($response->successful()) {
    $matches = $response->json('matches');
    // Process text search results
}
```

### Rerank Results

[Pinecone Docs](https://docs.pinecone.io/guides/inference/rerank)

Reranking is a two-stage process that improves search result quality by reordering initial results based on their semantic relevance to the query. This implementation uses Pinecone's hosted reranking models.

```php
// First, get results from any source (Pinecone query, database, etc.)
$documents = [
    ['id' => '1', 'text' => 'Apples are high in fiber and vitamin C.'],
    ['id' => '2', 'text' => 'Bananas provide potassium and can help with muscle function.'],
    ['id' => '3', 'text' => 'Oranges contain high levels of vitamin C and antioxidants.']
];

// Then rerank those results
$response = $pinecone->data()->vectors()->rerank(
    model: 'bge-reranker-v2-m3',  // Choose a supported model like bge-reranker-v2-m3 or cohere-rerank-3.5
    query: 'What are the health benefits of fruits?',
    documents: $documents,
    topN: 2,  // Return only top 2 results
    returnDocuments: true,  // Include original documents in response
    rankFields: ['text'],  // Field to use for reranking (optional, defaults to 'text')
    parameters: ['truncate' => 'END']  // Model-specific parameters
);

if($response->successful()) {
    $results = $response->json('results');
    $usage = $response->json('usage.rerank_units');
    
    // Process reranked results, which are sorted by relevance score
    foreach ($results as $result) {
        // $result['id'], $result['score'], $result['text']
    }
}
```

### Search Records with Integrated Reranking

[Pinecone Docs](https://docs.pinecone.io/guides/inference/rerank#integrated-reranking)

This feature allows you to perform search and reranking in a single operation, simplifying your code. It uses the records search endpoint with the optional rerank parameter.

```php
$response = $pinecone->data()->vectors()->search(
    // Query parameters
    queryInput: ['text' => 'Disease prevention strategies'],
    queryTopK: 10, // Get initial 10 results
    queryFilter: ['category' => 'digestive system'], // Optional: Filter results before reranking
    
    // Reranking parameters (all optional - omit for standard search without reranking)
    rerankModel: 'bge-reranker-v2-m3', // Reranker model to use
    rerankTopN: 3, // Return only top 3 results after reranking
    rerankRankFields: ['chunk_text'], // Field to use for reranking
    
    // Other parameters
    fields: ['title', 'category', 'chunk_text'], // Fields to include in results
    namespace: 'medical-journals' // Optional namespace
);

if ($response->successful()) {
    $records = $response->json('records');
    
    // Process the reranked search results
    foreach ($records as $record) {
        // Each record contains:
        // - _id: Document identifier
        // - _score: Relevance score (0-1, higher is better)
        // - fields: Requested document fields
        echo "ID: {$record['_id']}, Score: {$record['_score']}\n";
        echo "Title: {$record['fields']['title']}\n";
    }
}
```

### Delete Vectors

[Pinecone Docs](https://docs.pinecone.io/reference/delete_post)

```php
$response = $pinecone->data()->vectors()->delete(
    deleteAll: true
);

if($response->successful()) {
    // 
}
```

### Fetch Vectors

[Pinecone Docs](https://docs.pinecone.io/reference/fetch)

```php
$response = $pinecone->data()->vectors()->fetch([
    'vector_1', 'vector_2'
]);

if($response->successful()) {
    // 
}
```

## Testing

Testing is done via PestPHP. You can run the tests by running `./vendor/bin/pest` in the root of the project.
Copy .env.example to .env and update accordingly.

## Credits

- [Marcus Pohorely](https://github.com/derpoho)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## TODO - Submit PR if you want to contribute:

- [ ] validate parameters based on API docs - needs more checking
- [ ] Implement Custom Exceptions
- [ ] Add failure tests
- [ ] Add some examples
- [ ] Finish docs
