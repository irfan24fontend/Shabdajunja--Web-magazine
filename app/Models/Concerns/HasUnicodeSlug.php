<?php

namespace App\Models\Concerns;

trait HasUnicodeSlug
{
    /** Slug er source column (model e override kora jay) */
    protected function slugSource(): string
    {
        return 'name';
    }

    protected static function bootHasUnicodeSlug(): void
    {
        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->makeUniqueSlug($model->{$model->slugSource()});
            }
        });
    }

    public function makeUniqueSlug(string $text): string
    {
        $base = trim(preg_replace('/[^\p{L}\p{M}\p{N}]+/u', '-', $text), '-');
        $base = mb_substr($base, 0, 200) ?: 'lekha';

        $slug = $base;
        $i = 2;

        while (static::withoutGlobalScopes()
            ->when(method_exists($this, 'bootSoftDeletes'), fn ($q) => $q->withTrashed())
            ->where('slug', $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? 0)
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}