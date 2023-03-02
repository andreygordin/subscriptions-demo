<?php

declare(strict_types=1);

namespace App\Service\OpenApi\Manual;

use OpenApi\Attributes as OA;

#[OA\Response(response: 'NotFound', description: 'Not found')]
#[OA\Response(response: 'Deleted', description: 'Deleted')]
#[OA\Response(
	response: 'Created',
	description: 'Created',
	headers: [
		new OA\Header(
			header: 'Location',
			description: 'Location of the created entity.',
			schema: new OA\Schema(type: 'string')
		),
	],
)]
#[OA\Response(
	response: 'ValidationErrors',
	description: 'Validation errors',
	content: new OA\JsonContent(
		type: 'object',
		properties: [new OA\Property(property: 'errors', type: 'object')],
	),
)]
final class Response
{
}
