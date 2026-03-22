<?php
namespace App\DTOs;

class NotificationDTO
{
    public function __construct(
        public readonly int $tenant_id,
        public readonly int $user_id,
        public readonly string $type,
        public readonly array $payload,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            tenant_id:(int) $request->validated('tenant_id'), 
            user_id: (int) $request->validated('user_id'),
            type: $request->validated('type'),
            payload: $request->validated('payload')
        );
    }
}