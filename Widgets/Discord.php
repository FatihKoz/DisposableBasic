<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Discord extends Widget
{
    public $reloadTimeout = 60;

    protected $config = ['server' => null, 'bots' => false, 'bot' => ' Bot'];

    public function __construct(
        array $config = [],
        GuzzleClient $httpClient
    ) {
        parent::__construct($config);
        $this->httpClient = $httpClient;
    }

    public function run()
    {
        $server_id = $this->config['server'];

        if (empty($server_id) || !is_numeric($server_id)) {
            return null;
        }

        $discord_url = 'https://discord.com/api/guilds/' . $server_id . '/widget.json';

        try {
            $response = $this->httpClient->request('GET', $discord_url);
            if ($response->getStatusCode() !== 200) {
                Log::error('Disposable Basic, HTTP ' . $response->getStatusCode() . ' error occured during download !');
                return null;
            }
        } catch (GuzzleException $e) {
            Log::error('Disposable Basic, Discord Widget download error | ' . $e->getMessage());
            return null;
        }

        $widget_data = json_decode($response->getBody());

        $name = $widget_data->name;
        $invite = $widget_data->instant_invite;
        $presence = $widget_data->presence_count;

        // Collect Channels
        $channels = collect();
        foreach ($widget_data->channels as $ch) {
            $channels->push($ch);
        }

        // Collect Users (with Bot removal)
        $members = collect();
        foreach ($widget_data->members as $rm) {
            if ($this->config['bots'] === false && strpos($rm->username, $this->config['bot']) !== false) {
                continue;
            }
            $members->push($rm);
        }

        return view('DBasic::widgets.discord', [
            'channels'   => $channels,
            'invite'     => $invite,
            'is_visible' => (count($members) > 0) ? true : false,
            'members'    => $members,
            'name'       => $name,
            'presence'   => $presence,
        ]);
    }
}
