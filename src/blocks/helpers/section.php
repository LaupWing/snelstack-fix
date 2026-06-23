<?php
defined('ABSPATH') || exit;

if (! function_exists('snel_section_padding')) {
    function snel_section_padding(array $attributes): string
    {
        $size   = $attributes['size']          ?? 'md';
        $no_top = ! empty($attributes['disableTop']);
        $no_bot = ! empty($attributes['disableBottom']);

        $top    = ['sm' => 'pt-12 lg:pt-16', 'md' => 'pt-20 lg:pt-28', 'lg' => 'pt-24 lg:pt-32'];
        $bottom = ['sm' => 'pb-12 lg:pb-16', 'md' => 'pb-20 lg:pb-28', 'lg' => 'pb-24 lg:pb-32'];

        $parts = [];
        if (! $no_top) $parts[] = $top[$size]    ?? $top['md'];
        if (! $no_bot) $parts[] = $bottom[$size] ?? $bottom['md'];

        return implode(' ', $parts);
    }
}

if (! function_exists('snel_section_class')) {
    function snel_section_class(array $attributes, string $key = 'bg'): string
    {
        $val = $attributes[$key] ?? 'white';
        if ($val === 'dark')   return 'is-dark';
        if ($val === 'canvas') return 'is-dark bg-canvas';
        return 'bg-white';
    }
}

if (! function_exists('snel_section_style')) {
    function snel_section_style(array $attributes, string $key = 'bg'): string
    {
        $val = $attributes[$key] ?? 'white';
        if ($val === 'dark')   return ' style="background-color:#2e1065"';
        if ($val === 'canvas') return ' style="background-color:#020617"';
        return '';
    }
}
