<?php

namespace Probots\Pinecone\Requests\Data;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use function array_filter;

/**
 * @link https://docs.pinecone.io/guides/inference/rerank#integrated-reranking (conceptual, actual endpoint may vary)
 * This endpoint uses the unstable API version and may change in future Pinecone API updates.
 */
class Search extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * Constructor
     *
     * @param array $queryInput The query input, e.g., ['text' => 'Disease prevention']
     * @param int $queryTopK The number of initial results to fetch for the query.
     * @param array|null $queryFilter Optional metadata filter to apply to the query, e.g., ['category' => 'digestive system'].
     * @param string|null $rerankModel The reranking model to use, e.g., 'bge-reranker-v2-m3'. If null, no reranking is performed.
     * @param int|null $rerankTopN The number of results to return after reranking.
     * @param array|null $rerankRankFields Fields to rerank on. If null, defaults to the main query field. E.g. ['chunk_text']
     * @param array|null $fields Specific fields to return in the results, e.g., ['category', 'chunk_text'].
     * @param string|null $namespace Optional namespace to search in.
     */
    public function __construct(
        protected array $queryInput,
        protected int $queryTopK,
        protected ?array $queryFilter = null,
        protected ?string $rerankModel = null,
        protected ?int $rerankTopN = null,
        protected ?array $rerankRankFields = null,
        protected ?array $fields = null,
        protected ?string $namespace = null
    ) {}

    public function resolveEndpoint(): string
    {
        if ($this->namespace) {
            return "/records/namespaces/{$this->namespace}/search";
        }

        return '/records/search';
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Pinecone-Api-Version' => '2025-01',
        ];
    }

    protected function defaultBody(): array
    {
        $queryObject = [
            'inputs' => $this->queryInput,
            'top_k' => $this->queryTopK,
        ];

        if ($this->queryFilter !== null) {
            $queryObject['filter'] = $this->queryFilter;
        }

        $payload = [
            'query' => $queryObject
        ];

        if ($this->rerankModel) {
            $payload['rerank'] = array_filter([
                'model' => $this->rerankModel,
                'top_n' => $this->rerankTopN,
                'rank_fields' => $this->rerankRankFields,
            ], fn($value) => $value !== null);
        }

        if ($this->fields !== null) {
            $payload['fields'] = $this->fields;
        }
        
        return $payload;
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 200;
    }
} 