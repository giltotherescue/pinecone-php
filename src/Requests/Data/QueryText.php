<?php

namespace Probots\Pinecone\Requests\Data;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * @link https://docs.pinecone.io/reference/api/2025-01/data-plane/query_text
 */
class QueryText extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string  $text,
        protected ?string $namespace = null,
        protected array   $filter = [],
        protected int     $topK = 10,
        protected bool    $includeMetadata = true,
        protected bool    $includeValues = false,
    ) {}

    public function resolveEndpoint(): string
    {
        if ($this->namespace) {
            return "/records/namespaces/{$this->namespace}/query";
        }
        
        return '/records/query';
    }

    protected function defaultBody(): array
    {
        $payload = [
            'query' => $this->text,
            'topK' => $this->topK,
            'includeMetadata' => $this->includeMetadata,
            'includeValues' => $this->includeValues,
        ];

        if (count($this->filter) > 0) {
            $payload['filter'] = $this->filter;
        }

        return $payload;
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 200;
    }
} 