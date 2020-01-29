<?php

namespace Spatie\BackupServer\Notifications\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\BackupServer\Notifications\Notifications\Concerns\HandlesNotifications;
use Spatie\BackupServer\Tasks\Backup\Events\BackupFailedEvent;

class BackupFailedNotification extends Notification implements ShouldQueue
{
    use HandlesNotifications;

    private BackupFailedEvent $event;

    public function __construct(BackupFailedEvent $event)
    {
        $this->event = $event;
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->error()
            ->from($this->fromEmail(), $this->fromName())
            ->subject(trans('backup::notifications.backup_failed_subject', ['source_name' => $this->sourceName()]))
            ->line(trans('backup::notifications.backup_failed_body', ['application_name' => $this->sourceName()]))
            ->line(trans('backup::notifications.exception_message', ['message' => $this->event->throwable->getMessage()]))
            ->line(trans('backup::notifications.exception_trace', ['trace' => $this->event->throwable->getTraceAsString()]));

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return $this->slackMessage()
            ->error()
            ->content(trans('backup::notifications.backup_failed_subject', ['source_name' => $this->sourceName()]))
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title(trans('backup::notifications.exception_message_title'))
                    ->content($this->event->throwable->getMessage());
            })
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title(trans('backup::notifications.exception_trace_title'))
                    ->content($this->event->throwable->getTraceAsString());
            });
    }

    public function sourceName(): string
    {
        return $this->event->backup->source->name;
    }
}
