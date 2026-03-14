<?php

namespace app\config;

/**
 * Centralized configuration for homepage sections.
 *
 * These constants control how many articles appear in each section of the homepage.
 * In the future, these can be moved to a database `settings` table so that
 * editors/admins can control them from an admin panel.
 */
class HomepageConfig
{
    /** Number of latest articles displayed in the hero section (regardless of category). */
    public const HERO_ARTICLE_COUNT = 2;

    /** Number of articles displayed per category section. */
    public const CATEGORY_ARTICLE_COUNT = 4;

    /** 
     * The category slug that always appears as the second section (right after hero).
     * All other categories follow after this one.
     */
    public const SECOND_CATEGORY_SLUG = 'bangladesh';
}
