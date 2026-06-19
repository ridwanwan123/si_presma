<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $event,
        string $description,
        $subject = null,
        array $properties = []
    ) {
        $user = Auth::user();

        $defaultProperties = [
            'user' => [
                'id' => $user?->id,
                'username' => $user?->username,
                'email' => $user?->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $activity = activity()
            ->event($event);

        if ($user) {
            $activity->causedBy($user);
        }

        if ($subject) {
            $activity->performedOn($subject);
        }

        $activity
            ->withProperties(
                array_merge($defaultProperties, $properties)
            )
            ->log($description);
    }
}