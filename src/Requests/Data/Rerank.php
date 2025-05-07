<?php

namespace Probots\Pinecone\Requests\Data;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use function array_filter;

/**
 * @link https://docs.pinecone.io/guides/search/rerank-results
 */
class Rerank extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * Constructor
     *
     * @param string $model The reranking model to use.
     * @param string $queryText The query text.
     * @param array $documents An array of documents to rerank. Each document should be an associative array, potentially with 'id' and 'text' keys, or custom fields.
     * @param int|null $topN The number of results to return. If not specified, all results are returned.
     * @param bool $returnDocuments Whether to return the documents in the response. Defaults to false.
     * @param array|null $rankFields Fields to rerank on. If null, defaults to ['text']. For models like cohere-rerank-3.5, multiple fields can be specified.
     * @param array|null $parameters Additional model-specific parameters, e.g. ['truncate' => 'END'] to truncate at token limit or ['truncate' => 'NONE'] to return an error.
     */
    public function __construct(
        protected string $model,
        protected string $queryText,
        protected array $documents,
        protected ?int $topN = null,
        protected bool $returnDocuments = false,
        protected ?array $rankFields = null,
        protected ?array $parameters = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/rerank';
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.pinecone.io';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-Pinecone-API-Version' => '2025-01',
        ];
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'model' => $this->model,
            'query' => $this->queryText,
            'documents' => $this->documents,
            'top_n' => $this->topN,
            'return_documents' => $this->returnDocuments,
            'rank_fields' => $this->rankFields, // Defaults to ['text'] when null on the server side
            'parameters' => $this->parameters,
        ], fn($value) => $value !== null);
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        // Assuming 200 OK is the success status code for rerank endpoint.
        return $response->status() !== 200;
    }
} 