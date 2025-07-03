<?php

namespace App\Actions;

class GenerateNotificationAction
{
    public static function handle($notificationType, $action = '', $user = null, array $data = [])
    {
        switch ($notificationType) {
            case 'application':
                if ($action === 'application-created') {
                    return "{$user->profile->full_name} has applied to be a service provider in ServEase. Click to see the details.";
                }

                if ($action === 'application-approved') {
                    return "Your application as a service provider in ServEase has been approved.";
                }

                if ($action === 'application-updated') {
                    return "{$user->profile->full_name} has re-submitted their application. Click to see the details.";
                }

                if ($action === 'application-rejected') {
                    $reason = $data['remark'] ?? '';
                    if (($reason === 'Other' || $reason === 'other') && !empty($data['otherRemark'])) {
                        $reason .= ': ' . $data['otherRemark'];
                    }
                    return "Your application as service provider has been rejected. Reason: {$reason}";

                    // return "Your application as a service provider in ServEase has been rejected.";
                }
                break;
            case 'booking':
                if ($action === 'booking-confirmed') {
                    return "{$user->name} has confirmed your booking. You can now send initial or full payment.";
                }

                if ($action === 'booking-rejected') {
                    $reason = $data['remark'] ?? '';
                    if (($reason === 'Other' || $reason === 'other') && !empty($data['otherRemark'])) {
                        $reason .= ': ' . $data['otherRemark'];
                    }
                    return "The service that you booked with {$user->name} has been declined. Reason: {$reason}";
                }

                if ($action === 'booking-started') {
                    return "Your booking for {$user->name}'s service has started.";
                }

                if ($action === 'booking-cancelled') {
                    return "{$user->name} has cancelled the booking. Reason: {$data['remark']}";
                }

                if ($action === 'booking-created') {
                    return "{$user->name} has booked with your service. Click to see the details.";
                }

                if ($action === 'booking-completed') {
                    return "The service that you booked with {$user->name} has been completed. Click to see the details.";
                }

                if ($action === 'fully-paid') {
                    return "The service that you booked with {$user->name} has been completed. Click to see the details. If you've got a moment, feel free to write a review. Your insights go a long way.";
                }
                break;
            case 'feedback':
                if ($action === 'feedback-created') {
                    return "{$user->name} has given feedback on your service: {$data['feedback']}. Click to see the details.";
                }
                break;
            case 'payment':
                if ($action === 'payment-created') {
                    return "{$user->name} has created a payment for your service. Click to see the details.";
                }
                if ($action === 'payment-reservation') {
                    return "{$user->name} has created a reservation payment for your service. Click to see the details.";
                }
                if ($action === 'payment-approved') {
                    return "{$user->name} has approved your payment. If you've got a moment, feel free to write a review. Your insights go a long way.";
                }
                if ($action === 'payment-rejected') {
                    return "{$user->name} has rejected your payment. Click to see the details.";
                }
                break;
        }
    }
}
