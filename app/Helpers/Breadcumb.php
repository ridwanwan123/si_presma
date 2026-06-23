<?php

if (!function_exists('breadcrumb')) {

    function breadcrumb(array $items): array
    {
        $result = [];

        foreach ($items as $label => $url) {

            // kalau key berupa angka
            if (is_numeric($label)) {

                $result[] = [
                    'label' => $url,
                    'url' => null,
                ];

            } else {

                $result[] = [
                    'label' => $label,
                    'url' => $url,
                ];

            }

        }

        return $result;
    }
}