<?php

use CodeIgniter\Router\RouteCollection;

date_default_timezone_set('Europe/London');

/**
 * @var RouteCollection $routes
 */

$routes->post('/api/home', 'Api::home');
$routes->post('/api/company', 'Api::company');
$routes->post('/api/treatments', 'Api::treatments');
$routes->post('/api/treatment', 'Api::treatment');
$routes->post('/api/sub_treatments', 'Api::sub_treatments');
$routes->post('/api/check_staff', 'Api::check_staff');
$routes->post('/api/available_dates', 'Api_form::available_dates');
$routes->post('/api/book_appointment', 'Api::book_appointment');
$routes->post('/api/book_appointment_from_website', 'Api::book_appointment_from_website');
$routes->post('/api/photos', 'Api::photos');
$routes->post('/api/get_service_price', 'Api::get_service_price');
$routes->post('/api/fetch_slots', 'Api::fetch_slots');
$routes->post('/api/get_cart_items', 'Api::get_cart_items');
$routes->post('/api/sign_in', 'Api::sign_in');
$routes->post('/api/sign_up', 'Api::sign_up');
$routes->post('/api/customer', 'Api::customer');
$routes->post('/api/edit_profile', 'Api::edit_profile');
$routes->post('/api/update_password', 'Api::update_password');
$routes->post('/api/fetch_services', 'Api::fetch_services');
$routes->post('/api/add_to_cart', 'Api::add_to_cart');
$routes->post('/api/remove_from_cart', 'Api::remove_from_cart');
$routes->post('/api/my_appointments', 'Api::my_appointments');
$routes->post('/api/view_appointment', 'Api::view_appointment');
$routes->post('/api/submit_review', 'Api::submit_review');
$routes->post('/api/my_review', 'Api::my_review');
$routes->post('/api/our_reviews', 'Api::our_reviews');
$routes->post('/api/get_total_item_from_cart', 'Api::get_total_item_from_cart');
$routes->post('/api/check_discount', 'Api_other::check_discount');
$routes->post('/api/forgot_password', 'Api::forgot_password');
$routes->post('/api/reset_password', 'Api::reset_password');
$routes->post('/api/send_inquiry', 'Api::send_inquiry');
$routes->post('/api/reset_password', 'Api::reset_password');
$routes->post('/api/check_code_exist', 'Api::check_code_exist');
$routes->post('/api/offers', 'Api_other::offers');
$routes->post('/api/check-staff-time', 'Api_salon::check_staff_time');
$routes->post('/api/submit_consent_form', 'Api_form::submit_consent_form');
$routes->post('/api/fetch_available_slots', 'Api_form::fetch_available_slots');

// Mobile APIs
$routes->post('/mobileapi/sign-in', 'Api_mobile::sign_in');
$routes->post('/mobileapi/forget-password', 'Api_mobile::forget_password');
$routes->post('/mobileapi/reset-password', 'Api_mobile::reset_password');
$routes->post('/mobileapi/logout', 'Api_mobile::logout');

$routes->post('/mobileapi/create-salon', 'Api_salon::create_salon');
$routes->post('/mobileapi/all-services', 'Api_salon::all_services');
$routes->post('/mobileapi/salons', 'Api_salon::salons');
$routes->post('/mobileapi/create-salon', 'Api_salon::create_salon');
$routes->post('/mobileapi/view-salon', 'Api_salon::view_salon');
$routes->post('/mobileapi/update-salon', 'Api_salon::update_salon');

$routes->post('/mobileapi/service-groups', 'Api_salon::service_groups');
$routes->post('/mobileapi/create-service-group', 'Api_salon::create_service_group');
$routes->post('/mobileapi/delete-service-group', 'Api_salon::delete_service_group');
$routes->post('/mobileapi/view-service-group', 'Api_salon::view_service_group');
$routes->post('/mobileapi/update-service-group', 'Api_salon::update_service_group');

