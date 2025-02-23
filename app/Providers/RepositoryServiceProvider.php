<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Contracts\InformationRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\InformationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    // Laravel reads $bindings automatically and autowires each repo's model constructor.
    // ponytail: 244 lines of Log audit-trail + binding "validation" deleted — bind() needs none of it.
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        BookingRepositoryInterface::class => BookingRepository::class,
        InformationRepositoryInterface::class => InformationRepository::class,
    ];
}
