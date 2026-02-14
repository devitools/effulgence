<?php

declare(strict_types=1);

namespace Effulgence\Presentation;

use Constructo\Contract\Message;
use Constructo\Support\Set;
use Effulgence\Laravel\Request\LaravelFormRequest;
use Effulgence\Presentation\Input\Mapped;
use Effulgence\Presentation\Input\Params;

class Input extends LaravelFormRequest implements Message
{
    /**
     * @var array<string, array|string>
     */
    protected array $rules = [];

    /**
     * @var array<string, callable(mixed $value):mixed|string>
     */
    protected array $mappings = [];

    protected bool $authorize = true;

    public function content(): Set
    {
        return $this->values();
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    /**
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @return array<string, callable(array $data):mixed|string>
     */
    public function mappings(): array
    {
        return $this->mappings;
    }

    protected function validationData(): array
    {
        return (new Mapped($this))
            ->then(new Params($this))
            ->resolve(parent::validationData());
    }
}
