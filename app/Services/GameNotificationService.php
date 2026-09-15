<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameNotificationService
{
    /**
     * Send Game Invitation Push Notification via FCM.
     *
     * @param string $fcmToken     Target device FCM token
     * @param string $gameSlug     Game slug identifier (e.g. "tic-tac-toe")
     * @param string $roomId       Game room ID (e.g. "room_12345")
     * @param array|object $sender Sender details (id, name, photo_url / avatar_url)
     * @return array Response status and payload info
     */
    public function sendGameInviteNotification(
        string $fcmToken,
        string $gameSlug,
        string $roomId,
        array|object $sender
    ): array {
        $fcmPayload = self::buildLegacyFcmRequest(
            fcmToken: $fcmToken,
            gameSlug: $gameSlug,
            roomId: $roomId,
            sender: $sender
        );

        $serverKey = config('services.fcm.server_key', env('FCM_SERVER_KEY'));

        if (! $serverKey) {
            Log::warning('FCM Server Key is not configured in services.fcm.server_key or FCM_SERVER_KEY.');
            return [
                'success' => false,
                'message' => 'FCM Server Key not configured',
                'payload' => $fcmPayload,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $fcmPayload);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
                'payload'  => $fcmPayload,
            ];
        } catch (\Throwable $e) {
            Log::error('FCM Game Invite Notification Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'payload' => $fcmPayload,
            ];
        }
    }

    /**
     * Build the raw FCM data payload array for a game invite.
     *
     * @param string $gameSlug
     * @param string $roomId
     * @param array|object $sender
     * @return array
     */
    public static function buildGameInviteDataPayload(
        string $gameSlug,
        string $roomId,
        array|object $sender
    ): array {
        $senderId = is_array($sender) ? ($sender['id'] ?? '') : ($sender->id ?? '');
        $senderName = is_array($sender) ? ($sender['name'] ?? 'Player') : ($sender->name ?? 'Player');
        $photoUrl = is_array($sender)
            ? ($sender['photo_url'] ?? $sender['avatar_url'] ?? '')
            : ($sender->photo_url ?? $sender->avatar_url ?? '');

        return [
            'title'        => 'New game challenge',
            'body'         => 'Invited you to play a game 🎮',
            'type'         => 'game_invite',
            'event_type'   => 'game_invite',
            'message_type' => 'game_invite',
            'media_url'    => "{$gameSlug}:{$roomId}",
            'sender_id'    => (string) $senderId,
            'sender_name'  => (string) $senderName,
            'photo_url'    => (string) $photoUrl,
        ];
    }

    /**
     * Build legacy FCM API request body ({ "to": ..., "data": ... })
     *
     * @param string $fcmToken
     * @param string $gameSlug
     * @param string $roomId
     * @param array|object $sender
     * @return array
     */
    public static function buildLegacyFcmRequest(
        string $fcmToken,
        string $gameSlug,
        string $roomId,
        array|object $sender
    ): array {
        return [
            'to'       => $fcmToken,
            'data'     => self::buildGameInviteDataPayload($gameSlug, $roomId, $sender),
            'priority' => 'high',
        ];
    }

    /**
     * Build FCM HTTP v1 request body ({ "message": { "token": ..., "data": ... } })
     *
     * @param string $fcmToken
     * @param string $gameSlug
     * @param string $roomId
     * @param array|object $sender
     * @return array
     */
    public static function buildHttpV1FcmRequest(
        string $fcmToken,
        string $gameSlug,
        string $roomId,
        array|object $sender
    ): array {
        return [
            'message' => [
                'token'   => $fcmToken,
                'data'    => self::buildGameInviteDataPayload($gameSlug, $roomId, $sender),
                'android' => [
                    'priority' => 'high',
                ],
            ],
        ];
    }
}