$routes->post('/mobileapi/services', 'Api_salon::services');
$routes->post('/mobileapi/create-service', 'Api_salon::create_service');
$routes->post('/mobileapi/delete-service', 'Api_salon::delete_service');
$routes->post('/mobileapi/view-service', 'Api_salon::view_service');
$routes->post('/mobileapi/update-service', 'Api_salon::update_service');
$routes->post('/mobileapi/add-service-price', 'Api_mobile::add_service_price');

$routes->post('/mobileapi/payment-types', 'Api_salon::payment_types');
$routes->post('/mobileapi/create-payment-type', 'Api_salon::create_payment_type');
$routes->post('/mobileapi/delete-payment-type', 'Api_salon::delete_payment_type');
$routes->post('/mobileapi/view-payment-type', 'Api_salon::view_payment_type');
$routes->post('/mobileapi/update-payment-type', 'Api_salon::update_payment_type');

$routes->post('/mobileapi/discount-types', 'Api_mobile::discount_types');
$routes->post('/mobileapi/create-discount-type', 'Api_mobile::create_discount_type');
$routes->post('/mobileapi/delete-discount-type', 'Api_mobile::delete_discount_type');
$routes->post('/mobileapi/view-discount-type', 'Api_mobile::view_discount_type');
$routes->post('/mobileapi/update-discount-type', 'Api_mobile::update_discount_type');

$routes->post('/mobileapi/customers', 'Api_mobile::customers');
$routes->post('/mobileapi/create-customer', 'Api_mobile::create_customer');
$routes->post('/mobileapi/delete-customer', 'Api_mobile::delete_customer');
$routes->post('/mobileapi/view-customer', 'Api_mobile::view_customer');
$routes->post('/mobileapi/update-customer', 'Api_mobile::update_customer');

$routes->post('/mobileapi/photos', 'Api_mobile::photos');
$routes->post('/mobileapi/upload-photo', 'Api_mobile::upload_photo');
$routes->post('/mobileapi/delete-photo', 'Api_mobile::delete_photo');

$routes->post('/mobileapi/discounts', 'Api_mobile::discounts');
$routes->post('/mobileapi/create-discount', 'Api_mobile::create_discount');
$routes->post('/mobileapi/delete-discount', 'Api_mobile::delete_discount');
$routes->post('/mobileapi/view-discount', 'Api_mobile::view_discount');
$routes->post('/mobileapi/update-discount', 'Api_mobile::update_discount');

$routes->post('/mobileapi/staffs', 'Api_appointment::staffs');
$routes->post('/mobileapi/create-staff', 'Api_appointment::create_staff');
$routes->post('/mobileapi/delete-staff', 'Api_appointment::delete_staff');
$routes->post('/mobileapi/view-staff', 'Api_appointment::view_staff');
$routes->post('/mobileapi/update-staff', 'Api_appointment::update_staff');

$routes->post('/mobileapi/roles', 'Api_appointment::positions');
$routes->post('/mobileapi/positions', 'Api_appointment::roles');
$routes->post('/mobileapi/all-discount-types', 'Api_other::all_discount_types');
$routes->post('/mobileapi/all-payment-types', 'Api_other::all_payment_types');

$routes->post('/mobileapi/staff-timing-list', 'Api_appointment::staff_timing_list');
$routes->post('/mobileapi/add-staff-timing', 'Api_appointment::add_staff_timing');
$routes->post('/mobileapi/view-staff-timing', 'Api_appointment::view_staff_timing');
$routes->post('/mobileapi/update-staff-timing', 'Api_appointment::update_staff_timing');
$routes->post('/mobileapi/delete-staff-timing', 'Api_appointment::delete_staff_timing');

