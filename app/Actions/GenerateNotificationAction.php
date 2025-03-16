<?php

namespace App\Actions;

class GenerateNotificationAction
{
    public static function handle($notificationType, $action = '', $user = null)
    {
        switch ($notificationType) {
            case 'application':
                if ($action === 'application-approved') {
                    return "Your application as a service provider in ServEase has been approved.";
                }

                if ($action === 'application-rejected') {
                    return "Your application as a service provider in ServEase has been rejected.";
                }
                break;
            case 'booking':
                if ($action === 'booking-completed') {
                    return "The service that you booked with {$user->name} has been completed. Write a review.";
                }
                break;
        }
    }
}
