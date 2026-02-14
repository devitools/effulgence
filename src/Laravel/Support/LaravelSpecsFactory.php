<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Support;

use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

readonly class LaravelSpecsFactory extends DefaultSpecsFactory
{
    public function __construct(Builder $builder, ConfigRepository $config)
    {
        $specs = $config->get('effulgence.schema.specs', []);
        /** @var array $specs */
        $specs = is_array($specs)
            ? $specs
            : [];
        parent::__construct($builder, $specs);
    }
}
