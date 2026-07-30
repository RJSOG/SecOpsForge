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

        BuildPageJob::dispatchSync($transaction);

        return response()->json([
            'message' => 'Started to build file page',
            'transaction_id' => $transaction->id,
        ], 202); // 202 Accepted
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

        BuildFileTreeJob::dispatchSync($transaction);

        return response()->json([
            'message' => 'Started to build file tree',
            'transaction_id' => $transaction->id,
        ], 202); // 202 Accepted
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
