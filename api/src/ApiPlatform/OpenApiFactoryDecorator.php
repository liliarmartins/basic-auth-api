<?php

namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.openapi.factory')]
class OpenApiFactoryDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $tokenSchema = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your_refresh_token_here',
                ],
            ],
        ]);

        $pathItem = new PathItem(
            ref: 'Refresh JWT Token',
            post: new Operation(
                operationId: 'refreshToken',
                tags: ['Refresh Token'],
                responses: [
                    '200' => new Response(
                        description: 'JWT token refreshed',
                        content: new \ArrayObject([
                            'application/json' => new MediaType(schema: new \ArrayObject(['type' => 'string'])),
                        ])
                    ),
                ],
                summary: 'Refresh JWT token',
                requestBody: new RequestBody(
                    description: 'Refresh JWT token',
                    content: new \ArrayObject([
                        'application/json' => new MediaType(schema: $tokenSchema),
                    ]),
                    required: true
                )
            )
        );

        // Add the path to the OpenAPI documentation
        $openApi->getPaths()->addPath('/api/token/refresh', $pathItem);

        return $openApi;
    }
}
