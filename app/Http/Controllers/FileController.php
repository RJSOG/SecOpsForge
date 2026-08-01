<?php

namespace App\Http\Controllers;

use App\Enum\EventEnum;
use App\Enum\StatusEnum;
use App\Http\Requests\BuildFileTreeRequest;
use App\Http\Requests\BuildFilePageRequest;
use App\Http\Requests\ValidateFileTreeRequest;
use App\Jobs\BuildFileTreeJob;
use App\Jobs\BuildPageJob;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class FileController extends Controller
{
    /**
     * @param BuildFilePageRequest $request
     * @return JsonResponse
     */
    public function buildFilePage(BuildFilePageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $transaction = new Transaction([
            'event' => EventEnum::BUILD_PAGE,
            'status' => StatusEnum::PENDING,
            'details' => [
                'input' => [
                    'path' => $validated['path'],
                    'format' => $validated['format'],
                    'source' => $validated['source'],
                ],
            ],
        ]);

        $transaction->save();

        // dispatchSync() runs the job inline, in this same request, before we
        // ever get here: by the time we respond the work is already done.
        // 202 Accepted ("processing, not finished yet") was misleading; we
        // return 200 with the completed result instead.
        try {
            BuildPageJob::dispatchSync($transaction);
        } catch (InvalidArgumentException $e) {
            $transaction->update(['status' => StatusEnum::FAILED]);

            return response()->json([
                'message' => $e->getMessage(),
                'transaction_id' => $transaction->id,
            ], 422);
        }

        $transaction->refresh();

        return response()->json([
            'message' => 'File page built',
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'output' => $transaction->details['output'] ?? null,
        ], 200);
    }

    /**
     * @param BuildFileTreeRequest $request
     * @return JsonResponse
     */
    public function buildFileTree(BuildFileTreeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $transaction = new Transaction([
            'event' => EventEnum::BUILD_FILE_TREE,
            'status' => StatusEnum::PENDING,
            'details' => [
                'input' => [
                    'root' => $validated['root'] ?? '',
                    'format' => $validated['format'],
                ],
            ],
        ]);

        $transaction->save();

        try {
            BuildFileTreeJob::dispatchSync($transaction);
        } catch (InvalidArgumentException $e) {
            $transaction->update(['status' => StatusEnum::FAILED]);

            return response()->json([
                'message' => $e->getMessage(),
                'transaction_id' => $transaction->id,
            ], 422);
        }

        $transaction->refresh();

        return response()->json([
            'message' => 'File tree built',
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'output' => $transaction->details['output'] ?? null,
        ], 200);
    }

    public function validateFileTree(ValidateFileTreeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $transaction = new Transaction([
            'event' => EventEnum::VALIDATE_FILE_TREE,
            'status' => StatusEnum::IN_PROGRESS,
            'details' => [
                'input' => [
                    'root' => $validated['root'],
                    'hash' => $validated['hash'],
                ],
            ],
        ]);

        $transaction->save();

        $latestFileTreeTransaction = Transaction::where('event', EventEnum::BUILD_FILE_TREE)
            ->where('details->input->root', $validated['root'])
            ->latest()
            ->first();

        $latestOutput = $latestFileTreeTransaction?->details['output'] ?? [];

        $isValid = ($latestOutput['hash'] ?? null) === $validated['hash'];

        $details = $transaction->details;
        $details['output'] = [
            'is_valid' => $isValid,
        ];

        $transaction->update([
            'details' => $details,
            'status' => StatusEnum::COMPLETED,
        ]);

        return response()->json([
            'is_valid' => $isValid,
        ]);
    }
}
