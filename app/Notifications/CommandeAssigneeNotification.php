<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommandeAssigneeNotification extends Notification
{
    use Queueable;

    protected $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle commande à livrer')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Une nouvelle commande (#' . $this->commande->id . ') vous a été assignée.')
            ->action('Voir la commande', url('/livreur/dashboard'))
            ->line('Merci de traiter cette livraison rapidement.');
    }
}