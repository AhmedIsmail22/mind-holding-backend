<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /** Projects are addressed by slug, so every project needs one. */
    public function up(): void
    {
        foreach (DB::table('projects')->whereNull('slug')->get(['id', 'client_name']) as $project) {
            $name = json_decode($project->client_name, true)['en'] ?? '';
            $slug = Str::slug($name) ?: 'project';

            if (DB::table('projects')->where('slug', $slug)->exists()) {
                $slug .= '-'.$project->id;
            }

            DB::table('projects')->where('id', $project->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        // Data-only change.
    }
};
