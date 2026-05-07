<?php

namespace App\Observers;

use App\Events\TicketMessage as TicketEvent;
use App\Models\TicketMessage;
// ==== 新增以下 3 行引用 ====
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TicketMessageObserver
{
    /**
     * Handle the TicketMessage "created" event.
     */
    public function created(TicketMessage $ticketMessage): void
    {
// ==== 邮件通知管理员部分 (仿WHMCS风格) ====
        if ($ticketMessage->user && $ticketMessage->user->role_id !== 1) {
            try {
                $ticket = $ticketMessage->ticket;
                $admins = \App\Models\User::where('role_id', 1)->get();
                
                // 获取基本信息
                $ticketId = $ticket->id;
                $ticketUrl = url("/admin/tickets/{$ticketId}/edit");
                $userName = $ticketMessage->user->name;
                $userId = $ticketMessage->user->id;
                $ticketSubject = $ticket->subject ?? 'No title';
                
                // 获取工单状态 (Paymenter 一般为 open, closed, answered 等)
                $status = ucfirst($ticket->status ?? 'Open'); 

                // 判断新工单还是回复
                $isNewTicket = $ticket->messages()->count() === 1;
                
                if ($isNewTicket) {
                    $actionType = "New Ticket";
                    $actionText = "A new ticket has been opened by {$userName} (ID: {$userId}).";
                } else {
                    $actionType = "New Reply";
                    $actionText = "Ticket #{$ticketId} has had a new reply posted by {$userName} (ID: {$userId}).";
                }

                // 过滤恶意代码，并使用 nl2br 完美保留用户输入的换行符
                $safeMessage = nl2br(strip_tags($ticketMessage->message));

                // 拼装 HTML 格式的邮件内容 (完美复刻 WHMCS 排版)
                $emailContent = "
                <div style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 15px;\">
                    <p style=\"margin-top: 0;\">{$actionText}</p>
                    <p><strong>Status:</strong> {$status} / Customer-Reply</p>
                    
                    <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;\">
                    
                    <div style=\"padding: 10px 0; font-size: 14px; color: #111; word-wrap: break-word;\">
                        {$safeMessage}
                    </div>
                    
                    <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;\">
                    
                    <p style=\"margin-bottom: 5px;\">You can respond to this ticket through the admin area at the url below:</p>
                    <p style=\"margin-top: 0;\"><a href=\"{$ticketUrl}\" style=\"color: #0052cc; text-decoration: none;\">{$ticketUrl}</a></p>
                </div>
                ";

                // 发送 HTML 邮件
                foreach ($admins as $admin) {
                    // 使用 Mail::html 替代 Mail::raw 以支持网页排版
                    \Illuminate\Support\Facades\Mail::html($emailContent, function ($mail) use ($admin, $ticketId, $ticketSubject, $actionType) {
                        $mail->to($admin->email)
                             // 邮件标题格式： [Ticket ID: 790] 无法连接服务器 - 新回复
                             ->subject("[Ticket ID: {$ticketId}] {$ticketSubject} - {$actionType}");
                    });
                }
            } catch (\Exception $e) {
                // 记录错误日志，防止工单保存失败
                \Illuminate\Support\Facades\Log::error('管理员工单通知发送失败: ' . $e->getMessage());
            }
        }
        // ==== 邮件通知部分结束 ====
        
        event(new TicketEvent\Created($ticketMessage));
    }

    /**
     * Handle the TicketMessage "uçpdated" event.
     */
    public function updated(TicketMessage $ticketMessage): void
    {
        event(new TicketEvent\Updated($ticketMessage));
    }

    /**
     * Handle the TicketMessage "deleted" event.
     */
    public function deleted(TicketMessage $ticketMessage): void
    {
        event(new TicketEvent\Deleted($ticketMessage));
    }
}
