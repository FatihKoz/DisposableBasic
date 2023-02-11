<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_Discord;
use Theme;

class Discord extends Widget
{
    public $reloadTimeout = 60;

    protected $config = ['server' => null, 'bots' => false, 'bot' => ' Bot', 'gdpr' => false, 'icao' => null];

    public function __construct(
        array $config = [],
        GuzzleClient $httpClient
    ) {
        parent::__construct($config);
        $this->httpClient = $httpClient;
    }

    public function run()
    {
        if (filled(Theme::getSetting('gen_discord_server')) && $this->config['server'] === null) {
            $theme_server_id = Theme::getSetting('gen_discord_server');
        }

        $server_id =  isset($theme_server_id) ? $theme_server_id : $this->config['server'];

        if (empty($server_id) || !is_numeric($server_id)) {
            return null;
        }

        $discord = DB_Discord::where('server_id', $server_id)->first();

        if (!$discord || $discord->updated_at->diffInSeconds() > $this->reloadTimeout) {
            // Prepare Model Data
            $model_data = [];
            $model_data['rawdata'] = null;

            // Download
            $discord_url = 'https://discord.com/api/guilds/' . $server_id . '/widget.json';

            try {
                $response = $this->httpClient->request('GET', $discord_url);
                if ($response->getStatusCode() == 200) {
                    $model_data['rawdata'] = $response->getBody();
                } else {
                    Log::error('Disposable Basic, HTTP ' . $response->getStatusCode() . ' error occured during download !');
                    // return null;
                }
            } catch (GuzzleException $e) {
                Log::error('Disposable Basic, Discord Widget download error | ' . $e->getMessage());
                // return null;
            }

            $discord = DB_Discord::updateOrCreate(['server_id' => $server_id], $model_data);
        }

        // Collections
        $channels = collect();
        $members = collect();

        $widget_data = json_decode($discord->rawdata);

        if ($widget_data) {
            $name = $widget_data->name;
            $invite = $widget_data->instant_invite;
            $presence = $widget_data->presence_count;

            // Collect Channels
            foreach ($widget_data->channels as $ch) {
                $channels->push($ch);
            }

            // Collect Users (with Bot removal)
            foreach ($widget_data->members as $rm) {
                if ($this->config['bots'] === false && strpos($rm->username, $this->config['bot']) !== false) {
                    continue;
                }
                if (is_null($this->config['icao']) === false && strpos($rm->username, $this->config['icao']) === false) {
                    continue;
                }
                if ($this->config['gdpr'] === true) {
                    $rm->username = $this->GDPR_Names($rm->username);
                }
                $members->push($rm);
            }
        }

        return view('DBasic::widgets.discord', [
            'channels'   => $channels,
            'invite'     => isset($invite) ? $invite : null,
            'is_visible' => (count($members) > 0) ? true : false,
            'members'    => $members,
            'name'       => isset($name) ? $name : null,
            'presence'   => isset($presence) ? $presence : null,
        ]);
    }

    public function GDPR_Names($full_name)
    {
        $parts = explode(' ', $full_name);
        $count = count($parts);

        if ($count === 1) {
            return $parts[0];
        }

        $gdpr_name = '';
        $last_name = $parts[$count - 1];
        $loop_count = 0;

        while ($loop_count < ($count - 1)) {
            $gdpr_name .= ' ' . $parts[$loop_count];
            $loop_count++;
        }

        $gdpr_name .= ' ' . mb_substr($last_name, 0, 1);

        return $gdpr_name;
    }
}