$routes->post('/mobileapi/daily-reports', 'Api_appointment::daily_reports');
$routes->post('/mobileapi/appointments', 'Api_other::appointments');
$routes->post('/mobileapi/search-customer', 'Api_appointment::search_customer');
$routes->post('/mobileapi/customer-history', 'Api_appointment::customer_history');
$routes->post('/mobileapi/cancel-appointment', 'Api_appointment::cancel_appointment');
$routes->post('/mobileapi/view-appointment', 'Api_other::view_appointment');
$routes->post('/mobileapi/reviews', 'Api_other::reviews');
$routes->post('/mobileapi/approve-review', 'Api_other::approve_review');
$routes->post('/mobileapi/checkout-appointment', 'Api_other::checkout_appointment');
$routes->post('/mobileapi/add-appointment', 'Api_other::add_appointment');
$routes->post('/mobileapi/edit-appointment', 'Api_other::edit_appointment');
$routes->post('/mobileapi/add-walkin-appointment', 'Api_other::add_walkin_appointment');
$routes->post('/mobileapi/present-staffs', 'Api_other::present_staffs');
$routes->post('/mobileapi/daily-report', 'Api_other::daily_report');
$routes->post('/mobileapi/add-cart', 'Api_other::add_cart');
$routes->post('/mobileapi/view-cart', 'Api_other::view_cart');
$routes->post('/mobileapi/remove-cart', 'Api_other::remove_cart');
$routes->post('/mobileapi/new-appointment', 'Api_other::new_appointment');
$routes->post('/mobileapi/drop-item', 'Api_other::drop_appointment');

$routes->get('/', 'Home::index');
$routes->get('/about-us', 'Home::about_us');
$routes->get('/treatments', 'Home::treatments');
$routes->get('/treatment/(:any)', 'Home::treatment/$1');
$routes->get('/offers', 'Home::offers');
$routes->get('/gallery', 'Home::gallery');
$routes->get('/contact-us', 'Home::contact_us');
$routes->post('/send_inquiry', 'Home::send_inquiry');
$routes->get('/privacy-policy', 'Home::privacy_policy');
$routes->get('/parking-instructions', 'Home::parking_instructions');
$routes->post('/all_sub_services', 'Home::all_sub_services');
$routes->post('/add_service_in_cart', 'Home::add_service_in_cart');
$routes->post('/book_appointment_from_website', 'Home::book_appointment_from_website');
$routes->post('/fetch-slots', 'Home::fetch_slots');

$routes->get('/admin', 'Auth::index');
$routes->post('/check-sign-in', 'Auth::check_sign_in');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/daily-reports', 'Dashboard::daily_reports');
$routes->post('/get-salon-timing', 'Dashboard::get_salon_timing');

$routes->post('/fetch-daily-report', 'Dashboard::fetch_daily_report');
$routes->post('/get-available-staff-time', 'Dashboard::get_available_staff_time');
$routes->post('/set-company-info', 'Dashboard::set_company_info');
$routes->post('/get-customer-info', 'Dashboard::get_customer_info');
$routes->post('/get-customer-info-by-id', 'Dashboard::get_customer_info_by_id');
$routes->get('/appointments', 'Dashboard::appointments');
$routes->post('/today-employees', 'Dashboard::today_employees');
$routes->post('/get-staff-color', 'Dashboard::get_staff_color');
$routes->post('/check-past-appointment', 'Dashboard::check_past_appointment');
$routes->post('/get-sub-services', 'Dashboard::get_sub_services');
$routes->post('/add-to-cart', 'Dashboard::add_to_cart');
$routes->post('/get-cart-items', 'Dashboard::get_cart_items');
$routes->post('/remove-from-cart', 'Dashboard::remove_from_cart');
$routes->post('/add-appointment', 'Dashboard::add_appointment');
$routes->post('/view-appointment', 'Dashboard::view_appointment');
$routes->post('/remove-appointment', 'Dashboard::remove_appointment');
$routes->post('/edit-appointment', 'Dashboard::edit_appointment');
$routes->post('/clear-carts', 'Dashboard::clear_carts');
$routes->post('/checkout-appointment', 'Dashboard::checkout_appointment');
$routes->post('/hide-appointment', 'Dashboard::hide_appointment');
$routes->post('/complete-appointment', 'Dashboard::complete_appointment');
$routes->post('/add-walkin', 'Dashboard::add_walkin');
$routes->post('/close-appointment', 'Dashboard::close_appointment');
$routes->post('/get-customer-history', 'Dashboard::get_customer_history');
$routes->post('/open-walkin', 'Dashboard::open_walkin');
$routes->post('/get-customer-appointments', 'Dashboard::get_customer_appointments');
$routes->post('/drop-appointment', 'Dashboard::drop_appointment');
$routes->resource('staffs');
$routes->resource('staff_timings');
$routes->post('/get-timing-grid', 'Staff_timings::get_timing_grid');
$routes->post('/get-weekly-time-report', 'Staff_timings::get_weekly_time_report');
$routes->post('/remove-staff-timing', 'Staff_timings::remove_staff_timing');
$routes->post('/new-timing', 'Staff_timings::new_timing');
$routes->get('/profile', 'Dashboard::profile');
$routes->get('/reviews', 'Dashboard::reviews');
$routes->get('/remove-review/(:any)', 'Dashboard::remove_review/$1');
$routes->get('/approve-review/(:any)', 'Dashboard::approve_review/$1');
$routes->get('/logout', 'Auth::logout');

