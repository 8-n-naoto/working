<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * api/* の例外を {"ok": false, "message": ...} 形式の JSON に整形する。
 */
class ApiExceptionRenderer
{
    public function __invoke(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        if ($e instanceof HttpResponseException) {
            return null;
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'ok' => false,
                'message' => '入力内容に誤りがあります。',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json(['ok' => false, 'message' => '連携トークンが無効です。'], 401);
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();

            return response()->json(
                ['ok' => false, 'message' => $this->messageFor($status)],
                $status,
                $e->getHeaders(),
            );
        }

        return response()->json(['ok' => false, 'message' => 'サーバーエラーが発生しました。'], 500);
    }

    private function messageFor(int $status): string
    {
        return match ($status) {
            403 => '権限がありません。',
            404 => '見つかりません。',
            405 => '許可されていないメソッドです。',
            419 => 'ページの有効期限が切れました。',
            429 => 'リクエストが多すぎます。しばらく待ってから再送してください。',
            default => $status >= 500 ? 'サーバーエラーが発生しました。' : 'リクエストを処理できません。',
        };
    }
}
