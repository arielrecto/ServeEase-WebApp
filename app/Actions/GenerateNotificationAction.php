<?php

namespace App\Actions;

class GenerateNotificationAction
{
    public static function handle($notificationType, $action = '', $user = null, array $data = [])
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
                if ($action === 'booking-confirmed') {
                    return "{$user->name} has confirmed your booking. Click to see the details.";
                }

                if ($action === 'booking-rejected') {
                    return "The service that you booked with {$user->name} has been declined. Reason: {$data['remark']}";
                }

                if ($action === 'booking-started') {
                    return "Your booking for {$user->name}'s service has started.";
                }

                if ($action === 'booking-cancelled') {
                    return "{$user->name} has cancelled your booking.";
                }

                if ($action === 'booking-created') {
                    return "{$user->name} has booked with your service. Click to see the details.";
                }

                if ($action === 'booking-completed') {
                    return "The service that you booked with {$user->name} has been completed. Write a review.";
                }
                break;
        }
    }
}
