<?php

namespace Probots\Pinecone\Requests\Control;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * @link https://docs.pinecone.io/reference/api/2025-01/control-plane/create_for_model
 */
class CreateForModel extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $name,
        protected array $embed,
        protected ?string $cloud = 'aws',
        protected ?string $region = 'us-east-1',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/indexes/create-for-model';
    }

    protected function defaultBody(): array
    {
        $payload = [
            'name' => $this->name,
            'embed' => $this->embed,
            'cloud' => $this->cloud,
            'region' => $this->region,
        ];

        return $payload;
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->status() !== 201;
    }
} 