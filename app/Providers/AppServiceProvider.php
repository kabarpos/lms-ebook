<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\SmtpSetting;
use App\Observers\TransactionObserver;
use App\Observers\QueryOptimizationObserver;
use App\Repositories\CourseRepository;
use App\Repositories\CourseRepositoryInterface;
use App\Repositories\TransactionRepository;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->singleton(CourseRepositoryInterface::class, CourseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Transaction::observe(TransactionObserver::class);
        
        // Boot query optimization observer for database monitoring
        QueryOptimizationObserver::boot();

        // Apply active SMTP configuration to mail config at runtime
        try {
            $smtp = SmtpSetting::getActive();
            if ($smtp && $smtp->isConfigured()) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $smtp->host);
                Config::set('mail.mailers.smtp.port', (int) $smtp->port);
                Config::set('mail.mailers.smtp.username', $smtp->username);
                Config::set('mail.mailers.smtp.password', $smtp->password);
                Config::set('mail.mailers.smtp.encryption', $smtp->encryption ?: 'tls');
                Config::set('mail.from.address', $smtp->from_email);
                Config::set('mail.from.name', $smtp->from_name);
            }
        } catch (\Exception $e) {
            // Silent fail to avoid boot issues; logs handled elsewhere
        }
    }
}
