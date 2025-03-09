<?php

namespace Backpack\Models;

class BlogCategory extends PostTerm
{
    public function form(): array
    {
        return [
            'slug' => ['required' => false],
            'description' => ['type' => 'textarea'],
            'term_type' => ['type' => 'hidden', 'value' => 'category'],
            'terms_id' => ['ignore' => true]
        ];
    }
}