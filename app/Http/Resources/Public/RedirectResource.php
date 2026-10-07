<?php

namespace App\Http\Resources\Public;

use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Redirect
 */
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
