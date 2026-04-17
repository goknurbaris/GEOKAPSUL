<?php

namespace App\Console\Commands;

use App\Models\Capsule;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendCapsuleUnlockReminders extends Command
{
    protected $signature = 'capsules:send-unlock-reminders';

    protected $description = 'Sends daily reminders for capsules that unlock today or tomorrow';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        $sentCount = 0;

        $capsules = Capsule::query()
            ->whereNotNull('unlock_date')
            ->whereBetween('unlock_date', [$today->toDateString(), $tomorrow->toDateString()])
            ->get();

        foreach ($capsules as $capsule) {
            $unlockDate = $capsule->unlock_date?->startOfDay();
            if (!$unlockDate) {
                continue;
            }

            $isTomorrow = $unlockDate->isSameDay($tomorrow);
            $title = $isTomorrow ? 'Kapsül yarın açılıyor' : 'Kapsül bugün açılıyor';
            $body = $capsule->category === 'anniversary'
                ? ($isTomorrow
                    ? 'Yıldönümü kapsülün yarın açılacak. Kutlamaya hazır ol! 🎉'
                    : 'Yıldönümü kapsülün bugün açılabilir durumda. 🎂')
                : ($isTomorrow
                    ? 'Kapsülün yarın açılacak: ' . Str::limit($capsule->message, 60)
                    : 'Kapsülün bugün açılabilir: ' . Str::limit($capsule->message, 60));

            $actionUrl = route('dashboard', ['capsule' => $capsule->id]);
            $alreadySentToday = Notification::query()
                ->where('user_id', $capsule->user_id)
                ->where('type', 'capsule-unlock-reminder')
                ->where('action_url', $actionUrl)
                ->whereDate('created_at', $today->toDateString())
                ->exists();

            if ($alreadySentToday) {
                continue;
            }

            Notification::create([
                'user_id' => $capsule->user_id,
                'type' => 'capsule-unlock-reminder',
                'title' => $title,
                'body' => $body,
                'action_url' => $actionUrl,
            ]);

            $sentCount++;
        }

        $this->info("{$sentCount} reminder(s) sent.");

        return self::SUCCESS;
    }
}

