<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

class UserAuthResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var \Laravel\Sanctum\NewAccessToken
     */
    public $token;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct(User $resource, NewAccessToken $token)
    {
        parent::__construct($resource);

        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource),
            'token' => new TokenResource($this->token),
        ];
    }
}
