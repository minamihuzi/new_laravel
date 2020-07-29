<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SignHtml;
use App\Models\Ticket;
use App\Models\CheckinLog;
use App\Models\SignLog;
use App\Models\SignCert;
use App\Models\Organiser;
use App\Jobs\SendAttendeeTicket;
use App\Models\SignTemplate;
use Carbon\Carbon;
use App\Services\Order as OrderService;
use Illuminate\Support\Facades\URL;
use DB;
use Mail;
use Auth;
use Aws\Signature\SignatureTrait;
use Image;
use Illuminate\Http\Request;
use JavaScript;

class EventCheckInController extends MyBaseController
{
    /**
     * Show the check-in page
     *
     * @param $event_id
     * @return \Illuminate\View\View
     */
    public function showCheckIn(Request $request,$event_id)
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
	public function showCheckInItem(Request $request,$event_id)
    {
        $allowed_sorts = ['first_name', 'business_name', 'email', 'has_arrived', 'created_at'];

        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';

        $event = Event::scope()->findOrFail($event_id);
		$attendees = Attendee::scope()->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->where(function ($query) use ($event_id) {
                $query->where('attendees.event_id', '=', $event_id);
            })->where(function ($query) use ($searchQuery) {
                $query->orWhere('attendees.first_name', 'like', $searchQuery . '%')
                    ->orWhere(
                        DB::raw("CONCAT_WS(' ', attendees.first_name, attendees.last_name)"),
                        'like',
                        $searchQuery . '%'
                    )
                    //->orWhere('attendees.email', 'like', $searchQuery . '%')
                    ->orWhere('orders.order_reference', 'like', $searchQuery . '%')
                    ->orWhere('attendees.last_name', 'like', $searchQuery . '%');
            })
            ->select([
                'attendees.id',
                'attendees.first_name',
                'attendees.last_name',
				'attendees.business_name',
				'attendees.second_name',
                'attendees.email',
                'attendees.arrival_time',
                'attendees.reference_index',
                'attendees.has_arrived',
                'tickets.title as ticket',
				'tickets.type as type',
				'tickets.is_hidden as is_hidden',
				'tickets.is_normal as is_normal',
                'orders.order_reference',
				'orders.amount',
                'orders.is_payment_received'
            ])
            ->orderBy($sort_by, $sort_order)
            ->get();	

        $data = [
            'event'     => $event,
            'attendees' => $event->attendees,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
            'q'          => $searchQuery ? $searchQuery : '',
        ];

        return view('ManageEvent.CheckIn', $data);
    }
    public function showQRCodeModal(Request $request, $event_id)
    {
        return view('ManageEvent.Modals.QrcodeCheckIn');
    }

    /**
     * Search attendees
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInSearch(Request $request, $event_id)
    {
        $allowed_sorts = ['attendees.first_name', 'attendees.business_name', 'attendees.email', 'attendees.has_arrived', 'attendees.created_at', 'tickets.type', 'tickets.is_hidden', 'tickets.is_normal'];

        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'attendees.has_arrived');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';

        $attendees = Attendee::scope()->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->where(function ($query) use ($event_id) {
                $query->where('attendees.event_id', '=', $event_id);
            })->where(function ($query) use ($searchQuery) {
                $query->orWhere('attendees.first_name', 'like', $searchQuery . '%')
                    ->orWhere(
                        DB::raw("CONCAT_WS(' ', attendees.first_name, attendees.last_name)"),
                        'like',
                        $searchQuery . '%'
                    )
                    //->orWhere('attendees.email', 'like', $searchQuery . '%')
                    ->orWhere('orders.order_reference', 'like', $searchQuery . '%')
                    ->orWhere('attendees.last_name', 'like', $searchQuery . '%');
            })
            ->select([
                'attendees.id',
                'attendees.first_name',
                'attendees.last_name',
				'attendees.business_name',
				'attendees.second_name',
				'attendees.second_last_name',
                'attendees.email',
                'attendees.arrival_time',
                'attendees.reference_index',
                'attendees.has_arrived',
				'attendees.photo_path',
                'tickets.title as ticket',
				'tickets.type as type',
				'tickets.is_hidden as is_hidden',
				'tickets.is_normal as is_normal',
                'orders.order_reference',
				'orders.amount',
                'orders.is_payment_received'
            ])
           ->orderBy($sort_by, $sort_order)
            ->get();

        return response()->json($attendees);
    }
	public function postCheckInSearchType(Request $request, $event_id)
    {
        $searchQuery = $request->get('q');
		//$searchQueryType1 = $request->get('type1');
		$searchQueryType2 = $request->get('type2');
		$searchQueryType3 = $request->get('type3');
		$searchQueryType4 = $request->get('type4');		
        return response()->json([
            'status'  => 'success',
            'message' => "message_successfully_sent"
        ]);
    }
    /**
     * Check in/out an attendee
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendee(Request $request, $event_id)
    {
        $attendee_id = $request->get('attendee_id');
        $checking = $request->get('checking');
		
        if($attendee_id==0){
			$attendee_id = $request->get('attendee_id');
			$eventId = $request->get('eventId');
			$checking = $request->get('checking');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$user = Auth::user();
			
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 5;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 0;    
			$order->paid_type = $request->get('pay_walk');			
            $order->save();
			
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "Walkin Ticket";
			$ticket->quantity_available = null;
			$date = date('Y-m-d H:i');
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "This is a walkin ticket.";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 0;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
		}else if($attendee_id==-1){
			$checking = $request->get('checking');
			$eventId = $request->get('eventId');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$user = Auth::user();
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 5;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 0;
			$order->paid_type = $request->get('pay_walk');			            
            $order->save();
			$date = date('Y-m-d H:i');
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "Walkin Ticket";
			$ticket->quantity_available = null;
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "This is a walkin ticket.";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 0;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
			
			$this->dispatch(new SendAttendeeTicket($attendee));

			return response()->json([
				'status'  => 'success',
				'message' => trans("Controllers.ticket_successfully_resent"),
			]);
			
		}else if($attendee_id==-2){
			$passcode = $request->get('passcode');
			$user = Auth::user();
			if($user->pass_code!=$passcode){
				return response()->json([
					'status'  => 'error',
					'message' => trans("basic.invalid_pass_code")
				]);
			}
			$checking = $request->get('checking');
			$eventId = $request->get('eventId');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 1;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 1;   

			$event = Event::findOrFail($eventId);
			// Calculating grand total including tax
            $orderService = new OrderService($attendee_amount, 0, $event);
            $orderService->calculateFinalCosts();

            $order->taxamt = $orderService->getTaxAmount();
            
			
			/*
             * Update the event sales volume
             */
            $event->increment('sales_volume', $orderService->getGrandTotal());
            $event->increment('organiser_fees_volume', $order->organiser_booking_fee);

