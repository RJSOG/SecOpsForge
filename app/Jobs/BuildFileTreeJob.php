<?php

namespace App\Jobs;

use App\Enum\StatusEnum;
use App\Events\CallbackEvent;
use App\Models\Transaction;
use App\Services\Builder\FileTreeBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Log\Logger;
use Inertia\Inertia;
use Throwable;

class BuildFileTreeJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param Transaction $transaction
     */
    public function __construct(
        protected Transaction $transaction
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->transaction->update(['status' => StatusEnum::IN_PROGRESS]);

        $builder = new FileTreeBuilder();

        $builder->construct($this->transaction->details['input'])
            ->load()
            ->build()
            ->terminate();

        $details = $this->transaction->details;
        $details['output'] = $builder->output;

        $this->transaction->update([
            'details' => $details,
            'status' => StatusEnum::COMPLETED,
        ]);

        $this->transaction->save();

        $this->terminate();
    }

    /**
     * @param Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function failed(Throwable $exception): void
    {
        $this->transaction->update(['status' => StatusEnum::FAILED]);
        $this->transaction->save();

        $this->terminate($exception);

        throw $exception;
    }

    /**
     * @param Throwable|null $exception
     * @return void
     */
    public function terminate(Throwable $exception = null): void
    {
        $output = $this->transaction->status === StatusEnum::COMPLETED
            ? $this->transaction->details['output']
            : $exception->getMessage();

        $callbackEvent = new CallbackEvent(
            output: $output,
            status: $this->transaction->status->value,
            broadcastOn: 'build.file.tree',
        );

        event($callbackEvent);
    }
}
