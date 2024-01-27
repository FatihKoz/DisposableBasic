<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\NewsAdded;
use Modules\DisposableBasic\Services\DB_NotificationServices;

class Gen_News
{
    public function handle(NewsAdded $event)
    {
        $news = $event->news;
        $news->loadMissing('user');

        if (DB_Setting('dbasic.discord_newsmsg', true)) {
            // Send Discord Notification
            $NotificationSvc = app(DB_NotificationServices::class);
            $NotificationSvc->NewsMessage($news);
        }
    }
}
