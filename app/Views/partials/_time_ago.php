<?php

/**
 * Helper function to convert a datetime string to a human-readable relative time.
 *
 * @param string|null $datetime  A datetime string (e.g. '2026-03-10 08:00:00')
 * @return string  e.g. "2 hrs ago", "3 days ago", "Just now"
 */
function timeAgo(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }

    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) {
        return $diff->y === 1 ? '1 year ago' : $diff->y . ' years ago';
    }
    if ($diff->m > 0) {
        return $diff->m === 1 ? '1 month ago' : $diff->m . ' months ago';
    }
    if ($diff->d > 0) {
        if ($diff->d >= 7) {
            $weeks = (int)floor($diff->d / 7);
            return $weeks === 1 ? '1 week ago' : $weeks . ' weeks ago';
        }
        return $diff->d === 1 ? '1 day ago' : $diff->d . ' days ago';
    }
    if ($diff->h > 0) {
        return $diff->h === 1 ? '1 hr ago' : $diff->h . ' hrs ago';
    }
    if ($diff->i > 0) {
        return $diff->i === 1 ? '1 min ago' : $diff->i . ' mins ago';
    }

    return 'Just now';
}
