<?php

namespace App\Enums;

enum SettingKey: string
{
    case SOCIAL_LINKS = 'social_links';
    case NOTIFICATION_EMAILS = 'notification_emails';
    case SITE_TITLE = 'site_title';
    case CONTACT_PHONE_NUMBER = 'CONTACT_PHONE_NUMBER';
    case EMAIL_ADDRESS = 'email_address';
    case ADDRESS = 'address';
    case COMPANY_TEAM = 'company_team';
    case TINY_EDITOR = 'tiny_editor';
    case LOGO = 'logo';
    case QUEUE_MONITOR_UI = 'queue_monitor_ui';

    case VISA_FAQ = 'visa_faq';
    case VISA_TERMS = 'visa_terms';
    case VISA_PRIVACY = 'visa_privacy';
    case VISA_ABOUT = 'visa_about';
    case VISA_SUPPORT_EMAIL = 'visa_support_email';
    case VISA_SUPPORT_WHATSAPP = 'visa_support_whatsapp';
    case VISA_SUPPORT_PHONE = 'visa_support_phone';
    case VISA_SUPPORT_TITLE = 'visa_support_title';
    case VISA_SUPPORT_SUBTITLE = 'visa_support_subtitle';
    case VISA_LOYALTY = 'visa_loyalty';

    case COMPANY_LOCATION_URL = 'company_location_url';
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
