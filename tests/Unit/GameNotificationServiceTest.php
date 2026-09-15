<?php

namespace Tests\Unit;

use App\Services\GameNotificationService;
use PHPUnit\Framework\TestCase;

class GameNotificationServiceTest extends TestCase
{
    public function test_it_builds_expected_fcm_game_invite_payload(): void
    {
        $sender = [
            'id'        => 76,
            'name'      => 's26',
            'photo_url' => 'https://cloudfront.net/profile.jpg',
        ];

        $payload = GameNotificationService::buildGameInviteDataPayload(
            gameSlug: 'tic-tac-toe',
            roomId: 'room_12345',
            sender: $sender
        );

        $this->assertSame('New game challenge', $payload['title']);
        $this->assertSame('Invited you to play a game 🎮', $payload['body']);
        $this->assertSame('game_invite', $payload['type']);
        $this->assertSame('game_invite', $payload['message_type']);
        $this->assertSame('tic-tac-toe:room_12345', $payload['media_url']);
        $this->assertSame('76', $payload['sender_id']);
        $this->assertSame('s26', $payload['sender_name']);
        $this->assertSame('https://cloudfront.net/profile.jpg', $payload['photo_url']);
    }

    public function test_it_builds_expected_fcm_legacy_request_body(): void
    {
        $sender = [
            'id'        => '76',
            'name'      => 's26',
            'photo_url' => 'https://cloudfront.net/profile.jpg',
        ];

        $requestBody = GameNotificationService::buildLegacyFcmRequest(
            fcmToken: 'DEVICE_FCM_TOKEN',
            gameSlug: 'tic-tac-toe',
            roomId: 'room_12345',
            sender: $sender
        );

        $expected = [
            'to'   => 'DEVICE_FCM_TOKEN',
            'data' => [
                'title'        => 'New game challenge',
                'body'         => 'Invited you to play a game 🎮',
                'type'         => 'game_invite',
                'event_type'   => 'game_invite',
                'message_type' => 'game_invite',
                'media_url'    => 'tic-tac-toe:room_12345',
                'sender_id'    => '76',
                'sender_name'  => 's26',
                'photo_url'    => 'https://cloudfront.net/profile.jpg',
            ],
            'priority' => 'high',
        ];

        $this->assertSame($expected, $requestBody);
    }
}