            /*
             * Update the event stats
             */
            $event_stats = EventStats::updateOrCreate([
                'event_id' => $event_id,
                'date'     => DB::raw('CURRENT_DATE'),
            ]);
            $event_stats->increment('tickets_sold', 1);
            $event_stats->increment('sales_volume', $order->amount);
            $event_stats->increment('organiser_fees_volume', $order->organiser_booking_fee);
            

            /*
             * Add the attendees
             */
           
			
			$order->paid_type = $request->get('pay_walk');			
            $order->save();
			
			$orderItem = new OrderItem();
			$orderItem->title = "walkin Ticket";
			$orderItem->quantity = 1;
			$orderItem->order_id = $order->id;
			$orderItem->unit_price = $attendee_amount;
			$orderItem->unit_booking_fee = 0;
			$orderItem->save();
			
			$date = date('Y-m-d H:i');
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "walkin Ticket";
			$ticket->quantity_available = null;
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "walkin ticket description";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 1;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
			
			
            
			
			return response()->json([
				'status'  => 'success',
				'message' => trans("Controllers.ticket_successfully_resent"),
			]);
			
		}else{
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			//$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			
			$attendee = Attendee::scope()->find($attendee_id);
			
			if ($checking == 'received'){				
				$passcode = $request->get('passcode');
				$user = Auth::user();
				if($user->pass_code!=$passcode){
					return response()->json([
						'status'  => 'error',
						'message' => trans("basic.invalid_pass_code")
					]);
				}
				$attendee->first_name = $attendee_first_name;
				$attendee->last_name = $attendee_last_name;
				$attendee->second_name = $attendee_second_name;
				$attendee->second_last_name = $attendee_second_last_name;
				$attendee->business_name = $attendee_business_name;
				$attendee->email = $attendee_email;
				$attendee->checkin_at = $attendee->checkin_at." ".Carbon::now();
				//$attendee->checkout_at = Carbon::now();
				$attendee->arrival_time = Carbon::now();
				$attendee->has_arrived = 1;
				$order = Order::scope()->find($attendee->order_id);
				$order->order_status_id = 1;
				$order->is_payment_received=1;
				
				
				$event = Event::findOrFail($attendee->event_id);
				// Calculating grand total including tax
				$orderService = new OrderService($order->amount, 0, $event);
				$orderService->calculateFinalCosts();

				$order->taxamt = $orderService->getTaxAmount();
				
				
				/*
				 * Update the event sales volume
				 */
				$event->increment('sales_volume', $orderService->getGrandTotal());
				$event->increment('organiser_fees_volume', $order->organiser_booking_fee);

				/*
				 * Update the event stats
				 */
				$event_stats = EventStats::updateOrCreate([
					'event_id' => $event_id,
					'date'     => DB::raw('CURRENT_DATE'),
				]);
				$event_stats->increment('tickets_sold', 1);
				$event_stats->increment('sales_volume', $order->amount);
				$event_stats->increment('organiser_fees_volume', $order->organiser_booking_fee);
				

				/*
				 * Add the attendees
				 */
			   
				
				
				$order->save();
				
				$orderItem = new OrderItem();
				$orderItem->title = "walkin Ticket";
				$orderItem->quantity = 1;
				$orderItem->order_id = $order->id;
				$orderItem->unit_price = $order->amount;
				$orderItem->unit_booking_fee = 0;
				$orderItem->save();
				
				
			}else{
			

				/*
				 * Ugh
				 */
				if ((($checking == 'in') && ($attendee->has_arrived == 1)) || (($checking == 'out') && ($attendee->has_arrived == 0))) {
					return response()->json([
						'status'  => 'error',
						'message' => 'Attendee Already Checked ' . (($checking == 'in') ? 'In (at ' . $attendee->arrival_time->format('H:i A, F j') . ')' : 'Out') . '!',
						'checked' => $checking,
						'id'      => $attendee->id,
					]);
				}
				//$checkin_log = CheckinLog::scope()->find($attendee_id);
				$checkin_log = new CheckinLog();
				$checkin_log->order_id = $attendee->order_id;
				$checkin_log->event_id = $attendee->event_id;
				$checkin_log->ticket_id = $attendee->ticket_id;
				$checkin_log->attendee_id = $attendee->id;
				
				if($attendee->has_arrived == 0){
					$attendee->has_arrived = ($checking == 'in') ? 1 : 0;
				}
				if($checking == 'out' && $attendee->has_arrived==2){
					$attendee->has_arrived = 1;
					$checking = 'in';					
				}else{
					$attendee->has_arrived = ($checking == 'out') ? 2 : 1;				
				}
				if($attendee->has_arrived == 0){
					$attendee->clear_checkin_at = $attendee->clear_checkin_at." ".Carbon::now();
					$checkin_log->clear_checkin_at = Carbon::now();
					$attendee->period_in =  0;
				}else if($attendee->has_arrived == 1){
					$attendee->checkin_at = $attendee->checkin_at." ".Carbon::now();
					$checkin_log->checkin_at = Carbon::now();
				}else if($attendee->has_arrived == 2){
					$attendee->checkout_at = $attendee->checkout_at." ".Carbon::now();
					$checkin_log->checkout_at = Carbon::now();
										
					$checkin_log_prev = CheckinLog::where('event_id', '=', $attendee->event_id)
										->where('order_id', '=', $attendee->order_id)
										->where('ticket_id', '=', $attendee->ticket_id)
										->where('attendee_id', '=', $attendee->id)
										->where('checkin_at', '!=', null)
										->select('*')
										->orderBy('id', 'desc')
										->get();
					$i_prev = 0;
					$checkin_time = "";
					foreach ($checkin_log_prev as $checkin_log_prev_item) {
						if($i_prev==0){
							$checkin_time = $checkin_log_prev_item['checkin_at'];
							$diff = strtotime($checkin_log->checkout_at) - strtotime($checkin_time);
							$checkin_log->period_check = $diff;
							if($attendee->period_in ==null){
								$attendee->period_in =$diff;
							}else{
								$p_in = $attendee->period_in;
								$p_in = $p_in+1.0*$diff;
								$attendee->period_in =  $p_in;
							}
							$i_prev = $i_prev + 1;
						}else{
							break;
						}
					}
					if($checkin_time!=""){
						$checkin_log->checkin_at = $checkin_time;
					}			
					
				}
				$checkin_log->save();
				$attendee->arrival_time = Carbon::now();
			}
			$attendee->save();
		}
        return response()->json([
            'status'  => 'success',
            'checked' => $checking,
            'message' =>  (($checking == 'in') ? trans("Controllers.attendee_successfully_checked_in") : trans("Controllers.attendee_successfully_checked_out")),
            'id'      => $attendee->id,
        ]);
    }
	public function postCheckInAttendeeSign(Request $request, $event_id)
    {
        $attendee_id = $request->get('attendee_id');
        $checking = $request->get('checking');
		$sign_log = new SignLog();
		$signature_val1 = $request->get('signature_val1');
		$signature_val2 = $request->get('signature_val2');
		$sign_log->sign_content = $signature_val1;
		$sign_log->sign_content_parent = $signature_val2;
		$sign_log->sign_id = $request->get('sign_id');
        if($attendee_id==0){
			$attendee_id = $request->get('attendee_id');
			$eventId = $request->get('eventId');
			$checking = $request->get('checking');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$user = Auth::user();
			
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 5;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 0;    
			$order->paid_type = $request->get('pay_walk');			
            $order->save();
			
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "Walkin Ticket";
			$ticket->quantity_available = null;
			$date = date('Y-m-d H:i');
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "This is a walkin ticket.";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 0;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
		}else if($attendee_id==-1){
			$checking = $request->get('checking');
			$eventId = $request->get('eventId');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$user = Auth::user();
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 5;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 0;
			$order->paid_type = $request->get('pay_walk');			            
            $order->save();
			$date = date('Y-m-d H:i');
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "Walkin Ticket";
			$ticket->quantity_available = null;
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "This is a walkin ticket.";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 0;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
			
			$this->dispatch(new SendAttendeeTicket($attendee));

			return redirect()->action(
				'EventCheckInController@showCheckIn', ['event_id' => $event_id]
			);
			
		}else if($attendee_id==-2){
			$passcode = $request->get('passcode');
			$user = Auth::user();
			if($user->pass_code!=$passcode){
				return response()->json([
					'status'  => 'error',
					'message' => trans("basic.invalid_pass_code")
				]);
			}
			$checking = $request->get('checking');
			$eventId = $request->get('eventId');
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			$attendee_type = $request->get('ticket_type');
			$attendee_profile_type = $request->get('profile_type');
			$order = new Order();
            
            /*
             * Create the order
             */
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = 1;
            $order->amount = $attendee_amount;
            $order->booking_fee = 0.00;
            $order->organiser_booking_fee = 0.00;
            $order->discount = 0.00;
            $order->account_id = $user->account->id;
            $order->event_id = $eventId;
            $order->is_payment_received = 1;   

			$event = Event::findOrFail($eventId);
			// Calculating grand total including tax
            $orderService = new OrderService($attendee_amount, 0, $event);
            $orderService->calculateFinalCosts();

            $order->taxamt = $orderService->getTaxAmount();
            
			
			/*
             * Update the event sales volume
             */
            $event->increment('sales_volume', $orderService->getGrandTotal());
            $event->increment('organiser_fees_volume', $order->organiser_booking_fee);

            /*
             * Update the event stats
             */
            $event_stats = EventStats::updateOrCreate([
                'event_id' => $event_id,
                'date'     => DB::raw('CURRENT_DATE'),
            ]);
            $event_stats->increment('tickets_sold', 1);
            $event_stats->increment('sales_volume', $order->amount);
            $event_stats->increment('organiser_fees_volume', $order->organiser_booking_fee);
            

            /*
             * Add the attendees
             */
           
			
			$order->paid_type = $request->get('pay_walk');			
            $order->save();
			
			$orderItem = new OrderItem();
			$orderItem->title = "walkin Ticket";
			$orderItem->quantity = 1;
			$orderItem->order_id = $order->id;
			$orderItem->unit_price = $attendee_amount;
			$orderItem->unit_booking_fee = 0;
			$orderItem->save();
			
			$date = date('Y-m-d H:i');
			$ticket = Ticket::createNew();
			$ticket->event_id = $eventId;
			$ticket->title = "walkin Ticket";
			$ticket->quantity_available = null;
			$ticket->start_sale_date = $date;
			$ticket->end_sale_date = $date;
			$ticket->price = $attendee_amount;
			$ticket->sales_volume = $attendee_amount;
			$ticket->quantity_available = 1;
			$ticket->quantity_sold = 1;
			$ticket->min_per_person = 1;
			$ticket->max_per_person = 1;
			$ticket->description = "walkin ticket description";
			$ticket->is_hidden = 0;
			$ticket->is_normal = $attendee_type;
			$ticket->is_free_n = 0;
			$ticket->is_suggested_donation = 0;
			$ticket->type = $attendee_profile_type;
			$ticket->note = "walkin note";
			$ticket->save();
			
			$attendee = new Attendee();
			$attendee->order_id = $order->id;
			$attendee->ticket_id = $ticket->id;
			$attendee->account_id = $user->account_id;
			$attendee->event_id = $eventId;
			$attendee->has_arrived = 1;
			$attendee->first_name = $attendee_first_name;
			$attendee->last_name = $attendee_last_name;
			$attendee->second_name = $attendee_second_name;
			$attendee->second_last_name = $attendee_second_last_name;
			$attendee->business_name = $attendee_business_name;
			$attendee->email = $attendee_email;
			$attendee->arrival_time = Carbon::now();
			$attendee->reference_index = 1;
            $attendee->save();
			
			return redirect()->action(
				'EventCheckInController@showCheckIn', ['event_id' => $event_id]
			);
			
		}else{
			$attendee_first_name = $request->get('attendee_first_name');
			$attendee_last_name = $request->get('attendee_last_name');
			$attendee_second_name = $request->get('attendee_second_name');
			$attendee_second_last_name = $request->get('attendee_second_last_name');
			$attendee_email = $request->get('attendee_email');
			//$attendee_amount = $request->get('attendee_amount');
			$attendee_business_name = $request->get('attendee_business_name');
			
			$attendee = Attendee::scope()->find($attendee_id);
			$event = Event::findOrFail($attendee->event_id);
			$sign_log->attendee_id = $attendee_id;
			$sign_log->organiser_id = $event->organiser_id;
			if ($checking == 'received'){				
				$passcode = $request->get('passcode');
				$user = Auth::user();
				if($user->pass_code!=$passcode){
					return response()->json([
						'status'  => 'error',
						'message' => trans("basic.invalid_pass_code")
					]);
				}
				$attendee->first_name = $attendee_first_name;
				$attendee->last_name = $attendee_last_name;
				$attendee->second_name = $attendee_second_name;
				$attendee->second_last_name = $attendee_second_last_name;
				$attendee->business_name = $attendee_business_name;
				$attendee->email = $attendee_email;
				$attendee->checkin_at = $attendee->checkin_at." ".Carbon::now();
				//$attendee->checkout_at = Carbon::now();
				$attendee->arrival_time = Carbon::now();
				$attendee->has_arrived = 1;
				$order = Order::scope()->find($attendee->order_id);
				$order->order_status_id = 1;
				$order->is_payment_received=1;
				
				
				$event = Event::findOrFail($attendee->event_id);
				// Calculating grand total including tax
				$orderService = new OrderService($order->amount, 0, $event);
				$orderService->calculateFinalCosts();

				$order->taxamt = $orderService->getTaxAmount();
				
				
				/*
				 * Update the event sales volume
				 */
				$event->increment('sales_volume', $orderService->getGrandTotal());
				$event->increment('organiser_fees_volume', $order->organiser_booking_fee);

				/*
				 * Update the event stats
				 */
				$event_stats = EventStats::updateOrCreate([
					'event_id' => $event_id,
					'date'     => DB::raw('CURRENT_DATE'),
				]);
				$event_stats->increment('tickets_sold', 1);
				$event_stats->increment('sales_volume', $order->amount);
				$event_stats->increment('organiser_fees_volume', $order->organiser_booking_fee);
				

				/*
				 * Add the attendees
				 */
			   
				
				
				$order->save();
				
				$orderItem = new OrderItem();
				$orderItem->title = "walkin Ticket";
				$orderItem->quantity = 1;
				$orderItem->order_id = $order->id;
				$orderItem->unit_price = $order->amount;
				$orderItem->unit_booking_fee = 0;
				$orderItem->save();
				
				
			}else{
			

				/*
				 * Ugh
				 */
				if ((($checking == 'in') && ($attendee->has_arrived == 1)) || (($checking == 'out') && ($attendee->has_arrived == 0))) {
					return response()->json([
						'status'  => 'error',
						'message' => 'Attendee Already Checked ' . (($checking == 'in') ? 'In (at ' . $attendee->arrival_time->format('H:i A, F j') . ')' : 'Out') . '!',
						'checked' => $checking,
						'id'      => $attendee->id,
					]);
				}
				//$checkin_log = CheckinLog::scope()->find($attendee_id);
				$checkin_log = new CheckinLog();
				$checkin_log->order_id = $attendee->order_id;
				$checkin_log->event_id = $attendee->event_id;
				$checkin_log->ticket_id = $attendee->ticket_id;
				$checkin_log->attendee_id = $attendee->id;
				$event = Event::findOrFail($attendee->event_id);
				$sign_log->organiser_id = $event->organiser_id;
				$sign_log->order_id = $attendee->order_id;
				$sign_log->event_id = $attendee->event_id;
				$sign_log->ticket_id = $attendee->ticket_id;
				if($attendee->has_arrived == 0){
					$attendee->has_arrived = ($checking == 'in') ? 1 : 0;
				}
				if($checking == 'out' && $attendee->has_arrived==2){
					$attendee->has_arrived = 1;
					$checking = 'in';				
					
				}else{
					$attendee->has_arrived = ($checking == 'out') ? 2 : 1;										
				}
				
				if($attendee->has_arrived == 0){
					$attendee->clear_checkin_at = $attendee->clear_checkin_at." ".Carbon::now();
					$checkin_log->clear_checkin_at = Carbon::now();
					$sign_log->clear_checkin_at = Carbon::now();
					$attendee->period_in =  0;
				}else if($attendee->has_arrived == 1){
					$attendee->checkin_at = $attendee->checkin_at." ".Carbon::now();
					$checkin_log->checkin_at = Carbon::now();
					$sign_log->in_checkin_at = Carbon::now();					
				}else if($attendee->has_arrived == 2){
					$attendee->checkout_at = $attendee->checkout_at." ".Carbon::now();
					$checkin_log->checkout_at = Carbon::now();
					$sign_log->checkout_at = Carbon::now();					
					$checkin_log_prev = CheckinLog::where('event_id', '=', $attendee->event_id)
										->where('order_id', '=', $attendee->order_id)
										->where('ticket_id', '=', $attendee->ticket_id)
										->where('attendee_id', '=', $attendee->id)
										->where('checkin_at', '!=', null)
										->select('*')
										->orderBy('id', 'desc')
										->get();
					$i_prev = 0;
					$checkin_time = "";
					foreach ($checkin_log_prev as $checkin_log_prev_item) {
						if($i_prev==0){
							$checkin_time = $checkin_log_prev_item['checkin_at'];
							$diff = strtotime($checkin_log->checkout_at) - strtotime($checkin_time);
							$checkin_log->period_check = $diff;
							if($attendee->period_in ==null){
								$attendee->period_in =$diff;
							}else{
								$p_in = $attendee->period_in;
								$p_in = $p_in+1.0*$diff;
								$attendee->period_in =  $p_in;
							}
							$i_prev = $i_prev + 1;
						}else{
							break;
						}
					}
					if($checkin_time!=""){
						$checkin_log->checkin_at = $checkin_time;
						//$sign_log->checkin_at = $checkin_time;
					}
				}
				$checkin_log->save();
				$sign_log->save();
				$attendee->arrival_time = Carbon::now();
			}
			$attendee->save();
		}
        return redirect()->action(
			'EventCheckInController@showCheckIn', ['event_id' => $event_id]
		);
    }
	/**
     * Show the 'Import Attendee' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function showCheckinLogModal(Request $request, $attendee_id)
    {
        $sort_by = "id";
		$sort_order = "desc";
		$checkinLog = CheckinLog::where('attendee_id', '=', $attendee_id)
										->where('period_check', '>', 0.0)
										->select('*')
										->orderBy($sort_by, $sort_order)
										->get();
        
		return view('ManageEvent.Modals.CheckinLog', [
			'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
			'q'          =>  '',
            'checkinLog'   => $checkinLog
        ]);
    }
	public function postCheckInAttendeeSet(Request $request)
    {
        $pass_require = $request->get('pass_require');
		if(isset($pass_require) && $pass_require = "yes"){
			$passcode = $request->get('passcode');
			$user = Auth::user();
			if($user->pass_code!=$passcode){
				return response()->json([
					'status'  => 'error',
					'message' => trans("basic.invalid_pass_code")
				]);
			}
		}
		$attendee_id = $request->get('attendee_id');
        $checking = $request->get('checking');
		$attendee_first_name = $request->get('attendee_first_name');
		$attendee_last_name = $request->get('attendee_last_name');
		$attendee_email = $request->get('attendee_email');
		$attendee_second_name = $request->get('attendee_second_name');
		$attendee_second_last_name = $request->get('attendee_second_last_name');
		$attendee_business_name = $request->get('attendee_business_name');

        $attendee = Attendee::scope()->find($attendee_id);

        //$attendee->has_arrived = ($checking == 'out') ? 1 : 0;
		$attendee->has_arrived = 0;
		$attendee->first_name = $attendee_first_name;
		$attendee->last_name = $attendee_last_name;
		$attendee->second_name = $attendee_second_name;
		$attendee->second_last_name = $attendee_second_last_name;
		$attendee->business_name = $attendee_business_name;
		$attendee->email = $attendee_email;
        $attendee->arrival_time = Carbon::now();
        $checkin_log = new CheckinLog();
		$checkin_log->order_id = $attendee->order_id;
		$checkin_log->event_id = $attendee->event_id;
		$checkin_log->ticket_id = $attendee->ticket_id;
		$checkin_log->attendee_id = $attendee->id;
		if($attendee->has_arrived == 0){
			//$attendee->clear_checkin_at = Carbon::now();
			$attendee->period_in =  0;
			$attendee->clear_checkin_at = $attendee->clear_checkin_at." ".Carbon::now();
			$checkin_log->clear_checkin_at = Carbon::now();
			$checkin_log->period_check=0.000001;
		}else if($attendee->has_arrived == 1){
			$attendee->checkin_at =$attendee->checkin_at." ".Carbon::now();
			$checkin_log->checkin_at = Carbon::now();
		}
		$checkin_log->save();
		$attendee->save();
        return response()->json([
            'status'  => 'success',
            'checked' => $checking,
            'message' =>  (($checking == 'in') ? trans("Controllers.attendee_successfully_checked_in") : trans("Controllers.attendee_successfully_checked_out")),
            'id'      => $attendee->id,
        ]);
    }
	public function postWalkInAttendeeSet(Request $request, $event_id)
    {
		//request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;       
		$attendee_id = $request->get('attendee_id');
        $checking = $request->get('checking');
		$attendee_first_name = $request->get('attendee_first_name');
		$attendee_last_name = $request->get('attendee_last_name');
		$attendee_email = $request->get('attendee_email');
		$attendee_second_name = $request->get('attendee_second_name');
		$attendee_second_last_name = $request->get('attendee_second_last_name');
		$attendee_business_name = $request->get('attendee_business_name');
			
        $attendee = Attendee::createNew();
		//$attendee->order_id = 1;
		//$attendee->ticket_id = 1;
		//$attendee->event_id = $event_id
		$attendee->has_arrived = 0;
		$attendee->first_name = $attendee_first_name;
		$attendee->last_name = $attendee_last_name;
		$attendee->business_name = $attendee_business_name;
		$attendee->second_last_name = $attendee_second_last_name;
		$attendee->business_name = $attendee_business_name;
		$attendee->email = $attendee_email;
        $attendee->arrival_time = Carbon::now();
        //$attendee->save();

        return response()->json([
            'status'  => 'success',
            'id'      => $attendee->id,
        ]);
    }
    /**
     * Check in an attendee
     *
     * @param $event_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendeeQr($event_id, Request $request)
    {
        $event = Event::scope()->findOrFail($event_id);

        $qrcodeToken = $request->get('attendee_reference');
        $attendee = Attendee::scope()->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where(function ($query) use ($event, $qrcodeToken) {
                $query->where('attendees.event_id', $event->id)
                    ->where('attendees.private_reference_number', $qrcodeToken);
            })->select([
                'attendees.id',
                'attendees.order_id',
                'attendees.first_name',
                'attendees.last_name',
                'attendees.email',
                'attendees.reference_index',
                'attendees.arrival_time',
                'attendees.has_arrived',
                'tickets.title as ticket',
            ])->first();

        if (is_null($attendee)) {
            return response()->json([
                'status'  => 'error',
                'message' => trans("Controllers.invalid_ticket_error")
            ]);
        }

        $relatedAttendesCount = Attendee::where('id', '!=', $attendee->id)
            ->where([
                'order_id'    => $attendee->order_id,
                'has_arrived' => false
            ])->count();

        if ($attendee->has_arrived) {
            return response()->json([
                'status'  => 'error',
                'message' => trans("Controllers.attendee_already_checked_in", ["time"=> $attendee->arrival_time->format(config("attendize.default_datetime_format"))])
            ]);
        }

        Attendee::find($attendee->id)->update(['has_arrived' => true, 'arrival_time' => Carbon::now()]);

        return response()->json([
            'status'  => 'success',
            'name' => $attendee->first_name." ".$attendee->last_name,
            'reference' => $attendee->reference,
            'ticket' => $attendee->ticket
        ]);
    }
	 public function postDashboardCheckIn(Request $request, $event_id)
    {
        $passcode = $request->get('passcode');
		$user = Auth::user();
		if($user->pass_code==$passcode){
			return response()->json([
				'status'  => 'success',
				'message' => trans("basic.valid_pass_code")
			]);
		}else{
			return response()->json([
				'status'  => 'error',
				'message' => trans("basic.invalid_pass_code")
			]);
		}
    }
	/**
     * Upload event image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUploadCheckinPhoto(Request $request)
    {
        if ($request->hasFile('file')) {
			
            $the_file = \File::get($request->file('file')->getRealPath());
            $file_name = 'user_image-' . md5(microtime()) . '.' . strtolower($request->file('file')->getClientOriginalExtension());

            $relative_path_to_file = config('attendize.user_images_path') . '/' . $file_name;
            $full_path_to_file = public_path() . '/' . $relative_path_to_file;

            $img = Image::make($the_file);

            $img->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($full_path_to_file);
			$attendee_id = $request->get('attendee_id');
			$attendee = Attendee::scope()->find($attendee_id);
			$base_url = URL::to('/');
			$attendee->photo_path = $base_url."/".$relative_path_to_file;        
			$attendee->save();
            if (\Storage::put($file_name, $the_file)) {
                return response()->json([
                    'link' => '/' . $relative_path_to_file,
                ]);
            }

            return response()->json([
                'error' => trans("Controllers.image_upload_error"),
            ]);
		}

		if($request->get('file')!==null){
			$img = $request->get('file');
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$imgData = base64_decode($img);

			$attendee_id = $request->get('attendee_id');

			$file_name = 'user_image-' . md5(microtime()) . '.png';

            $relative_path_to_file = config('attendize.user_images_path') . '/' . $file_name;
			$full_path_to_file = public_path() . '/' . $relative_path_to_file;
			
			file_put_contents($full_path_to_file, $imgData);

            // $img = Image::make($full_path_to_file);

            // $img->resize(1000, null, function ($constraint) {
            //     $constraint->aspectRatio();
            //     $constraint->upsize();
            // });

			// $img->save($full_path_to_file);
			
			$attendee = Attendee::scope()->find($attendee_id);
			$base_url = URL::to('/');
			$attendee->photo_path = $base_url."/".$relative_path_to_file;        
			$attendee->save();
            return response()->json([
				'link' => '/' . $relative_path_to_file,
			]);
		}
    }
	public function postDeleteCheckinPhoto(Request $request)
    {
        
		$attendee_id = $request->get('attendee_id');
		$attendee = Attendee::scope()->find($attendee_id);
		$del_file = $attendee->photo_path;
		$attendee->photo_path = "";        
		$attendee->save();
		if($del_file){
			$myArray = explode('/', $del_file);
			$photo_path = $myArray[sizeof($myArray)-1];
			unlink(public_path(). '/user_content/user_photos/' .$photo_path);
			//unlink(public_path(). '/user_content/' .$photo_path);
		}
		return response()->json([
			'link' => '',
		]);		
    }
	public function postCheckInAttendeeCertificate(Request $request, $event_id)
    {
        $event_id = $request->get('event_id');
		$certificate_id = $request->get('certificate_id');
		$sign_cert = SignCert::scope()->find($certificate_id);
		$event = Event::scope()->find($event_id);		
		$event->certificate_id = $certificate_id;
		$event->certificate_title = $sign_cert->title;
		$event->save();
		
	}
	public function postCheckInAttendeeCertificateCeu(Request $request, $event_id)
    {
        $ceu_hr = $request->get('ceu_hr');
		$ceu_total = $request->get('ceu_total');
		$ceu_unit = $request->get('ceu_unit');
		$event = Event::scope()->findOrFail($event_id);	
		$event->ceu_hr = $ceu_hr;		
		$event->ceu_total = $ceu_total;
		$event->ceu_unit = $ceu_unit;
		$event->save();		
	}
	public function showCreateCertificate(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);	
		$attendee_id = $request->get('attendee_id');
		$attendee = Attendee::scope()->find($attendee_id);		
        $organiser_id = $event->organiser_id;
		$organiser = Organiser::scope()->findOrFail($organiser_id);
		try {
            $certificate = SignCert::scope()->findOrFail($event->certificate_id);                
        }catch (\Exception $e) {
            $certificate = NULL;
        }
		$data = [
            'event' => $event,
			'organiser' => $organiser,
            'template' => $certificate,
			'attendee' => $attendee,
			'attendee_id'     => $request->get('attendee_id'),
            'event_id' => $event_id
        ];

        return view('ManageEvent.CheckinCertificate', $data);
    }
	public function showPrintCertificate(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);	
		$attendee_id = $request->get('attendee_id');
		$attendee = Attendee::scope()->find($attendee_id);		
        $organiser_id = $event->organiser_id;
		$organiser = Organiser::scope()->findOrFail($organiser_id);
		try {
            $certificate = SignCert::scope()->findOrFail($event->certificate_id);                
        }catch (\Exception $e) {
            $certificate = NULL;
        }
		$data = [
            'event' => $event,
			'organiser' => $organiser,
            'template' => $certificate,
			'attendee' => $attendee,
			'attendee_id'     => $request->get('attendee_id'),
            'event_id' => $event_id
        ];

        return view('ManageEvent.PrintCertificate', $data);
    }
	public function exportAllCertificates(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);	
        $organiser_id = $event->organiser_id;
		$organiser = Organiser::scope()->findOrFail($organiser_id);
		$attendees = $event->attendees()
			->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
			->join('orders', 'orders.id', '=', 'attendees.order_id')
			->withoutCancelled()
			->orderBy('id', 'asc')
			->select('attendees.*', 'orders.order_reference')
			->get();
		try {
            $certificate = SignCert::scope()->findOrFail($event->certificate_id);                
        }catch (\Exception $e) {
            $certificate = NULL;
        }
		$data = [
            'event' => $event,
			'organiser' => $organiser,
            'template' => $certificate,
			'attendees' => $attendees,
            'event_id' => $event_id
        ];

        return view('ManageEvent.ExportAllCertificates', $data);
    }
	public function postSendCertificate(Request $request, $event_id)
    {
        $attendee_id = $request->get('attendee_id');
		$attendee = Attendee::scope()->findOrFail($attendee_id);
		$event = Event::scope()->findOrFail($event_id);	
		try {
            $certificate = SignCert::scope()->findOrFail($event->certificate_id);                
        }catch (\Exception $e) {
            $certificate = NULL;
        }
        $data = [
            'attendee'        => $attendee,
            'message_content' => $certificate->contents,
            'subject'         => $certificate->title,
            'event'           => $attendee->event,
            'email_logo'      => $attendee->event->organiser->full_logo_path,
        ];

        //@todo move this to the SendAttendeeMessage Job
        Mail::send('Emails.messageReceived', $data, function ($message) use ($attendee, $data) {
            $message->to($attendee->email, $attendee->full_name)
                ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                ->subject($data['subject']);
        });

        /* Could bcc in the above? */
        if ($request->get('send_copy') == '1') {
            Mail::send('Emails.messageReceived', $data, function ($message) use ($attendee, $data) {
                $message->to($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                    ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->subject($data['subject'] . trans("Email.organiser_copy"));
            });
        }

        return response()->json([
            'status'  => 'success',
            'message' => trans("Controllers.message_successfully_sent"),
        ]);
    }
}
