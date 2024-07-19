<?php

namespace NeutromeLabs\SsrGraphql\Api;

interface ResolverInterface
{

    public function resolve(string $query, array $variables = []): array;
}
