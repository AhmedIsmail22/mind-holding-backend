<?php

namespace App\Http\Resources\Admin;

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
            'id' => $this->id,
            'old_path' => $this->old_path,
            'new_path' => $this->new_path,
            'status' => 301,
        ];
    }
}
