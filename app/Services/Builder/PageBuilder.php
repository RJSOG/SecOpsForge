<?php

namespace App\Services\Builder;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

class PageBuilder extends DataBuilder
{
    const AVAILABLE_FORMATS = ['md'];

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $format;

    /**
     * @var string
     */
    protected string $content;

    /**
     * @return PageBuilder
     */
    public function load(): static
    {
        $this->format = Str::afterLast($this->input['path'], '.');

        $path = $this->format . '/' . $this->input['path'];

        if (!Storage::disk('private')->exists($path)) {
            throw new InvalidArgumentException('File not found !');
        }

        $this->path = Storage::disk('private')->path($path);

        return $this;
    }

    /**
     * @return PageBuilder
     * @throws CommonMarkException
     */
    public function build(): static
    {
        if (!in_array($this->format, self::AVAILABLE_FORMATS, true)) {
            throw new InvalidArgumentException('Can\'t build page ! Invalid format !');
        }

        $converter = match ($this->format) {
            'md' => new CommonMarkConverter(),
        };

        $content = file_get_contents($this->path);

        $this->content = $converter->convert($content)->getContent();

        return $this;
    }

    /**
     * @return static
     */
    public function terminate(): static
    {
        $title = pathinfo($this->path, PATHINFO_FILENAME);

        $this->output = [
            'title' => $title,
            'content' => $this->content,
        ];

        return $this;
    }
}
