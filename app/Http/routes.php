<?php

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function() {
    Route::post('{event_id}/check_in/search', [
                'as'   => 'postCheckInSearch',
                'uses' => 'EventCheckInController@postCheckInSearch',
            ]);
            Route::post('{event_id}/check_in/', [
                'as'   => 'postCheckInAttendee',
                'uses' => 'EventCheckInController@postCheckInAttendee',
            ]);
			Route::post('{event_id}/check_in_set/', [
                'as'   => 'postCheckInAttendeeSet',
                'uses' => 'EventCheckInController@postCheckInAttendeeSet',
            ]);
            Route::post('{event_id}/qrcode_check_in', [
                'as'   => 'postQRCodeCheckInAttendee',
                'uses' => 'EventCheckInController@postCheckInAttendeeQr',
            ]);
			Route::post('{event_id}/check_in/searchType', [
                'as'   => 'postCheckInSearchType',
                'uses' => 'EventCheckInController@postCheckInSearchType',
            ]);
			Route::get('{event_id}/check_in/walk_in/', [
                'as'   => 'postWalkInAttendeeSet',
                'uses' => 'EventCheckInController@postWalkInAttendeeSet',
            ]);
			Route::post('{event_id}/dashboard_check_in', [
                'as'   => 'postDashboardCheckIn',
                'uses' => 'EventCheckInController@postDashboardCheckIn',
            ]);
	Route::get('{event_id}/check_in', [
                'as'   => 'showCheckIn',
                'uses' => 'EventCheckInController@showCheckIn',
            ]);
    Route::get('/', [
        'as'   => 'index',
        'uses' => 'IndexController@showIndex',
    ]);
});

