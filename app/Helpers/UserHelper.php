<?php

if (!function_exists('getUserInitials')) {
    /**
     * Generate user initials from name
     *
     * @param string $name
     * @return string
     */
    function getUserInitials($name)
    {
        if (empty($name)) {
            return 'U';
        }

        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        // Limit to 2 characters maximum
        return substr($initials, 0, 2);
    }
}

if (!function_exists('getAvatarUrl')) {
    /**
     * Get user avatar URL or generate initials-based avatar
     *
     * @param \App\Models\User|null $user
     * @param int $size
     * @return string
     */
    function getAvatarUrl($user, $size = 100)
    {
        if (!$user) {
            return generateInitialsAvatar('U', $size);
        }

        // If user has photo, return the photo URL
        if (!empty($user->photo)) {
            return asset('storage/' . $user->photo);
        }

        // Generate initials-based avatar
        $initials = getUserInitials($user->name);
        return generateInitialsAvatar($initials, $size);
    }
}

if (!function_exists('generateInitialsAvatar')) {
    /**
     * Generate initials-based avatar URL
     *
     * @param string $initials
     * @param int $size
     * @param string $bgColor
     * @param string $textColor
     * @return string
     */
    function generateInitialsAvatar($initials, $size = 100, $bgColor = '6366f1', $textColor = 'ffffff')
    {
        return "https://via.placeholder.com/{$size}x{$size}/{$bgColor}/{$textColor}?text=" . urlencode($initials);
    }
}

if (!function_exists('getRandomAvatarColor')) {
    /**
     * Get random color for avatar based on user name
     *
     * @param string $name
     * @return string
     */
    function getRandomAvatarColor($name)
    {
        $colors = [
            '6366f1', // indigo
            'ec4899', // pink
            '10b981', // emerald
            'f59e0b', // amber
            'ef4444', // red
            '8b5cf6', // violet
            '06b6d4', // cyan
            'f97316', // orange
            '84cc16', // lime
            '3b82f6', // blue
        ];

        $hash = crc32($name);
        $index = abs($hash) % count($colors);
        
        return $colors[$index];
    }
}

if (!function_exists('getUserAvatarWithColor')) {
    /**
     * Get user avatar URL with random color based on name
     *
     * @param \App\Models\User|null $user
     * @param int $size
     * @return string
     */
    function getUserAvatarWithColor($user, $size = 100)
    {
        if (!$user) {
            return generateInitialsAvatar('U', $size);
        }

        // If user has photo, return the photo URL
        if (!empty($user->photo)) {
            return asset('storage/' . $user->photo);
        }

        // Generate initials-based avatar with random color
        $initials = getUserInitials($user->name);
        $bgColor = getRandomAvatarColor($user->name);
        return generateInitialsAvatar($initials, $size, $bgColor);
    }
}