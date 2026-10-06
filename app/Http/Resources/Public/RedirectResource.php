<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RedirectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'from' => $this->old_path,
            'to' => $this->new_path,
            'status' => 301,
        ];
    }
}
