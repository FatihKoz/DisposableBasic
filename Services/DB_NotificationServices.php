<?php

namespace Modules\DisposableBasic\Services;

use Illuminate\Support\Facades\Log;

class DB_NotificationServices
{
    public function PirepMessage($pirep, $content)
    {
        $wh_url = DB_Setting('dbasic.discord_pirep_webhook');
        $message_poster = !empty(DB_Setting('dbasic.discord_pirep_msgposter')) ? DB_Setting('dbasic.discord_pirep_msgposter') : config('app.name');

        $user_avatar = !empty($pirep->user->avatar) ? $pirep->user->avatar->url : $pirep->user->gravatar(256);
        $pirep_aircraft = !empty($pirep->aircraft) ? $pirep->aircraft->registration . ' (' . $pirep->aircraft->icao . ')' : 'Not Reported';

        $json_data = json_encode([
            // Plain text message
            'content'  => $content,
            'username' => $message_poster,
            'tts'      => false,
            'embeds'   => [
                // Embed content
                [
                    'title'     => '**Flight Details**',
                    'image'     => ['url' => $pirep->airline->logo],
                    'type'      => 'rich',
                    'timestamp' => date('c', strtotime($pirep->submitted_at)),
                    'color'     => hexdec('FF0000'),
                    'thumbnail' => ['url' => $user_avatar],
                    'author'    => ['name' => 'Pilot In Command: ' . $pirep->user->name_private, 'url' => route('frontend.profile.show', [$pirep->user->id])],
                    // Additional embed fields (Discord displays max 3 items per row)
                    'fields' => [
                        [
                            'name'   => '__Flight #__',
                            'value'  => $pirep->airline->code . $pirep->flight_number,
                            'inline' => true
                        ], [
                            'name'   => '__Origin__',
                            'value'  => $pirep->dpt_airport_id,
                            'inline' => true
                        ], [
                            'name'   => '__Destination__',
                            'value'  => $pirep->arr_airport_id,
                            'inline' => true
                        ], [
                            'name'   => '__Distance__',
                            'value'  => $pirep->distance . ' nm',
                            'inline' => true
                        ], [
                            'name'   => '__Block Time__',
                            'value'  => DB_ConvertMinutes($pirep->flight_time),
                            'inline' => true
                        ], [
                            'name'   => '__Equipment__',
                            'value'  => $pirep_aircraft,
                            'inline' => true
                        ],
                    ],
                ],
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->DiscordNotification($wh_url, $json_data);
    }

    public function DiscordNotification($webhook_url, $json_data)
    {
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if ($response) {
            Log::debug('Disposable Basic | Discord WebHook Msg Response: ' . $response);
        }
        curl_close($ch);
    }
}
