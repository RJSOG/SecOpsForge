<?php

namespace App\Services\Builder;

abstract class DataBuilder
{
    /**
     * @var array
     */
    protected array $input = [];

    /**
     * @var array
     */
    public array $output = [];

    /**
     * @param array $input
     * @return $this
     */
    public function construct(array $input): static
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @return static
     */
    abstract public function build(): static;

    /**
     * @return $this
     */
    abstract public function terminate(): static;
}
