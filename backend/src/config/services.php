<?php

use App\application\ports\api\ServiceUserInterface;
use App\application\ports\api\ServiceOutilInterface;
use App\application\ports\api\ServiceCategoryInterface;
use App\application\ports\spi\UserRepositoryInterface;
use App\application\ports\spi\OutilRepositoryInterface;
use App\application\ports\spi\CategoryRepositoryInterface;
use App\application\ports\spi\ItemRepositoryInterface;

use App\application\services\ServiceUser;
use App\application\services\ServiceOutil;
use App\application\services\ServiceCategory;

use App\application\usecases\AuthenticateUserUseCase;
use App\application\usecases\CreateUserUseCase;
use App\application\usecases\FindUserByIdUseCase;
use App\application\usecases\ListOutilsUseCase;
use App\application\usecases\GetOutilUseCase;
use App\application\usecases\ListCategoriesUseCase;

use App\infrastructure\repositories\PDOUserRepository;
use App\infrastructure\repositories\PDOOutilRepository;
use App\infrastructure\repositories\PDOCategoryRepository;
use App\infrastructure\repositories\PDOItemRepository;

return [
    // Repositories
    UserRepositoryInterface::class => function ($container) {
        return new PDOUserRepository($container->get(PDO::class));
    },
    
    OutilRepositoryInterface::class => function ($container) {
        return new PDOOutilRepository($container->get(PDO::class));
    },
    
    CategoryRepositoryInterface::class => function ($container) {
        return new PDOCategoryRepository($container->get(PDO::class));
    },
    
    ItemRepositoryInterface::class => function ($container) {
        return new PDOItemRepository($container->get(PDO::class));
    },

    // Use Cases
    AuthenticateUserUseCase::class => function ($container) {
        return new AuthenticateUserUseCase($container->get(UserRepositoryInterface::class));
    },
    
    CreateUserUseCase::class => function ($container) {
        return new CreateUserUseCase($container->get(UserRepositoryInterface::class));
    },

    FindUserByIdUseCase::class => function ($container) {
        return new FindUserByIdUseCase($container->get(UserRepositoryInterface::class));
    },
    
    ListOutilsUseCase::class => function ($container) {
        return new ListOutilsUseCase(
            $container->get(OutilRepositoryInterface::class),
            $container->get(ItemRepositoryInterface::class)
        );
    },
    
    GetOutilUseCase::class => function ($container) {
        return new GetOutilUseCase(
            $container->get(OutilRepositoryInterface::class),
            $container->get(ItemRepositoryInterface::class)
        );
    },
    
    ListCategoriesUseCase::class => function ($container) {
        return new ListCategoriesUseCase($container->get(CategoryRepositoryInterface::class));
    },

    // Services
    ServiceUserInterface::class => function ($container) {
        return new ServiceUser(
            $container->get(AuthenticateUserUseCase::class),
            $container->get(CreateUserUseCase::class),
            $container->get(FindUserByIdUseCase::class)
        );
    },
    
    ServiceOutilInterface::class => function ($container) {
        return new ServiceOutil(
            $container->get(ListOutilsUseCase::class),
            $container->get(GetOutilUseCase::class)
        );
    },
    
    ServiceCategoryInterface::class => function ($container) {
        return new ServiceCategory($container->get(ListCategoriesUseCase::class));
    },

    \App\actions\CreateReservationAction::class => function ($container) {
        return new \App\actions\CreateReservationAction($container->get(ItemRepositoryInterface::class));
    },

    // PDO Connection
    PDO::class => function ($container) {
        $settings = $container->get('settings');
        $db = $settings['db'];
        
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8mb4";
        return new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    },
];
