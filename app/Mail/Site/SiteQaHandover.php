<?php

namespace App\Mail\Site;

use App\Models\Site\SiteQa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteQaHandover extends Mailable {

    use Queueable, SerializesModels;

    public $qa;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteQa $qa)
    {
        $this->qa = $qa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/qa-handover')->subject('SafeWorksite - QA Handover');
    }
}
