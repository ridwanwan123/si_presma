<?php

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTE (ROLE-AWARE)
|--------------------------------------------------------------------------
| Ganti isi function dashboardRoute() yang sudah ada di
| app/Helpers/DashboardRouter.php (yang sekarang sudah terdaftar di
| composer.json -> autoload.files) dengan versi ini.
*/

if (! function_exists('dashboardRoute')) {
    function dashboardRoute(): string
    {
        $user = auth()->user();

        if (! $user) {
            return route('login.form');
        }

        if ($user->hasRole('Madrasah')) {
            return route('dashboard.madrasah');
        }

        if ($user->hasRole('Pengawas')) {
            return route('dashboard.asesor');
        }

        return route('dashboard');
    }
}