<?php

namespace App\Helpers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class WhatsAppHelper
{
    private static function url(string $endpoint): string
    {
        $port = config('whatsapp.port');
        return "http://localhost:{$port}/{$endpoint}";
    }

    /**
     * Request headers
     *
     * @param string $key
     * @param string|null $token
     * @return string[]
     */
    public static function headers(string $key, ?string $token): array
    {
        if ($token)
            return [ 'Authorization' => $key, 'token' => $token ];

        return [ 'Authorization' => $key ];
    }

    /**
     * List users
     * [GET] /admin/users
     *
     * Retrieve a list of users from the database, displaying also connection status and other info.
     *
     * @throws ConnectionException
     */
    public static function adminUsers(array $headers): array
    {
        $endpoint = "admin/users";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->get($url);

        return json_decode($response->body(), true);
    }

    /**
     * Create a new user
     * [POST] /admin/users
     *
     * Add a new user to the database.
     *
     * {
     *      "name": "John Doe",
     *      "token": "1234ABCD",
     *      "webhook": "https://webhook.site/1234567890",
     *      "events": "All",
     *      "proxyConfig": {
     *          "enabled": true,
     *          "proxyURL": "https://serverproxy.com:9080"
     *      },
     *      "s3Config": {
     *          "enabled": true,
     *          "endpoint": "https://s3.amazonaws.com",
     *          "region": "us-east-1",
     *          "bucket": "my-bucket",
     *          "accessKey": "1234567890",
     *          "secretKey": "1234567890",
     *          "pathStyle": true,
     *          "publicURL": "https://s3.amazonaws.com",
     *          "mediaDelivery": "both",
     *          "retentionDays": 30
     *      }
     * }
     *
     * @throws ConnectionException
     */
    public static function createAdminUser(array $body, array $headers): array
    {
        $endpoint = "admin/users";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, $body);

