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
    public function upsertText(array $records, ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\UpsertText($records, $namespace));
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

    /**
     * Reranks search results using a specified model.
     *
     * @param string $model The reranking model to use.
     * @param string $query The query text.
     * @param array $documents An array of documents to rerank.
     * @param int|null $topN The number of results to return.
     * @param bool $returnDocuments Whether to return the documents in the response.
     * @param array|null $rankFields Fields to rerank on.
     * @param array|null $parameters Additional model-specific parameters.
     * @return Response
     */
    public function rerank(
        string $model,
        string $query,
        array $documents,
        ?int $topN = null,
        bool $returnDocuments = false,
        ?array $rankFields = null,
        ?array $parameters = null
    ): Response
    {
        // Temporarily set the correct base URL for inference
        $originalBaseUrl = $this->connector->resolveBaseUrl(); // Get current base (might be index host or api.pinecone.io)
        $this->connector->setBaseUrl('https://api.pinecone.io');

        $response = $this->connector->send(new Data\Rerank(
            model: $model,
            queryText: $query,
            documents: $documents,
            topN: $topN,
            returnDocuments: $returnDocuments,
            rankFields: $rankFields,
            parameters: $parameters
        ));

        // Restore the original base URL
        $this->connector->setBaseUrl($originalBaseUrl);

        return $response;
    }

    public function fetch(array $ids, ?string $namespace = null): Response
    {
        return $this->connector->send(new Data\FetchVectors(
            ids: $ids,
            namespace: $namespace
        ));
    }

    /**
     * Search records with optional integrated reranking.
     *
     * @param array $queryInput The query input, e.g., ['text' => 'Disease prevention']
     * @param int $queryTopK The number of initial results to fetch for the query.
     * @param array|null $queryFilter Optional metadata filter to apply to the query, e.g., ['category' => 'digestive system'].
     * @param string|null $rerankModel The reranking model to use, e.g., 'bge-reranker-v2-m3'. If null, no reranking is performed.
     * @param int|null $rerankTopN The number of results to return after reranking.
     * @param array|null $rerankRankFields Fields to rerank on. If null, defaults to the main query field. E.g. ['chunk_text']
     * @param array|null $fields Specific fields to return in the results, e.g., ['category', 'chunk_text'].
     * @param string|null $namespace Optional namespace to search in.
     * @return Response
     */
    public function search(
        array $queryInput,
        int $queryTopK,
        ?array $queryFilter = null,
        ?string $rerankModel = null,
        ?int $rerankTopN = null,
        ?array $rerankRankFields = null,
        ?array $fields = null,
        ?string $namespace = null
    ): Response
    {
        return $this->connector->send(new Data\Search(
            queryInput: $queryInput,
            queryTopK: $queryTopK,
            queryFilter: $queryFilter,
            rerankModel: $rerankModel,
            rerankTopN: $rerankTopN,
            rerankRankFields: $rerankRankFields,
            fields: $fields,
            namespace: $namespace
        ));
    }
}