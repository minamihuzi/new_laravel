<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organiser;
use Auth;
use JavaScript;
use View;


class MyBaseController extends Controller
{
    public function __construct()
    {
		$sign_type="";
       
        $data = [            
			'q'          => '',
			'sign_type'	=> $sign_type
        ];
		JavaScript::put([
            'qrcodeCheckInRoute' => route('postQRCodeCheckInAttendee', ['event_id' => 1]),
            'checkInRoute'       => route('postCheckInAttendee', ['event_id' => 1]),
			'checkInRouteSet'       => route('postCheckInAttendeeSet', ['event_id' => 1]),
            'checkInSearchRoute' => route('postCheckInSearch', ['event_id' => 1]),
			'checkInSearchRouteType' => route('postCheckInSearchType', ['event_id' => 1]),
			'walkInRouteSet'       => route('postWalkInAttendeeSet', ['event_id' => 1]),
			'dashboardCheckInRoute'       => route('postDashboardCheckIn', ['event_id' => 1]),
        ]);
		return view('ManageEvent.CheckIn', $data);
    }

    /**
     * Returns data which is required in each view, optionally combined with additional data.
     *
     * @param int $event_id
     * @param array $additional_data
     *
     * @return arrau
     */
    public function getEventViewData($event_id, $additional_data = [])
    {
        $event = Event::scope()->findOrFail($event_id);

        $image_path = $event->organiser->full_logo_path;
        if ($event->images->first() != null) {
            $image_path = $event->images()->first()->image_path;
        }

        return array_merge([
            'event'      => $event,
            'questions'  => $event->questions()->get(),
            'image_path' => $image_path,
        ], $additional_data);
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
}