        return json_decode($response->body(), true);
    }

    /**
     * Delete a user from DB
     * [DELETE] /admin/users/{id}
     *
     * Deletes a user by their ID.
     *
     * @throws ConnectionException
     */
    public static function destroyAdminUser(string $id, bool $full, array $headers): array
    {
        $endpoint = "admin/users/$id";
        if ($full)
            $endpoint .= "/full";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->delete($url);

        return json_decode($response->body(), true);
    }

    /**
     * Connects to WhatsApp servers
     * [POST] /session/connect
     *
     * Initiates connection to WhatsApp servers.
     * If there is no previous session created, it will generate a QR code that can be retrieved via the qr API call.
     * If the optional Subscribe is supplied, it will limit webhooks to the specified event types: Message, ReadReceipt, Presence, HistorySync, ChatPresence.
     * If no Subscribe is supplied, it will subscribe to All events.
     * If Immediate is set to false, the action will wait for 10 seconds to retrieve actual connection status from WhatsApp, otherwise it will return immediately.
     * When setting Immediate to true, you should check for actual connection status after a few seconds via the status API call as your connection might fail if the session was closed from another device.
     *
     * {
     *      "Subscribe": [
     *          "Message",
     *          "ChatPresence"
     *      ],
     *      "Immediate": true
     * }
     *
     * @throws ConnectionException
     */
    public static function sessionConnect(array $events, array $headers): array
    {
        $endpoint = "session/connect";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            "Subscribe" => $events,
            "Immediate" => true,
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Disconnects from WhatsApp servers
     * [POST] /session/disconnect
     *
     * Closes connection to WhatsApp servers. The Session is not terminated; that means that calling connect again will reuse the previous session avoiding QR code scanning
     *
     * @throws ConnectionException
     */
    public static function sessionDisconnect(array $headers): array
    {
        $endpoint = "session/disconnect";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url);

        return json_decode($response->body(), true);
    }

    /**
     * Logs out from WhatsApp
     * [POST] /session/logout
     *
     * Closes connection to WhatsApp servers and terminate session. That means that next time connect is issued, QR scan will be needed from the phone to connect again.
     *
     * @throws ConnectionException
     */
    public static function sessionLogout(array $headers): array
    {
        $endpoint = "session/logout";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url);

        return json_decode($response->body(), true);
    }

    /**
     * Gets connection and session status
     * [GET] /session/status
     *
     * Gets status from connection, including websocket connection and logged in status (session)
     *
     * @throws ConnectionException
     */
    public static function sessionStatus(array $headers): array
    {
        $endpoint = "session/status";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->get($url);

        return json_decode($response->body(), true);
    }

    /**
     * Gets QR code for scanning
     * [GET] /session/qr
     *
     * Get QR code if the user is connected but not logged in. If the user is already logged in, QRCode will be empty.
     *
     * @throws ConnectionException
     */
    public static function sessionQR(array $headers): array
    {
        $endpoint = "session/qr";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->get($url);

        return json_decode($response->body(), true);
    }

    /**
     * Checks if user has WhatsApp
     * [POST] /user/check
     *
     * Checks if users have an account with WhatsApp.
     *
     * {
     *      "Phone": ["5491155553935", "5491155553931"]
     * }
     *
     * @throws ConnectionException
     */
    public static function userCheck(array $phone, array $headers): array
    {
        $endpoint = "user/check";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            'Phone' => $phone,
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Gets all contacts for the account
     * [GET] /user/contacts
     *
     * Get a complete list of contacts for the connected account.
     *
     * @throws ConnectionException
     */
    public static function userContacts(array $headers): array
    {
        $endpoint = "user/contacts";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->get($url);

        return json_decode($response->body(), true);
    }

    /**
     * Sends a text message
     * [POST] /chat/send/text
     *
     * Phone and Body are mandatory. If no Id is supplied, a random one will be generated.
     * ContextInfo is optional and used when replying to some message.
     * StanzaId is the message id we are replying to and participant who wrote that message.
     * If sending a new message, ContextInfo can be omitted altogether.
     *
     * {
     *      "Phone": "5491155553935",
     *      "Body": "How you doin",
     *      "Id": "ABCDABCD1234",
     *      "ContextInfo": {
     *          "StanzaId": "3EB06F9067F80BAB89FF",
     *          "Participant": "5491155553935@s.whatsapp.net"
     *      }
     * }
     *
     * @throws ConnectionException
     */
    public static function chatText(string $phone, string $message, array $headers): array
    {
        $endpoint = "chat/send/text";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            'Phone' => $phone,
            'Body' => $message,
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Sends an image/picture message
     * [POST] /chat/send/image
     *
     * Sends an image message (must be base64 encoded in image/png or image/jpeg formats).
     *
     * {
     *      "Phone": "5491155553935",
     *      "Image": "data:image/jpeg;base64,iVBORw0",
     *      "Caption": "Image Description",
     *      "Id": "ABCDABCD1234",
     *      "ContextInfo": {
     *          "StanzaId": "3EB06F9067F80BAB89FF",
     *          "Participant": "5491155553935@s.whatsapp.net"
     *      }
     * }
     *
     * @throws ConnectionException
     */
    public static function chatImage(string $phone, string $image, string $message, array $headers): array
    {
        $endpoint = "chat/send/image";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            'Phone' => $phone,
            'Caption' => $message,
            'Image' => $image,
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Sends a document message
     * [POST] /chat/send/document
     *
     * Sends any document (must be base64 encoded using application/octet-stream mime)
     *
     * {
     *      "Phone": "5491155553935",
     *      "Document": "data:application/octet-stream;base64,aG9sYSBxdWUKdGFsCmNvbW8KZXN0YXMK",
     *      "FileName": "file.txt",
     *      "Id": "ABCDABCD1234",
     *      "ContextInfo": {
     *          "StanzaId": "3EB06F9067F80BAB89FF",
     *          "Participant": "5491155553935@s.whatsapp.net"
     *      }
     * }
     *
     * @throws ConnectionException
     */
    public static function chatDocument(string $phone, string $file, string $fileName, array $headers): array
    {
        $endpoint = "chat/send/document";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            'Phone' => $phone,
            'FileName' => $fileName,
            'Document' => $file,
        ]);

        return json_decode($response->body(), true);
    }

    /**
     * Sends a sticker message
     * [POST] /chat/send/sticker
     *
     * Sends a sticker message (must be base64 encoded in image/webp format)
     *
     * {
     *      "Phone": "5491155553935",
     *      "Sticker": "data:image/webp;base64,iVBORw0",
     *      "Id": "ABCDABCD1234",
     *      "PngThumbnail": "AA00D010",
     *      "ContextInfo": {
     *          "StanzaId": "3EB06F9067F80BAB89FF",
     *          "Participant": "5491155553935@s.whatsapp.net"
     *      }
     * }
     *
     * @throws ConnectionException
     */
    public static function chatSticker(string $phone, string $thumbnail, string $sticker, array $headers): array
    {
        $endpoint = "chat/send/sticker";
        $url = self::url($endpoint);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            ...$headers,
        ])->post($url, [
            'Phone' => $phone,
            'Sticker' => $sticker,
            'PngThumbnail' => $thumbnail,
        ]);

        return json_decode($response->body(), true);
    }
}