$routes->get('/google-req', 'Google::req');
$routes->get('/google', 'Google::res');
$routes->get('/embellish-contacts/(:any)', 'Google::embellish_contacts/$1');
$routes->get('/google-contacts', 'Customers::google');
$routes->get('/calendar', 'Google::calendar');

$routes->resource('service_groups');
$routes->resource('services');
$routes->get('/add-service-price/(:any)', 'Services::add_service_price/$1');
$routes->post('/new-service-price/(:any)', 'Services::new_service_price/$1');
$routes->post('/update-service-order', 'Service_groups::update_service_order');

$routes->resource('customers');
$routes->resource('discount_types');
$routes->resource('payment_types');
$routes->resource('discounts');
$routes->resource('photos');
$routes->resource('companies');
$routes->resource('consent_forms');
$routes->get('/add-more-consent-questions', 'Consent_forms::add_more_row');
$routes->get('/add-more-option', 'Consent_forms::add_more_option');
$routes->post('/customer-consent-form/(:any)', 'Customers::customer_consent_form/$1');
$routes->get('/delete-more-option', 'Consent_forms::delete_more_option');
$routes->get('/delete-more-question', 'Consent_forms::delete_more_question');
$routes->post('/get-customer-consent-forms', 'Customers::get_customer_consent_form');
$routes->get('/customer-consent-form-history/(:any)', 'Customers::customer_consent_form_history/$1');

$routes->get('/import-customer/(:any)/(:any)', 'Henisha::index/$1/$2');
$routes->get('/import-old-data', 'Henisha::import_old_data');
$routes->get('/import-old-cart', 'Henisha::import_old_cart');
$routes->get('/remove-old-booking', 'Henisha::remove_old_booking');
$routes->post('/load-customers', 'Customers::load');

$routes->get('/whatsapp', 'Whatsapp::index');
$routes->get('/get-whatsapp-history', 'Whatsapp::get_whatsapp_history');
$routes->post('/send-whatsapp-message', 'Whatsapp::send_whatsapp_message');
$routes->get('/send-whatsapp', 'Whatsapp::send_whatsapp');
$routes->get('/remove-customer-appointments/(:any)', 'Whatsapp::remove_customer_appointments/$1');
$routes->get('/send-reminder', 'CronController::send_reminder');
$routes->get('/send-whatsapp-reminder', 'CronController::send_whatsapp_reminder');
$routes->get('/get-unread-messages-count', 'Whatsapp::get_unread_messages_count');
$routes->get('/delete-logs', 'CronController::delete_logs');

// $routes->get('/receive-whatsapp-messages', 'Whatsapp::receive_whatsapp_messages');
$routes->match(['get', 'post'], 'receive-whatsapp-messages', 'Whatsapp::receive_whatsapp_messages');
$routes->match(['get', 'post'], 'receive-whatsapp-messages-embellish', 'Whatsapp::receive_whatsapp_messages_embellish');