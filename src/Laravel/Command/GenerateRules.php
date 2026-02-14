<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Command;

use Illuminate\Console\Command;
use ReflectionException;
use Effulgence\Infrastructure\File\RulesGenerator;

use function Constructo\Cast\stringify;
use function sprintf;

class GenerateRules extends Command
{
    protected $signature = 'effulgence:rules {source}';

    protected $description = 'Export the rules to validate an entity';

    public function __construct(private readonly RulesGenerator $generator)
    {
        parent::__construct();
    }

    /**
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $this->components->info('Exporting rules');
        $source = stringify($this->argument('source'));
        $this->line(sprintf("Generating rules for '%s'. Please wait...", $source));
        $this->newLine();

        $output = $this->generator->generate($source);
        if (! $output) {
            $this->error(sprintf("It was not possible to generate rules for the source '%s'", $source));
            return;
        }
        $this->info('Rules generated successfully');
        $this->line($output);
        $this->newLine();
        $this->line('Copy and paste the rules above into your input file');
        $this->line('--');
    }
}
