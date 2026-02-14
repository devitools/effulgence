<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Support;

use Constructo\Factory\DefaultTypesFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

use function Constructo\Cast\arrayify;

readonly class LaravelTypesFactory extends DefaultTypesFactory
{
    public function __construct(ConfigRepository $config)
    {
        $types = $config->get('effulgence.schema.types', []);
        parent::__construct(arrayify($types));
    }
}
