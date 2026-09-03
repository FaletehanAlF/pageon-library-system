<?php

declare(strict_types=1);

$router = new Router();

// Auth
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotForm');
$router->post('/forgot-password', 'AuthController@forgotSubmit');
$router->get('/reset-password', 'AuthController@resetForm');
$router->post('/reset-password', 'AuthController@resetSubmit');

// Dashboard
$router->get('/', 'DashboardController@index');
$router->post('/tips/hide', 'DashboardController@hideTips');

// Books
$router->get('/books', 'BookController@index');
$router->get('/books/create', 'BookController@create');
$router->post('/books', 'BookController@store');
$router->get('/books/{id}', 'BookController@show');
$router->get('/books/{id}/edit', 'BookController@edit');
$router->post('/books/{id}/update', 'BookController@update');
$router->post('/books/{id}/delete', 'BookController@destroy');

// Categories
$router->get('/categories', 'CategoryController@index');
$router->post('/categories', 'CategoryController@store');
$router->post('/categories/{id}/update', 'CategoryController@update');
$router->post('/categories/{id}/delete', 'CategoryController@destroy');

// Borrowings
$router->get('/borrowings', 'BorrowingController@index');
$router->post('/borrowings', 'BorrowingController@store');
$router->post('/borrowings/{id}/return', 'BorrowingController@returnBook');
$router->post('/borrowings/{id}/renew', 'BorrowingController@renew');
$router->get('/my-borrowings', 'BorrowingController@myBorrowings');
$router->get('/riwayat', 'BorrowingController@history');

// Fines / Denda (user lihat tagihan sendiri, admin kelola kas)
$router->get('/fines', 'FineController@index');
$router->post('/fines/{id}/pay', 'FineController@pay');
$router->post('/fines/{id}/waive', 'FineController@waive');

// Activity logs (admin)
$router->get('/logs', 'LogController@index');

// Profile
$router->get('/profile', 'ProfileController@show');
$router->post('/profile', 'ProfileController@update');
$router->post('/profile/password', 'ProfileController@updatePassword');

// Reports (admin)
$router->get('/reports', 'ReportController@index');

// Reservations
$router->get('/reservations', 'ReservationController@index');
$router->post('/reservations', 'ReservationController@store');
$router->post('/reservations/{id}/cancel', 'ReservationController@cancel');
$router->get('/admin/reservations', 'ReservationController@manage');

// Reviews
$router->post('/reviews', 'ReviewController@store');
$router->post('/reviews/{id}/delete', 'ReviewController@destroy');

// Wishlist
$router->get('/wishlist', 'WishlistController@index');
$router->post('/wishlist/toggle', 'WishlistController@toggle');

// Notifications
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications/{id}/read', 'NotificationController@read');
$router->post('/notifications/read-all', 'NotificationController@readAll');
$router->get('/notifications/unread-count', 'NotificationController@unreadCount');

// Announcements (admin)
$router->get('/announcements', 'AnnouncementController@index');
$router->post('/announcements', 'AnnouncementController@store');
$router->post('/announcements/{id}/toggle', 'AnnouncementController@toggle');
$router->post('/announcements/{id}/delete', 'AnnouncementController@destroy');

// Users (admin)
$router->get('/users', 'UserController@index');
$router->post('/users/{id}/role', 'UserController@updateRole');
$router->post('/users/{id}/status', 'UserController@toggleStatus');
$router->post('/users/{id}/reset-password', 'UserController@resetPassword');

// Settings (admin)
$router->get('/settings', 'SettingsController@index');
$router->post('/settings', 'SettingsController@update');

// Bantuan (semua user login)
$router->get('/bantuan', 'HelpController@index');

// Struk peminjaman (printable, pemilik atau admin)
$router->get('/borrowings/{id}/receipt', 'BorrowingController@receipt');

// Kartu anggota (printable)
$router->get('/profile/card', 'ProfileController@card');

// Portal admin rahasia (file gitignore, hanya ada di laptop owner)
$portalRoutes = BASE_PATH . '/routes/admin-portal.php';
if (file_exists($portalRoutes)) {
    require $portalRoutes;
}

$router->dispatch();
