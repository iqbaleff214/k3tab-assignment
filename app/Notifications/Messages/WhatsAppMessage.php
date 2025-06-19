<?php

namespace App\Notifications\Messages;

class WhatsAppMessage
{
    public string $phone;
    public ?string $message;
    public ?string $image;
    public ?string $document;
    public ?string $documentName;

    public function __construct(string $phone, ?string $message = null)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    public function image(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function document(string $document, string $filename): static
    {
        $this->document = $document;
        $this->documentName = $filename;
        return $this;
    }
}
