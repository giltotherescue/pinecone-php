<?php

namespace Probots\Pinecone\Resources\Data;

use Probots\Pinecone\Client;
use Probots\Pinecone\Requests\Data;
use Probots\Pinecone\Resources\Resource;
use Saloon\Http\Response;

class VectorResource extends Resource
{
    public function __construct(protected Client $connector)
    {
        parent::__construct($connector);
    }

    public function stats(): Response
    {
        return $this->connector->send(new Data\GetIndexStats());
    }

    public function update(string  $id,
                           array   $values = [],
                           array   $sparseValues = [],
                           array   $setMetadata = [],
                           ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\UpdateVector(
            id: $id,
            values: $values,
            sparseValues: $sparseValues,
            setMetadata: $setMetadata,
            namespace: $namespace
        ));
    }


    public function upsert(array $vectors, ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\UpsertVectors($vectors, $namespace));
    }

    /**
     * Upsert text records into a namespace. Pinecone converts the text to vectors automatically using the hosted embedding model associated with the index.
     * 
     * @param array $records An array of records to upsert. Each record should have an '_id' and 'text' keys, and optionally other keys for metadata.
     * @param string|null $namespace Optional namespace to upsert the records into.
     * @return \Saloon\Http\Response
     */
    public function upsertRecords(array $records, ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\UpsertRecords($records, $namespace));
    }

    /**
     * Query an index with integrated embedding using text. Pinecone converts the text to a vector automatically.
     * 
     * @param string $text The text to search for
     * @param string|null $namespace Optional namespace to search in
     * @param array $filter Optional metadata filter
     * @param int $topK Number of results to return
     * @param bool $includeMetadata Whether to include metadata in the response
     * @param bool $includeValues Whether to include vector values in the response
     * @return \Saloon\Http\Response
     */
    public function queryText(
        string   $text,
        ?string  $namespace = null,
        array    $filter = [],
        int      $topK = 10,
        bool     $includeMetadata = true,
        bool     $includeValues = false
    ): Response
    {
        return $this->connector->send(new Data\QueryText(
            text: $text,
            namespace: $namespace,
            filter: $filter,
            topK: $topK,
            includeMetadata: $includeMetadata,
            includeValues: $includeValues
        ));
    }

    public function query(
        array   $vector = [],
        ?string $namespace = null,
        array   $filter = [],
        int     $topK = 3,
        bool    $includeMetadata = true,
        bool    $includeValues = false,
        ?string $id = null
    ): Response
    {
        return $this->connector->send(new Data\QueryVectors(
            vector: $vector,
            namespace: $namespace,
            filter: $filter,
            topK: $topK,
            includeMetadata: $includeMetadata,
            includeValues: $includeValues,
            id: $id
        ));
    }

    public function delete(
        array   $ids = [],
        ?string $namespace = null,
        bool    $deleteAll = false,
        array   $filter = []
    ): Response
    {
        return $this->connector->send(new Data\DeleteVectors(
            ids: $ids,
            namespace: $namespace,
            deleteAll: $deleteAll,
            filter: $filter
        ));
    }

    public function fetch(array $ids, ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\FetchVectors(
            ids: $ids,
            namespace: $namespace
        ));
    }
}