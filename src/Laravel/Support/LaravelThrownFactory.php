<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Support;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Effulgence\Domain\Exception\Parser\DefaultThrownFactory;

use function Constructo\Cast\arrayify;

class LaravelThrownFactory extends DefaultThrownFactory
{
    public function __construct(ConfigRepository $config)
    {
        $classification = $config->get('effulgence.exceptions.classification', []);
        parent::__construct(arrayify($classification));
    }
}
