<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentWorkflowNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $actionType = 'general',
        public ?string $url = null,
        public ?string $documentType = null,
        public int|string|null $documentId = null,
        public ?string $referenceCode = null,
        public ?string $senderName = null,
        public ?int $senderId = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_type' => $this->actionType,
            'url' => $this->url,
            'document_type' => $this->documentType,
            'document_id' => $this->documentId,
            'reference_code' => $this->referenceCode,
            'sender_name' => $this->senderName,
            'sender_id' => $this->senderId,
        ];
    }
}
