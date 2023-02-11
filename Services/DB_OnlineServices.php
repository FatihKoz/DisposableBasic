<?php

namespace Modules\DisposableBasic\Services;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUp;

class DB_OnlineServices
{
    public function __construct(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function DownloadWhazzUp($network_selection = null, $server_address = null)
    {
        if (!$network_selection || !$server_address) {
            return;
        }

        try {
            $response = $this->httpClient->request('GET', $server_address);
            if ($response->getStatusCode() !== 200) {
                Log::error('Disposable Basic | HTTP ' . $response->getStatusCode() . ' Error Occured During WhazzUp Download !');
            }
        } catch (GuzzleException $e) {
            Log::error('Disposable Basic | WhazzUp Data Download Error: ' . $e->getMessage());
            return;
        }

        $whazzupdata = json_decode($response->getBody());

        if ($network_selection === 'VATSIM') {
            $whazzup_sections = [
                'network' => $network_selection,
                'pilots'  => (isset($whazzupdata)) ? json_encode($whazzupdata->pilots) : null,
                // 'atcos'   => json_encode($whazzupdata->controllers),
                // 'servers' => json_encode($whazzupdata->servers),
                // 'rawdata' => json_encode($whazzupdata),
            ];
        } else {
            $whazzup_sections = [
                'network' => $network_selection,
                'pilots'  => (isset($whazzupdata)) ? json_encode($whazzupdata->clients->pilots) : null,
                // 'atcos'        => json_encode($whazzupdata->clients->atcs),
                // 'observers'    => json_encode($whazzupdata->clients->observers),
                // 'servers'      => json_encode($whazzupdata->servers),
                // 'voiceservers' => json_encode($whazzupdata->voiceServers),
                // 'rawdata'      => json_encode($whazzupdata),
            ];
        }

        return DB_WhazzUp::updateOrCreate(['network' => $network_selection], $whazzup_sections);
    }
}
