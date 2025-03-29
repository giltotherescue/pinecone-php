<?php

namespace Probots\Pinecone\Requests\Data;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * @link https://docs.pinecone.io/reference/api/2025-01/data-plane/upsert_records
 */
class UpsertRecords extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected array   $records = [],
        protected ?string $namespace = null,
    ) {}

    public function resolveEndpoint(): string
    {
        if ($this->namespace) {
            return "/records/namespaces/{$this->namespace}/upsert";
        }
        
        return '/records/upsert';
    }

    protected function defaultBody(): array
    {
        return $this->records;
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 201;
    }
} 