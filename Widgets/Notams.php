<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Airport;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Notams extends Widget
{
    protected $config = ['icao' => null, 'filter' => false];

    public function __construct(
        GuzzleClient $httpClient,
        array $config = []
    ) {
        parent::__construct($config);
        $this->httpClient = $httpClient;
    }

    public function run()
    {
        $filter = is_bool($this->config['filter']) ? $this->config['filter'] : false;
        $icao = (isset($this->config['icao']) && Airport::where('id', $this->config['icao'])->count() > 0) ? $this->config['icao'] : null;

        $service_url = null;

        if ($icao) {
            $service_url = 'https://ourairports.com/airports/' . $icao . '/notams.rss';

            $cache_key = 'airports.notam.' . $icao;
            $notams = Cache::get($cache_key);

            if (empty($notams)) {
                $notams = [];

                try {
                    $response = $this->httpClient->request('GET', $service_url);
                    if ($response->getStatusCode() !== 200) {
                        Log::error('Disposable Basic | HTTP ' . $response->getStatusCode() . ' Error Occured During NOTAM Feed Retrieval !');
                    }
                } catch (GuzzleException $e) {
                    Log::error('Disposable Basic | ' . $icao . ' NOTAM Feed Download Error, ' . $e->getMessage());
                }

                try {
                    $rss_feed = isset($response) ? simplexml_load_string($response->getBody()) : null;
                } catch (Exception $e) {
                    Log::error('Disposable Basic | ' . $icao . ' NOTAM Feed Processing Error, ' . $e->getMessage());
                }

                if (isset($rss_feed) && is_object($rss_feed) && isset($rss_feed->channel) && (is_array($rss_feed->channel->item) || is_object($rss_feed->channel->item))) {
                    foreach ($rss_feed->channel->item as $notam) {
                        if ($filter && !str_contains($notam->title, 'NOTAM A')) {
                            continue;
                        }

                        $notams[] = [
                            'icao'  => $icao,
                            'title' => (string) $notam->title,
                            'text'  => (string) $notam->description,
                        ];
                    }
                }

                // Cache results for 45 mins
                Cache::add(
                    $cache_key,
                    $notams,
                    60 * 45,
                );
            }
        }

        return view('DBasic::widgets.notams', [
            'icao'       => $icao,
            'is_visible' => (isset($icao)) ? true : false,
            'notams'     => $notams,
        ]);
    }
}
