<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Database\Document;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Effulgence\Infrastructure\Database\Document\SleekDBFactory;
use SleekDB\Store;

use function Constructo\Cast\arrayify;
use function Constructo\Util\extractArray;
use function Constructo\Util\extractString;

readonly class LaravelSleekDBFactory implements SleekDBFactory
{
    public function __construct(private ConfigRepository $config)
    {
    }

    public function make(string $resource): Store
    {
        $options = arrayify($this->config->get('effulgence.databases.sleek'));
        $path = extractString($options, 'path');
        $configuration = extractArray($options, 'configuration');
        return new Store($resource, $path, $configuration);
    }
}
