<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    protected $headers = [
        'X-Forwarded-For' => '*',
        'X-Forwarded-Proto' => '*',
        'Host' => '*',
        'Referer' => '*',
        'Origin' => '*',
    ];


    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
